<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class CheckoutService extends BaseService
{
    public const SHIPPING_METHODS = [
        'standard' => ['label' => 'Standard Shipping', 'cost' => 9.99, 'delivery_days' => 5],
        'express' => ['label' => 'Express Shipping', 'cost' => 19.99, 'delivery_days' => 2],
    ];

    public const PAYMENT_METHODS = [
        'cod' => 'Cash on Delivery',
        'razorpay' => 'Razorpay',
    ];

    public function getCheckoutSummary(Request $request): array
    {
        $cart = $this->resolveCart($request);
        $this->assertCartUsable($cart);

        $shippingAddress = $this->resolveShippingAddress($request, $cart);
        $billingAddress = $this->resolveBillingAddress($request, $cart, $shippingAddress);
        $shippingMethod = $this->resolveShippingMethod($request);
        $coupon = $this->resolveCoupon($request, $cart);

        $validated = $this->validateCartForCheckout($cart, $shippingAddress, $billingAddress, $shippingMethod, $coupon);

        return [
            'cart' => $this->serializeCart($cart),
            'items' => $validated['items'],
            'subtotal' => $validated['subtotal'],
            'discount' => $validated['discount'],
            'tax' => $validated['tax'],
            'shipping' => $validated['shipping'],
            'grand_total' => $validated['grand_total'],
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
            'shipping_method' => $shippingMethod,
            'payment_method' => $this->resolvePaymentMethod($request),
            'coupon' => $coupon,
        ];
    }

    public function validateCheckout(Request $request): array
    {
        $cart = $this->resolveCart($request);
        $this->assertCartUsable($cart);

        $shippingAddress = $this->resolveShippingAddress($request, $cart);
        $billingAddress = $this->resolveBillingAddress($request, $cart, $shippingAddress);
        $shippingMethod = $this->resolveShippingMethod($request);
        $coupon = $this->resolveCoupon($request, $cart);

        $validated = $this->validateCartForCheckout($cart, $shippingAddress, $billingAddress, $shippingMethod, $coupon);

        return [
            'is_valid' => true,
            'summary' => [
                'subtotal' => $validated['subtotal'],
                'discount' => $validated['discount'],
                'tax' => $validated['tax'],
                'shipping' => $validated['shipping'],
                'grand_total' => $validated['grand_total'],
            ],
            'shipping_method' => $shippingMethod,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
            'coupon' => $coupon,
        ];
    }

    public function saveAddress(Request $request): array
    {
        $payload = $request->validated();
        $user = $request->user();

        if ($user) {
            $address = $user->addresses()->create($payload);
            return ['address' => $this->normalizeAddress($address)];
        }

        return ['address' => $payload];
    }

    public function updateAddress(Request $request, ?int $addressId = null): array
    {
        $user = $request->user();
        $payload = $request->validated();

        if ($user) {
            $address = $user->addresses()->findOrFail($addressId ?? $payload['id'] ?? null);
            $address->update($payload);
            return ['address' => $this->normalizeAddress($address->fresh())];
        }

        return ['address' => $payload];
    }

    public function selectShippingMethod(Request $request): array
    {
        $method = $this->resolveShippingMethod($request);
        $cart = $this->resolveCart($request);
        $cart->shipping_method = $method['code'];
        $cart->shipping_cost = $method['cost'];
        $cart->save();

        return ['shipping_method' => $method];
    }

    public function applyCoupon(Request $request): array
    {
        $cart = $this->resolveCart($request);
        $code = strtoupper(trim((string) $request->input('code', '')));

        if ($code === '') {
            throw ValidationException::withMessages(['code' => ['A coupon code is required.']]);
        }

        $coupon = Coupon::query()->where('code', $code)->where('status', 'active')->first();
        if (! $coupon) {
            throw ValidationException::withMessages(['code' => ['This coupon code is invalid or expired.']]);
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            throw ValidationException::withMessages(['code' => ['This coupon code is not available yet.']]);
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            throw ValidationException::withMessages(['code' => ['This coupon code has expired.']]);
        }

        $summary = $this->calculateCartTotals($cart, $coupon);
        $cart->coupon_code = $coupon->code;
        $cart->coupon_type = $coupon->type;
        $cart->coupon_value = $coupon->value;
        $cart->discount_amount = $summary['discount'];
        $cart->save();

        return [
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
            ],
            'summary' => $summary,
        ];
    }

    public function clearCoupon(Request $request): array
    {
        $cart = $this->resolveCart($request);
        $cart->coupon_code = null;
        $cart->coupon_type = null;
        $cart->coupon_value = null;
        $cart->discount_amount = 0;
        $cart->save();

        return ['coupon' => null, 'summary' => $this->calculateCartTotals($cart)];
    }

    public function selectPaymentMethod(Request $request): array
    {
        $method = $this->resolvePaymentMethod($request);
        $cart = $this->resolveCart($request);
        $cart->payment_method = $method['code'];
        $cart->save();

        return ['payment_method' => $method];
    }

    public function review(Request $request): array
    {
        $summary = $this->getCheckoutSummary($request);
        $summary['payment_method'] = $this->resolvePaymentMethod($request);
        return $summary;
    }

    protected function resolveCart(Request $request): Cart
    {
        $sessionId = $request->header('X-Cart-Session');
        $user = $request->user();

        if ($user) {
            $cart = $user->carts()->where('status', 'active')->first();
            if ($cart) {
                return $cart->fresh(['items.product', 'items.variant']);
            }
        }

        if ($sessionId) {
            $cart = Cart::query()->where('status', 'active')->where('session_id', $sessionId)->first();
            if ($cart) {
                return $cart->fresh(['items.product', 'items.variant']);
            }
        }

        throw ValidationException::withMessages(['cart' => ['No active cart found for checkout.']]);
    }

    protected function assertCartUsable(Cart $cart): void
    {
        if ($cart->items()->count() === 0) {
            throw ValidationException::withMessages(['cart' => ['Your cart is empty.']]);
        }

        foreach ($cart->items as $item) {
            $product = $item->product; 
            if (! $product || ! $product->is_active) {
                throw ValidationException::withMessages(['cart' => ['One or more products are no longer active.']]);
            }

            if ($item->product_variant_id) {
                $variant = ProductVariant::query()->find($item->product_variant_id);
                if (! $variant || ! $variant->is_active) {
                    throw ValidationException::withMessages(['cart' => ['One or more product variants are no longer valid.']]);
                }
            }
        }
    }

    protected function resolveShippingAddress(Request $request, Cart $cart): array
    {
        $data = $request->input('shipping_address');
        $user = $request->user();

        if (isset($data['id'])) {
            $address = Address::query();
            if ($user) {
                $address = $address->where('user_id', $user->id);
            }
            $address = $address->findOrFail($data['id']);
            return $this->normalizeAddress($address);
        }

        $payload = Arr::only($data ?? [], ['full_name', 'phone', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country']);
        if ($user) {
            $payload['user_id'] = $user->id;
        }
        $this->validateAddressPayload($payload);

        $cart->shipping_address_json = json_encode($payload);
        $cart->save();
        return $payload;
    }

    protected function resolveBillingAddress(Request $request, Cart $cart, array $shippingAddress): array
    {
        $data = $request->input('billing_address');
        $useShipping = (bool) $request->input('use_shipping_address_as_billing', false);
        if ($useShipping) {
            return $shippingAddress;
        }

        if (isset($data['id'])) {
            $user = $request->user();
            $address = Address::query();
            if ($user) {
                $address = $address->where('user_id', $user->id);
            }
            $address = $address->findOrFail($data['id']);
            return $this->normalizeAddress($address);
        }

        $payload = Arr::only($data ?? [], ['full_name', 'phone', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country']);
        $this->validateAddressPayload($payload);

        $cart->billing_address_json = json_encode($payload);
        $cart->save();
        return $payload;
    }

    protected function resolveShippingMethod(Request $request): array
    {
        $code = strtolower((string) $request->input('shipping_method', 'standard'));
        if (! isset(self::SHIPPING_METHODS[$code])) {
            throw ValidationException::withMessages(['shipping_method' => ['This shipping method is invalid.']]);
        }

        $config = self::SHIPPING_METHODS[$code];
        return ['code' => $code, 'label' => $config['label'], 'cost' => (float) $config['cost'], 'delivery_days' => $config['delivery_days']];
    }

    protected function resolveCoupon(Request $request, Cart $cart): ?array
    {
        $code = trim((string) $request->input('coupon_code', $cart->coupon_code ?? ''));
        if ($code === '') {
            return null;
        }

        $coupon = Coupon::query()->where('code', strtoupper($code))->where('status', 'active')->first();
        if (! $coupon) {
            throw ValidationException::withMessages(['coupon_code' => ['This coupon code is invalid or expired.']]);
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            throw ValidationException::withMessages(['coupon_code' => ['This coupon is not active yet.']]);
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            throw ValidationException::withMessages(['coupon_code' => ['This coupon has expired.']]);
        }

        $amount = $this->calculateCartTotals($cart, $coupon)['subtotal'];
        if ($coupon->min_order_amount && $amount < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages(['coupon_code' => ['This coupon requires a minimum order total.']]);
        }

        return [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'min_order_amount' => (float) ($coupon->min_order_amount ?? 0),
        ];
    }

    protected function resolvePaymentMethod(Request $request): array
    {
        $code = strtolower((string) $request->input('payment_method', 'cod'));
        if (! isset(self::PAYMENT_METHODS[$code])) {
            throw ValidationException::withMessages(['payment_method' => ['This payment method is not supported.']]);
        }

        return ['code' => $code, 'label' => self::PAYMENT_METHODS[$code]];
    }

    protected function validateCartForCheckout(Cart $cart, array $shippingAddress, array $billingAddress, array $shippingMethod, ?array $coupon): array
    {
        $items = [];
        $subtotal = 0.0;

        foreach ($cart->items()->with(['product', 'variant'])->get() as $item) {
            $product = $item->product;
            if (! $product || ! $product->is_active) {
                throw ValidationException::withMessages(['cart' => ['One or more products are no longer active.']]);
            }

            $unitPrice = (float) $this->currentPriceForItem($product, $item->product_variant_id);
            if ($unitPrice <= 0) {
                throw ValidationException::withMessages(['cart' => ['One or more product prices are invalid.']]);
            }

            $available = $this->availableStockForItem($product, $item->product_variant_id);
            if ($item->quantity > $available) {
                throw ValidationException::withMessages(['cart' => ['One or more products no longer have enough stock.']]);
            }

            $lineTotal = round($unitPrice * $item->quantity, 2);
            $subtotal += $lineTotal;

            $items[] = [
                'product_id' => $item->product_id,
                'product_name' => $product->name,
                'variant' => $item->variant ? $item->variant->name : null,
                'quantity' => $item->quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ];
        }

        $couponSummary = $this->calculateCouponDiscount($coupon, $subtotal);
        $discount = $couponSummary['discount'];
        $tax = round(($subtotal - $discount) * 0.08, 2);
        $shipping = (float) $shippingMethod['cost'];
        $grandTotal = round($subtotal - $discount + $tax + $shipping, 2);

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'tax' => round($tax, 2),
            'shipping' => round($shipping, 2),
            'grand_total' => round($grandTotal, 2),
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
            'shipping_method' => $shippingMethod,
        ];
    }

    protected function calculateCartTotals(Cart $cart, ?Coupon $coupon = null): array
    {
        $subtotal = 0.0;
        foreach ($cart->items()->with('product')->get() as $item) {
            $price = $this->currentPriceForItem($item->product, $item->product_variant_id);
            $subtotal += $price * $item->quantity;
        }

        $discount = 0.0;
        if ($coupon) {
            $discount = $this->calculateCouponDiscount([
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
            ], $subtotal)['discount'];
        }

        $tax = round(($subtotal - $discount) * 0.08, 2);
        $shipping = 0.0;
        $grandTotal = round($subtotal - $discount + $tax + $shipping, 2);

        return ['subtotal' => round($subtotal, 2), 'discount' => round($discount, 2), 'tax' => round($tax, 2), 'shipping' => round($shipping, 2), 'grand_total' => round($grandTotal, 2)];
    }

    protected function calculateCouponDiscount(?array $coupon, float $subtotal): array
    {
        if (! $coupon) {
            return ['discount' => 0.0, 'type' => null];
        }

        $value = (float) ($coupon['value'] ?? 0);
        if ($coupon['type'] === 'percentage') {
            $discount = ($value / 100) * $subtotal;
            return ['discount' => round($discount, 2), 'type' => 'percentage'];
        }

        $discount = min((float) $value, $subtotal);
        return ['discount' => round($discount, 2), 'type' => 'fixed'];
    }

    protected function currentPriceForItem(?Product $product, ?string $variantId): float
    {
        if (! $product) {
            return 0.0;
        }

        if ($variantId) {
            $variant = ProductVariant::query()->find($variantId);
            if ($variant) {
                return (float) ($variant->price ?? $product->price);
            }
        }

        return (float) $product->price;
    }

    protected function availableStockForItem(Product $product, ?string $variantId): int
    {
        if ($variantId) {
            $variant = ProductVariant::query()->find($variantId);
            if ($variant) {
                return max(0, (int) ($variant->stock ?? 0));
            }
        }

        return max(0, (int) ($product->stock_quantity ?? 0));
    }

    protected function serializeCart(Cart $cart): array
    {
        return [
            'id' => $cart->id,
            'status' => $cart->status,
            'item_count' => $cart->items()->sum('quantity'),
            'subtotal' => (float) $cart->subtotal,
            'total' => (float) $cart->total,
        ];
    }

    protected function validateAddressPayload(array $payload): void
    {
        $required = ['full_name', 'phone', 'address_line_1', 'city', 'state', 'postal_code', 'country'];
        foreach ($required as $field) {
            if (! isset($payload[$field]) || trim((string) $payload[$field]) === '') {
                throw ValidationException::withMessages([$field => ['This field is required.']]);
            }
        }
    }

    protected function normalizeAddress(Address $address): array
    {
        return [
            'id' => $address->id,
            'label' => $address->label,
            'full_name' => $address->full_name,
            'phone' => $address->phone,
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2,
            'city' => $address->city,
            'state' => $address->state,
            'postal_code' => $address->postal_code,
            'country' => $address->country,
            'country_code' => $address->country_code,
            'is_default' => (bool) $address->is_default,
        ];
    }
}
