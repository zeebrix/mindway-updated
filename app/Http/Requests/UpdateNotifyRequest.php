<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotifyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'notify_time' => 'required|string',
            'notify_day' => 'required|string',
        ];
    }
}
