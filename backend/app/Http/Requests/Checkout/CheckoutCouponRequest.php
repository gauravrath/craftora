<?php

namespace App\Http\Requests\Checkout;

use App\Http\Requests\ApiRequest;

class CheckoutCouponRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50'],
        ];
    }
}
