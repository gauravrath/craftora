<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\ApiRequest;

class AddCartItemRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'string', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'string', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }
}
