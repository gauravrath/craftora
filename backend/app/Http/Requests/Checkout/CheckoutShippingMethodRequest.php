<?php

namespace App\Http\Requests\Checkout;

use App\Http\Requests\ApiRequest;

class CheckoutShippingMethodRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'shipping_method' => ['required', 'string', 'in:standard,express'],
        ];
    }
}
