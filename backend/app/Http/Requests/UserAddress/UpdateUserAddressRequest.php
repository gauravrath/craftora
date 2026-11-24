<?php

namespace App\Http\Requests\UserAddress;

use App\Http\Requests\ApiRequest;

class UpdateUserAddressRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'label' => ['sometimes', 'string', 'max:100'],
            'full_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'address_line_1' => ['sometimes', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:100'],
            'state' => ['sometimes', 'string', 'max:100'],
            'postal_code' => ['sometimes', 'string', 'max:20'],
            'country' => ['sometimes', 'string', 'max:100'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
