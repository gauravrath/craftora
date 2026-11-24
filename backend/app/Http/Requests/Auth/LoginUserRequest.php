<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;

class LoginUserRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
