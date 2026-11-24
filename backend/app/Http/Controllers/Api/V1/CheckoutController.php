<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CheckoutAddressRequest;
use App\Http\Requests\Checkout\CheckoutCouponRequest;
use App\Http\Requests\Checkout\CheckoutPaymentMethodRequest;
use App\Http\Requests\Checkout\CheckoutShippingMethodRequest;
use App\Http\Requests\Checkout\CheckoutValidateRequest;
use App\Services\CheckoutService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return ApiResponse::success(
            $this->checkoutService->getCheckoutSummary($request),
            'Checkout summary generated successfully.'
        );
    }

    public function validateCheckout(CheckoutValidateRequest $request): JsonResponse
    {
        return ApiResponse::success(
            $this->checkoutService->validateCheckout($request),
            'Checkout validation passed.'
        );
    }

    public function saveAddress(CheckoutAddressRequest $request): JsonResponse
    {
        return ApiResponse::success(
            $this->checkoutService->saveAddress($request),
            'Address saved successfully.',
            201
        );
    }

    public function updateAddress(CheckoutAddressRequest $request): JsonResponse
    {
        return ApiResponse::success(
            $this->checkoutService->updateAddress($request),
            'Address updated successfully.'
        );
    }

    public function shippingMethod(CheckoutShippingMethodRequest $request): JsonResponse
    {
        return ApiResponse::success(
            $this->checkoutService->selectShippingMethod($request),
            'Shipping method updated successfully.'
        );
    }

    public function coupon(CheckoutCouponRequest $request): JsonResponse
    {
        return ApiResponse::success(
            $this->checkoutService->applyCoupon($request),
            'Coupon applied successfully.'
        );
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        return ApiResponse::success(
            $this->checkoutService->clearCoupon($request),
            'Coupon removed successfully.'
        );
    }

    public function paymentMethod(CheckoutPaymentMethodRequest $request): JsonResponse
    {
        return ApiResponse::success(
            $this->checkoutService->selectPaymentMethod($request),
            'Payment method saved successfully.'
        );
    }

    public function review(Request $request): JsonResponse
    {
        return ApiResponse::success(
            $this->checkoutService->review($request),
            'Checkout review generated successfully.'
        );
    }
}
