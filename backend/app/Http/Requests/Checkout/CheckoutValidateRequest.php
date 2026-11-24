<?php

namespace App\Http\Requests\Checkout;

use App\Http\Requests\ApiRequest;

class CheckoutValidateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'array'],
            'billing_address' => ['nullable', 'array'],
            'use_shipping_address_as_billing' => ['nullable', 'boolean'],
            'shipping_method' => ['required', 'string', 'in:standard,express'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'string', 'in:cod,razorpay'],
        ];
    }
}
