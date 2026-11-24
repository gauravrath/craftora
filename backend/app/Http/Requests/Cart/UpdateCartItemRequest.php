<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\ApiRequest;

class UpdateCartItemRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'product_variant_id' => ['nullable', 'string', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ];
    }
}
