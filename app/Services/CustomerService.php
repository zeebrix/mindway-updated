<?php

namespace App\Services;

use App\Models\CustomerDetail;
use App\Models\ProgramCustomer;
use App\Models\User;
use App\Repositories\CustomersRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerService
{
    protected $brevoService;

    public function __construct(BrevoService $brevoService)
    {
        $this->brevoService = $brevoService;
    }

    public function createCustomer(array $data, User $programUser)
    {
        try {
            $user = ($programUser) ? $programUser : Auth::user();
            $customer = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt('password'),
                'user_type' => 'customer'
            ]);
            CustomerDetail::create([
                'user_id' => $customer->id,
                'program_id' => $user->programDetail->id,
                'level' => $data['level']
            ]);
            ProgramCustomer::create([
                'customer_id' => $customer->id,
                'program_id' => $user->id,
            ]);

            $this->brevoService->createContact($customer, $user);

            if ($data['level'] === 'admin') {
                $this->sendAdminEmail($customer, $user);
            }

            return $customer;
        } catch (\Exception $e) {
            Log::error('Failed to create customer: ' . $e->getMessage());
            throw $e; // Re-throw the exception to be handled by the controller
        }
    }

    protected function sendAdminEmail($customer, $program)
    {
        $recipient = $customer->email;
        $subject = 'You’ve Been Made an Admin for Mindway EAP';
        $template = 'emails.become-admin-member';
        $data = [
            'full_name' => $customer->name ?? '',
            'company_name' => $program->company_name ?? '',
            'access_code' => $program->code ?? ''
        ];
        return;
        // Assuming sendDynamicEmailFromTemplate is a helper function
        // sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
    }
    public function login(array $modelValues = [])
    {
        $useSanctum = request()->header('Use-Sanctum') === 'true';
        $guard = $useSanctum ? 'api_sanctum' : 'api';
        $user = User::Customers()->where('email', $modelValues['email'])->first();
        if ($user) {
            if (!$user->single_program) {
                return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'This account is not setup Correctly.'
                ], 421);
            }
            if ($useSanctum) {
                if (!Hash::check($modelValues['password'], $user->password)) {
                    return response()->json([
                        'code' => 421,
                        'status' => 'Error',
                        'message' => 'Password or email incorrect. If you’re still having trouble, reset your password.'
                    ], 401);
                }
                $token = $user->createToken('auth_token')->plainTextToken;
                $user["bearer_token"] = $token ?? NULL;
                return response()->json([
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'Login successfully.',
                    'data' => $user
                ], 200);
            }
            Auth::guard($guard)->login($user);
            $repo = new CustomersRepository($user);
            $apiAuthToken = $repo->getUniqueValue(10, 'api_auth_token');
            $user->api_auth_token = $apiAuthToken;
            $user->save();
            $user = $user->toArray();
            $user["bearer_token"] = $user["api_auth_token"] ?? NULL;
            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Login successfully.',
                'data' => [$user]
            ], 200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'Password or email incorrect. If you’re still having trouble, reset your password'
        ], 421);
    }
    public function store($request, array $modelValues = [])
    {
        try {
            DB::beginTransaction();
            $pushToBrevo = false;
            $modelValues["verification_code"] = random_int(100000, 999999);
            $modelValues['password'] = Hash::make($modelValues['password']);
            Arr::forget($modelValues, ["password_confirmation"]);

            $customer = User::create($modelValues);
            $customerDetail = CustomerDetail::createorupdate(['user_id' => $customer->id], [
                'program_id' => $modelValues['program_id'],
                'level' => 'member',
            ]);
            if ($modelValues['program_id']) {
                $program = User::ProgramOwners()->where('id', $modelValues['program_id'])->first();
                $customerDetail->max_session = $program->max_session;
                $customerDetail->save();
                $existingRecord = ProgramCustomer::where('program_id', $modelValues['program_id'])
                    ->where('customer_id', $customer->id)
                    ->first();

                if (!$existingRecord) {
                    ProgramCustomer::insert([
                        'program_id' => $modelValues['program_id'],
                        'customer_id' => $customer->id,
                    ]);
                }
            }
            if ($modelValues['register_type'] ?? '' == 'code') {
                try {
                    // Send OTP email
                    $otp = $modelValues["verification_code"];
                    $email = $modelValues["email"];
                    Mail::send('email.otp', ['otp' => $otp], function ($message) use ($email) {
                        $message->to($email)
                            ->subject('Your OTP Code');
                    });
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => 'An error occurred while sending the email: ' . $e->getMessage()], 500);
                }
            }

            $token = $customer["api_auth_token"] ?? NULL;
            $useSanctum = request()->header('Use-Sanctum') === 'true';
            if ($useSanctum) {
                $token = $customer->createToken('auth_token')->plainTextToken;
            }
            $customer["bearer_token"] = $token ?? NULL;
            DB::commit();
            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Customer registered successfully.',
                'data' => $customer
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Exception caught: ' . $th->getMessage(), [
                'exception' => $th
            ]);
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function verifySignup(array $modelValues = [])
    {
        $customer = User::customers()->where('email', $modelValues["email"])->first();

        if ($customer && !$customer->verified_at && $customer->verification_code == $modelValues["verification_code"]) {
            $repo = new CustomersRepository($customer);
            $apiAuthToken = $repo->getUniqueValue(10, 'api_auth_token');
            $customer->verified_at = \Carbon\Carbon::now();
            $customer->status = TRUE;
            $customer->api_auth_token = $apiAuthToken;
            $customer->save();

            $customer = $customer->toArray();
            $customer["bearer_token"] = $customer["api_auth_token"] ?? NULL;
            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Customer verified successfully.',
                'data' => [$customer]
            ], 200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'Phone number or verification code is invalid.'
        ], 421);
    }
}
