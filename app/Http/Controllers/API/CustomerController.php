<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerRegisterRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerRegisterVerificationRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerLoginRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerUpdateProfileRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerForgetPasswordRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerResetPasswordVerificationRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerGetNotifyRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerRegisterByEmailRequest;
use App\Models\User;
use App\Repositories\CustomersRepository;
use App\Services\CustomerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CustomerController extends Controller
{

    protected $customerService;
    protected $repository;
    public function __construct(CustomerService $customerService, CustomersRepository $repository)
    {
        $this->customerService = $customerService;
        $this->repository = $repository;
    }
    public function passwordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->input('email');
        $customer = User::customers()->where('email', $email)->first();
        if (!$customer) {
            return response()->json([
                'code' => 421,
                'status' => 'Error',
                'message' => 'No account found with this email address.'
            ], 421);
        }
        $token = Str::random(64);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => now(),
            ]
        );
        $resetLink = url("/app-reset-password/{$token}?email={$email}");
        Mail::to($email)->send(new \App\Mail\ResetPasswordMail($resetLink));
        return response()->json(['success' => true]);
    }
    public function findMe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $is_resend = isset($request->is_resend) ? $request->is_resend : false;
        if (!$is_resend) {
            $user = User::Customers()->Where('email', $request->email)->first();
            if ($user) {
                return response()->json(['status' => false, 'message' => 'This email is already registered. Please try a different one.'], 400);
            }
        }
        $customer = User::Customers()->where("email", $request->email)->first();
        if ($customer) {
            $otp = random_int(100000, 999999);
            if ($request->verification_after == 'access_code') {
                $customer->verification_code = $otp;
            } else {
                $customer->otp = $otp;
                $customer->otp_expiry = Carbon::now()->addMinutes(10);
            }
            $customer->save();
            try {
                // Send OTP email
                Mail::send('email.otp', ['otp' => $otp], function ($message) use ($customer) {
                    $message->to($customer->email)
                        ->subject('Your OTP Code');
                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'An error occurred while sending the email: ' . $e->getMessage()], 500);
            }
            return response()->json(['status' => true, 'data' => $customer, 'message' => 'OTP sent to the provided email.'], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'No email found. Please try another email or use your access code below.'], 400);
        }
    }
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);
        $type = isset($request->type) ? $request->type : 'by_email';
        if ($type == 'by_email') {
            $customer = User::customers()->where("email", $request->email)->first();
            if ($customer && $customer->otp && Carbon::now()->lt($customer->otp_expiry)) {
                if ($customer->otp === $request->otp) {
                    return response()->json(['status' => true, 'data' => $customer, 'message' => 'OTP verified successfully.'], 200);
                } else {
                    return response()->json(['status' => false, 'message' => 'Invalid OTP.'], 400);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'OTP not found or has expired.'], 400);
            }
        } else if ($type == 'by_code') {
            $customer = User::customers()->where('email', $request->email)->first();
            if ($customer && $customer->verification_code == $request->otp) {
                $apiAuthToken = $this->repository->getUniqueValue(10, 'api_auth_token');
                $customer->verified_at = \Carbon\Carbon::now();
                $customer->status = true;
                $customer->api_auth_token = $apiAuthToken;
                $customer->save();
                $token = $apiAuthToken ?? NULL;
                $useSanctum = request()->header('Use-Sanctum') === 'true';
                if ($useSanctum) {
                    $token = $customer->createToken('auth_token')->plainTextToken;
                }
                $customer = $customer->toArray();
                $customer["bearer_token"] = $token ?? NULL;

                return response()->json([
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'Customer verified successfully.',
                    'data' => [$customer]
                ], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'OTP not found or has expired.'], 400);
            }
        }
    }

    public function register(CustomerRegisterRequest $request)
    {
        return $this->customerService->store($request->all());
    }
    public function registerByEmail(CustomerRegisterByEmailRequest $request)
    {
        return $this->customerService->store($request->all());
    }
    public function verifySignup(CustomerRegisterVerificationRequest $request)
    {
        return $this->customerService->verifySignup($request->all());
    }

    public function login(CustomerLoginRequest $request)
    {
        return $this->customerService->login($request->all());
    }
    public function getNotify(CustomerGetNotifyRequest $request)
    {
        if (isset($request['email']) && !empty($request['email'])) {
            $email_id = $request['email'];

            User::customers()
                ->where('email', $email_id)
                ->update([
                    'notify_time' => $request['notify_time'],
                    'notify_day' => $request['notify_day'],
                ]);
            return response()->json(['code' => 200, 'status' => "success", 'message' => "Get notify time and day updated"]);
        } else {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "incomplete parameters email is required"]);
        }
    }
}
