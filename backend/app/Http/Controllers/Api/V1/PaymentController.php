<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    public function checkout(Request $request): JsonResponse
    {
        $data = $this->paymentService->createCheckoutPayment($request);

        return ApiResponse::success($data, 'Payment order prepared successfully.');
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $this->paymentService->verifyPayment($request);

        return ApiResponse::success($data, 'Payment verified successfully.');
    }

    public function webhook(Request $request): JsonResponse
    {
        try {
            $result = $this->paymentService->handleWebhook($request);
            return ApiResponse::success($result, 'Razorpay webhook processed successfully.');
        } catch (ValidationException $exception) {
            return ApiResponse::error($exception->errors()['signature'][0] ?? 'Invalid Razorpay signature.', 400, $exception->errors());
        }
    }
}
