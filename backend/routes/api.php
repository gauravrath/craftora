<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\V1\HomeController;

    Route::get('/categories', [HomeController::class, 'categories']);
    Route::get('/v1/categories', [HomeController::class, 'storefrontCategories']);

    /*
    Route::prefix('v1')->group(function () {
        Route::get('/health', [App\Http\Controllers\Api\V1\HealthController::class, 'index']);

        Route::get('/ping', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'pong',
                'data' => [
                    'service' => 'craftora-backend',
                    'version' => 'v1',
                ],
            ]);
        });

        Route::get('/products', [App\Http\Controllers\Api\V1\ProductController::class, 'index']);
        Route::get('/products/{slug}', [App\Http\Controllers\Api\V1\ProductController::class, 'show']);

        Route::middleware(['throttle:60,1'])->group(function () {
            Route::get('/cart', [App\Http\Controllers\Api\V1\CartController::class, 'index']);
            Route::post('/cart/items', [App\Http\Controllers\Api\V1\CartController::class, 'addItem']);
            Route::put('/cart/items/{productId}', [App\Http\Controllers\Api\V1\CartController::class, 'updateItem']);
            Route::delete('/cart/items/{productId}', [App\Http\Controllers\Api\V1\CartController::class, 'removeItem']);
            Route::post('/cart/coupon', [App\Http\Controllers\Api\V1\CartController::class, 'applyCoupon']);
            Route::delete('/cart/coupon', [App\Http\Controllers\Api\V1\CartController::class, 'removeCoupon']);

            Route::get('/checkout', [App\Http\Controllers\Api\V1\CheckoutController::class, 'index']);
            Route::post('/checkout/validate', [App\Http\Controllers\Api\V1\CheckoutController::class, 'validateCheckout']);
            Route::post('/checkout/address', [App\Http\Controllers\Api\V1\CheckoutController::class, 'saveAddress']);
            Route::put('/checkout/address', [App\Http\Controllers\Api\V1\CheckoutController::class, 'updateAddress']);
            Route::post('/checkout/shipping-method', [App\Http\Controllers\Api\V1\CheckoutController::class, 'shippingMethod']);
            Route::post('/checkout/coupon', [App\Http\Controllers\Api\V1\CheckoutController::class, 'coupon']);
            Route::delete('/checkout/coupon', [App\Http\Controllers\Api\V1\CheckoutController::class, 'removeCoupon']);
            Route::post('/checkout/payment-method', [App\Http\Controllers\Api\V1\CheckoutController::class, 'paymentMethod']);
            Route::post('/checkout/review', [App\Http\Controllers\Api\V1\CheckoutController::class, 'review']);

            Route::post('/payments/checkout', [App\Http\Controllers\Api\V1\PaymentController::class, 'checkout']);
            Route::post('/payments/verify', [App\Http\Controllers\Api\V1\PaymentController::class, 'verify']);
            Route::post('/payments/webhook/razorpay', [App\Http\Controllers\Api\V1\PaymentController::class, 'webhook']);
        });

        Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
            Route::get('/orders', [App\Http\Controllers\Api\V1\OrderController::class, 'index']);
            Route::post('/orders', [App\Http\Controllers\Api\V1\OrderController::class, 'store']);
            Route::get('/orders/{id}', [App\Http\Controllers\Api\V1\OrderController::class, 'show']);
            Route::post('/orders/{order}/cancel', [App\Http\Controllers\Api\V1\OrderController::class, 'cancel']);
            Route::get('/orders/{order}/invoice', [App\Http\Controllers\Api\V1\OrderController::class, 'invoice']);

            Route::post('/products', [App\Http\Controllers\Api\V1\ProductController::class, 'store']);
            Route::put('/products/{product}', [App\Http\Controllers\Api\V1\ProductController::class, 'update']);
            Route::delete('/products/{product}', [App\Http\Controllers\Api\V1\ProductController::class, 'destroy']);
        });

        require __DIR__ . '/auth.php';
    */
