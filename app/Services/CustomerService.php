<?php

namespace App\Services;

use App\Http\Resources\UserResource;
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
use App\Support\ApiResponse;
use App\Support\ApiMessages;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class CustomerService
{
    use ApiResponse;
    protected $brevoService;

    public function __construct(BrevoService $brevoService)
    {
        $this->brevoService = $brevoService;
    }
    public function verifyOTP(array $data)
    {
        $type = $data['type'] ?? 'by_email';

        $customer = User::customers()
            ->where('email', $data['email'])
            ->first();

        if (! $customer) {
            return $this->error(ApiMessages::OTP_EXPIRED, 400);
        }

        return match ($type) {
            'by_code'  => $this->verifyByCode($customer, $data['otp']),
            default    => $this->verifyByEmailOtp($customer, $data['otp']),
        };
    }

    protected function verifyByEmailOtp(User $customer, string $otp)
    {
        if (! $customer->otp || Carbon::now()->gte($customer->otp_expiry)) {
            return $this->error(ApiMessages::OTP_EXPIRED, 400);
        }

        if ($customer->otp !== $otp) {
            return $this->error(ApiMessages::INVALID_OTP, 400);
        }

        // Optional: clear OTP after success
        $customer->update([
            'otp' => null,
            'otp_expiry' => null,
        ]);

        return $this->success(ApiMessages::OTP_VERIFIED);
    }

    protected function verifyByCode(User $customer, string $otp)
    {
        if ($customer->verification_code !== $otp) {
            return $this->error(ApiMessages::OTP_EXPIRED, 400);
        }

        $customer->update([
            'verified_at' => now(),
            'status' => true,
            'verification_code' => null,
        ]);

        $token = $customer->createToken('auth_token')->plainTextToken;

        return $this->success(
            ApiMessages::CUSTOMER_VERIFIED,
            [
                'email' => $customer->email,
                'bearer_token' => $token,
            ]
        );
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
    public function login(array $credentials)
    {
        $user = User::customers()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return $this->error(
                ApiMessages::INVALID_CREDENTIALS,
                401
            );
        }

        if (! $user->programs()->exists()) {
            return $this->error(
                ApiMessages::ACCOUNT_NOT_CONFIGURED,
                403
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->bearer_token = $token;

        return $this->success(
            ApiMessages::LOGIN_SUCCESS,
            new UserResource($user)
        );
    }
    public function findMe(array $data)
    {
        $email = $data['email'];
        $isResend = $data['is_resend'] ?? false;
        $verificationAfter = $data['verification_after'] ?? 'otp';

        $customer = User::customers()
            ->where('email', $email)
            ->first();

        // Block duplicate registration (unless resend)
        if (! $isResend && $customer) {
            return $this->error(
                ApiMessages::EMAIL_ALREADY_REGISTERED,
                400
            );
        }

        if (! $customer) {
            return $this->error(
                ApiMessages::EMAIL_NOT_FOUND,
                400
            );
        }

        $otp = random_int(100000, 999999);

        if ($verificationAfter === 'access_code') {
            $customer->otp = $otp;
            $customer->otp_expiry = Carbon::now()->addMinutes(10);
        } else {
            $customer->otp = $otp;
            $customer->otp_expiry = Carbon::now()->addMinutes(10);
        }

        $customer->save();

        try {
            Mail::send('email.otp', ['otp' => $otp], function ($message) use ($customer) {
                $message->to($customer->email)
                    ->subject('Your OTP Code');
            });
        } catch (Exception $e) {
            return $this->error(
                ApiMessages::OTP_EMAIL_FAILED,
                500
            );
        }

        return $this->success(
            ApiMessages::OTP_SENT,
            [
                'email' => $customer->email,
                'expires_in' => 600,
                'customers' => new UserResource($customer)

            ]
        );
    }
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {

            $data['verification_code'] = random_int(100000, 999999);
            $data['password'] = Hash::make($data['password']);
            Arr::forget($data, ['password_confirmation']);

            /** Create customer */
            $customer = User::create($data);

            /** Customer details */
            $detail = CustomerDetail::updateOrCreate(
                ['user_id' => $customer->id],
                [
                    'program_id' => $data['program_id'] ?? null,
                    'level' => 'member',
                ]
            );

            /** Attach program (if any) */
            if (! empty($data['program_id'])) {
                $program = User::programOwners()
                    ->select('id', 'max_session')
                    ->find($data['program_id']);

                if ($program) {
                    $detail->update([
                        'max_session' => $program->max_session,
                    ]);

                    ProgramCustomer::firstOrCreate([
                        'program_id' => $program->id,
                        'customer_id' => $customer->id,
                    ]);
                }
            }

            /** Send verification OTP (code-based registration) */
            if (($data['register_type'] ?? null) === 'code') {
                $this->sendOtpEmail(
                    $customer->email,
                    $data['verification_code']
                );
            }

            /** Sanctum token */
            $token = $customer->createToken('auth_token')->plainTextToken;

            return $this->success(
                ApiMessages::CUSTOMER_REGISTERED,
                [
                    'email' => $customer->email,
                    'bearer_token' => $token,
                ]
            );
        });
    }

    protected function sendOtpEmail(string $email, int $otp): void
    {
        try {
            Mail::send('email.otp', ['otp' => $otp], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Your OTP Code');
            });
        } catch (Throwable $e) {
            throw new \RuntimeException(ApiMessages::OTP_EMAIL_FAILED);
        }
    }
    public function verifySignup(array $data)
    {
        $customer = User::customers()
            ->where('email', $data['email'])
            ->first();

        if (
            ! $customer
            || $customer->verified_at
            || $customer->verification_code !== $data['verification_code']
        ) {
            return $this->error(
                ApiMessages::INVALID_VERIFICATION,
                422
            );
        }

        // Verify customer
        $customer->update([
            'verified_at' => now(),
            'status' => true,
            'verification_code' => null,
        ]);

        // Sanctum token
        $token = $customer->createToken('auth_token')->plainTextToken;

        return $this->success(
            ApiMessages::CUSTOMER_VERIFIED,
            [
                'email' => $customer->email,
                'bearer_token' => $token,
            ]
        );
    }
    public function updateNotify(array $data)
    {
        $updated = User::customers()
            ->where('email', $data['email'])
            ->update([
                'notify_time' => $data['notify_time'],
                'notify_day' => $data['notify_day'],
            ]);

        if (! $updated) {
            return $this->error(ApiMessages::MISSING_EMAIL, 422);
        }

        return $this->success(ApiMessages::NOTIFY_UPDATED);
    }
    public function updateProfile(Request $request)
    {
        $user = $request->user(); // Sanctum authenticated user

        $data = $request->only(['name', 'phone']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('users', 'public');
        }

        if (! $user->update($data)) {
            return $this->error(
                ApiMessages::PROFILE_UPDATE_FAILED,
                422
            );
        }

        return $this->success(
            ApiMessages::PROFILE_UPDATED,
            new UserResource($user->fresh())
        );
    }
    public function passwordReset(array $data)
    {
        $email = $data['email'];

        $customer = User::customers()
            ->where('email', $email)
            ->first();

        if (! $customer) {
            return $this->error(
                ApiMessages::ACCOUNT_NOT_FOUND,
                404
            );
        }

        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        $resetLink = url("/app-reset-password/{$token}?email={$email}");

        try {
            Mail::to($email)
                ->send(new \App\Mail\ResetPasswordMail($resetLink));
        } catch (Throwable $e) {
            return $this->error(
                ApiMessages::PASSWORD_RESET_FAILED,
                500
            );
        }
        return $this->success(
            ApiMessages::PASSWORD_RESET_LINK_SENT
        );
    }
}
