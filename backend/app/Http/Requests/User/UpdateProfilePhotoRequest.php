<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiRequest;

class UpdateProfilePhotoRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
