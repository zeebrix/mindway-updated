<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Define the maximum number of attempts and the decay time (in minutes)
    protected $maxAttempts = 5;
    protected $decayMinutes = 1;

    // The login field name
    public function username()
    {
        return 'email';
    }

    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
                return redirect()->intended(route('admin.dashboard'));
            }
            if ($user->hasRole('counsellor')) {
                return redirect()->intended(route('counsellor.dashboard'));
            }
            if ($user->hasRole('program')) {
                return redirect()->intended(route('program.dashboard'));
            }

            Auth::logout();
            return redirect()->intended(route('/home'));
        }
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Validate the request data
        $this->validateLogin($request);
        // $recaptchaToken = $request->input('g-recaptcha-response');
        // $score = $this->verifyRecaptchaToken($recaptchaToken);
        // $threshold = 0.7; // Define your acceptable score threshold (e.g., 0.7)

        // if ($score < $threshold) {
        //     // If the score is too low, treat it as a failed login attempt
        //     $this->incrementLoginAttempts($request);

        //     throw ValidationException::withMessages([
        //         $this->username() => ['ReCAPTCHA verification failed. Please try again.'],
        //     ]);
        // }
        // 2. Check for throttling (Lockout)
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // 3. Attempt to log the user in
        if (Auth::attempt($request->only($this->username(), 'password'), $request->boolean('remember'))) {
            // Clear attempts on success
            $this->clearLoginAttempts($request);
            $request->session()->regenerate();

            // Custom redirection logic (from your original code)
            $user = Auth::user();
            if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
                return redirect()->intended(route('admin.dashboard'));
            }
            if ($user->hasRole('counsellor')) {
                return redirect()->intended(route('counsellor.dashboard'));
            }
            if ($user->hasRole('program')) {
                return redirect()->intended(route('program.dashboard'));
            }
            return redirect()->intended(route('home'));
        }

        // 4. If login failed, increment the login attempts counter
        $this->incrementLoginAttempts($request);

        // 5. Send the failed login response
        return $this->sendFailedLoginResponse($request);
    }

    // The logout method remains the same
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // --- Throttling Helper Methods (Manual Implementation) ---

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return RateLimiter::tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts
        );
    }

    protected function incrementLoginAttempts(Request $request)
    {
        RateLimiter::hit(
            $this->throttleKey($request),
            $this->decayMinutes * 60
        );
    }

    protected function clearLoginAttempts(Request $request)
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    protected function fireLockoutEvent(Request $request)
    {
        // You can fire a custom event here if needed
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = RateLimiter::availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            $this->username() => [
                Lang::get('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ],
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    protected function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input($this->username())) . '|' . $request->ip());
    }
    private function verifyRecaptchaToken(string $token): float
    {
        $response = \Illuminate\Support\Facades\Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => env('NOCAPTCHA_SECRET'), // Your secret key
                'response' => $token,
                'remoteip' => request()->ip(),
            ]
        );

        $body = $response->json();
        if (isset($body['success']) && $body['success'] === true) {
            return $body['score'];
        }
        return 0.0;
    }
}
