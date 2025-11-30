<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Password; // Import the Password facade
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    protected $redirectTo = '/login';

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request): View
    {
        return view('auth.passwords.reset')->with(
            ['token' => $request->route('token'), 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Here we get the response from the password broker.
        $response = Password::broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the password reset validation rules.
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8', // Added min:8 for security
        ];
    }

    /**
     * Get the password reset validation error messages.
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Get the password reset credentials from the request.
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        );
    }

    /**
     * Reset the given user's password.
     */
    protected function resetPassword($user, $password)
    {
        $user->password = \Hash::make($password);
        $user->save();

        Auth::guard()->login($user);
    }

    /**
     * Get the response for a successful password reset.
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectTo)->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset.
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }
}
