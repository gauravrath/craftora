<?php

namespace App\Http\Requests\Checkout;

use App\Http\Requests\ApiRequest;

class CheckoutPaymentMethodRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:cod,razorpay'],
        ];
    }
}
