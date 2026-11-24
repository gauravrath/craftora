<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Services\CartService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->resolveCart($request);

        return ApiResponse::success(
            $this->cartService->payloadForCart($cart),
            'Cart retrieved successfully.'
        );
    }

    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        $cart = $this->cartService->addItem($request);

        return ApiResponse::success(
            $this->cartService->payloadForCart($cart),
            'Item added to cart successfully.'
        );
    }

    public function updateItem(UpdateCartItemRequest $request, string $productId): JsonResponse
    {
        $cart = $this->cartService->updateItem($request, $productId);

        return ApiResponse::success(
            $this->cartService->payloadForCart($cart),
            'Cart updated successfully.'
        );
    }

    public function removeItem(Request $request, string $productId): JsonResponse
    {
        $cart = $this->cartService->removeItem($request, $productId);

        return ApiResponse::success(
            $this->cartService->payloadForCart($cart),
            'Item removed from cart successfully.'
        );
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $couponData = $this->cartService->applyCoupon($request);
        $cart = $this->cartService->resolveCart($request);

        return ApiResponse::success([
            'coupon' => $couponData['coupon'],
            'summary' => $couponData['summary'],
            'cart' => $this->cartService->payloadForCart($cart)['cart'],
        ], 'Coupon applied successfully.');
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        $cart = $this->cartService->removeCoupon($request);

        return ApiResponse::success(
            $this->cartService->payloadForCart($cart),
            'Coupon removed successfully.'
        );
    }
}
