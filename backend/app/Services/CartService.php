<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartService extends BaseService
{
    public const TAX_RATE = 0.08;

    public function resolveCart(Request $request): Cart
    {
        $user = $request->user();
        $sessionId = $this->getSessionId($request) ?: (string) Str::uuid();

        if ($user) {
            $sessionCart = Cart::query()
                ->where('status', 'active')
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->first();

            $userCart = $user->carts()->where('status', 'active')->first();

            if (! $userCart) {
                $userCart = $sessionCart ?? $user->carts()->create([
                    'session_id' => $sessionId,
                    'status' => 'active',
                ]);
            }

            if ($sessionCart && $userCart->id !== $sessionCart->id) {
                $this->mergeCartIntoUser($sessionCart, $userCart);
            }

            $userCart->user_id = $user->id;
            $userCart->session_id = $sessionId;
            $userCart->save();

            return $userCart->fresh(['items.product', 'items.variant']);
        }

        $cart = Cart::query()
            ->where('status', 'active')
            ->where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if (! $cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'status' => 'active',
            ]);
        }

        return $cart->fresh(['items.product', 'items.variant']);
    }

    public function addItem(Request $request): Cart
    {
        $productId = $request->input('product_id');
        $variantId = $request->input('product_variant_id');
        $quantity = max(1, (int) $request->input('quantity', 1));

        $product = Product::query()->with('variants')->findOrFail($productId);
        $availableStock = $this->availableStock($product, $variantId);

        if ($quantity > $availableStock) {
            throw ValidationException::withMessages([
                'quantity' => ['Only ' . $availableStock . ' item(s) are available for this product.'],
            ]);
        }

        $cart = $this->resolveCart($request);

        $existingItem = $cart->items()->where('product_id', $productId)
            ->when($variantId, fn ($query) => $query->where('product_variant_id', $variantId))
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            $this->assertAvailableQuantity($product, $variantId, $newQuantity);

            $existingItem->quantity = $newQuantity;
            $existingItem->unit_price = $this->unitPriceForProduct($product, $variantId);
            $existingItem->total_price = round($existingItem->unit_price * $newQuantity, 2);
            $existingItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'unit_price' => $this->unitPriceForProduct($product, $variantId),
                'total_price' => round($this->unitPriceForProduct($product, $variantId) * $quantity, 2),
            ]);
        }

        $this->syncTotals($cart);

        return $cart->fresh(['items.product', 'items.variant']);
    }

    public function updateItem(Request $request, string $productId): Cart
    {
        $cart = $this->resolveCart($request);
        $quantity = max(0, (int) $request->input('quantity', 0));
        $variantId = $request->input('product_variant_id');

        $item = $cart->items()->where('product_id', $productId)
            ->when($variantId, fn ($query) => $query->where('product_variant_id', $variantId))
            ->firstOrFail();

        if ($quantity === 0) {
            $item->delete();
            $this->syncTotals($cart);
            return $cart->fresh(['items.product', 'items.variant']);
        }

        $product = Product::query()->with('variants')->findOrFail($productId);
        $this->assertAvailableQuantity($product, $variantId, $quantity);

        $item->quantity = $quantity;
        $item->unit_price = $this->unitPriceForProduct($product, $variantId);
        $item->total_price = round($item->unit_price * $quantity, 2);
        $item->save();

        $this->syncTotals($cart);

        return $cart->fresh(['items.product', 'items.variant']);
    }

    public function removeItem(Request $request, string $productId): Cart
    {
        $cart = $this->resolveCart($request);
        $variantId = $request->input('product_variant_id');

        $cart->items()->where('product_id', $productId)
            ->when($variantId, fn ($query) => $query->where('product_variant_id', $variantId))
            ->delete();

        $this->syncTotals($cart);

        return $cart->fresh(['items.product', 'items.variant']);
    }

    public function applyCoupon(Request $request): array
    {
        $cart = $this->resolveCart($request);
        $code = strtoupper(trim((string) $request->input('code', '')));

        if ($code === '') {
            throw ValidationException::withMessages([
                'code' => ['A coupon code is required.'],
            ]);
        }

        $coupon = Coupon::query()
            ->where('code', $code)
            ->where('status', 'active')
            ->first();

        if (! $coupon) {
            throw ValidationException::withMessages([
                'code' => ['This coupon code is invalid or expired.'],
            ]);
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            throw ValidationException::withMessages([
                'code' => ['This coupon code is not available yet.'],
            ]);
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            throw ValidationException::withMessages([
                'code' => ['This coupon code has expired.'],
            ]);
        }

        $subtotal = (float) $cart->subtotal;
        if ($coupon->min_order_amount && $subtotal < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'code' => ['This coupon requires a minimum order of $' . number_format((float) $coupon->min_order_amount, 2) . '.'],
            ]);
        }

        if ($coupon->min_order_amount && $subtotal >= (float) $coupon->min_order_amount) {
            $subtotal = max($subtotal, (float) $coupon->min_order_amount);
        }

        $discountAmount = $this->calculateDiscount($coupon, $subtotal);

        $cart->coupon_code = $coupon->code;
        $cart->coupon_type = $coupon->type;
        $cart->coupon_value = $coupon->value;
        $cart->discount_amount = round($discountAmount, 2);
        $cart->save();

        $this->syncTotals($cart);

        return [
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
                'min_order_amount' => (float) ($coupon->min_order_amount ?? 0),
            ],
            'summary' => $this->buildSummary($cart),
        ];
    }

    public function removeCoupon(Request $request): Cart
    {
        $cart = $this->resolveCart($request);
        $cart->coupon_code = null;
        $cart->coupon_type = null;
        $cart->coupon_value = null;
        $cart->discount_amount = 0;
        $cart->save();

        $this->syncTotals($cart);

        return $cart->fresh(['items.product', 'items.variant']);
    }

    public function payloadForCart(Cart $cart): array
    {
        $summary = $this->buildSummary($cart);

        return [
            'cart' => [
                'id' => $cart->id,
                'session_id' => $cart->session_id,
                'user_id' => $cart->user_id,
                'status' => $cart->status,
            ],
            'items' => $cart->items()->with(['product', 'variant'])->get()->map(function (CartItem $item) {
                return [
                    'id' => $item->id,
                    'cart_id' => $item->cart_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'price' => (float) $item->product->price,
                        'stock_quantity' => (int) ($item->product->stock_quantity ?? 0),
                    ] : null,
                    'variant' => $item->variant ? [
                        'id' => $item->variant->id,
                        'name' => $item->variant->name,
                        'stock' => (int) ($item->variant->stock ?? 0),
                    ] : null,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                ];
            })->values()->all(),
            'coupon' => $cart->coupon_code ? [
                'code' => $cart->coupon_code,
                'type' => $cart->coupon_type,
                'value' => (float) ($cart->coupon_value ?? 0),
            ] : null,
            'summary' => $summary,
        ];
    }

    public function buildSummary(Cart $cart): array
    {
        return [
            'subtotal' => round((float) $cart->subtotal, 2),
            'discount_amount' => round((float) $cart->discount_amount, 2),
            'tax_total' => round((float) $cart->tax_total, 2),
            'shipping_amount' => round((float) $cart->shipping_amount, 2),
            'total' => round((float) $cart->total, 2),
            'currency' => 'USD',
            'item_count' => $cart->items()->sum('quantity'),
        ];
    }

    protected function syncTotals(Cart $cart): void
    {
        $subtotal = (float) $cart->items()->sum('total_price');
        $discountAmount = $this->couponDiscountForCart($cart, $subtotal);
        $taxTotal = round(($subtotal - $discountAmount) * self::TAX_RATE, 2);
        $shippingAmount = 0.00;
        $total = round($subtotal - $discountAmount + $taxTotal + $shippingAmount, 2);

        $cart->subtotal = round($subtotal, 2);
        $cart->discount_amount = round($discountAmount, 2);
        $cart->tax_total = $taxTotal;
        $cart->shipping_amount = $shippingAmount;
        $cart->total = $total;
        $cart->save();
    }

    protected function mergeCartIntoUser(Cart $guestCart, Cart $userCart): void
    {
        foreach ($guestCart->items()->get() as $item) {
            $existingItem = $userCart->items()->where('product_id', $item->product_id)
                ->when($item->product_variant_id, fn ($query) => $query->where('product_variant_id', $item->product_variant_id))
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $item->quantity;
                $existingItem->unit_price = $this->unitPriceForProduct($item->product, $item->product_variant_id);
                $existingItem->total_price = round($existingItem->unit_price * $existingItem->quantity, 2);
                $existingItem->save();
                $item->delete();
                continue;
            }

            $item->cart_id = $userCart->id;
            $item->save();
        }

        $guestCart->delete();
        $userCart->refresh();
        $this->syncTotals($userCart);
    }

    protected function couponDiscountForCart(Cart $cart, float $subtotal): float
    {
        if (! $cart->coupon_code) {
            return 0.0;
        }

        $coupon = Coupon::query()->where('code', $cart->coupon_code)->where('status', 'active')->first();

        if (! $coupon) {
            return 0.0;
        }

        return $this->calculateDiscount($coupon, $subtotal);
    }

    protected function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->type === 'percentage') {
            $discount = (($coupon->value ?? 0) / 100) * $subtotal;
            if ($coupon->max_discount_amount) {
                $discount = min($discount, (float) $coupon->max_discount_amount);
            }

            return round($discount, 2);
        }

        $discount = (float) ($coupon->value ?? 0);

        if ($coupon->max_discount_amount) {
            $discount = min($discount, (float) $coupon->max_discount_amount);
        }

        return round($discount, 2);
    }

    protected function unitPriceForProduct(Product $product, ?string $variantId): float
    {
        if ($variantId) {
            $variant = $product->variants()->whereKey($variantId)->first();
            if ($variant) {
                return (float) ($variant->price ?? $product->price);
            }
        }

        return (float) $product->price;
    }

    protected function availableStock(Product $product, ?string $variantId): int
    {
        if ($variantId) {
            $variant = $product->variants()->whereKey($variantId)->first();
            if ($variant) {
                return (int) ($variant->stock ?? 9999);
            }
        }

        $stock = $product->stock_quantity ?? $product->inventory?->quantity_on_hand ?? 9999;

        return max(0, (int) $stock);
    }

    protected function assertAvailableQuantity(Product $product, ?string $variantId, int $quantity): void
    {
        if ($quantity > $this->availableStock($product, $variantId)) {
            throw ValidationException::withMessages([
                'quantity' => ['Only ' . $this->availableStock($product, $variantId) . ' item(s) are available for this product.'],
            ]);
        }
    }

    protected function getSessionId(Request $request): ?string
    {
        $header = $request->header('X-Cart-Session');
        if ($header) {
            return trim($header);
        }

        $cookie = $request->cookie('craftora_cart_session');
        if ($cookie) {
            return trim($cookie);
        }

        return null;
    }
}
