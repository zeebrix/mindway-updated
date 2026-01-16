<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FindMeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'is_resend' => 'sometimes|boolean',
            'verification_after' => 'sometimes|in:access_code,otp',
        ];
    }
}
