<?php

namespace Tests\Feature\Payments;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RazorpayPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_razorpay_webhook_rejects_invalid_signature(): void
    {
        $payload = [
            'event' => 'payment.authorized',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_test_123',
                        'order_id' => 'order_test_123',
                        'amount' => 10000,
                        'currency' => 'INR',
                        'status' => 'authorized',
                    ],
                ],
            ],
        ];

        $body = json_encode($payload);
        $signature = 'invalid-signature';

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'X-Razorpay-Signature' => $signature,
        ])->postJson('/api/v1/payments/webhook/razorpay', json_decode($body, true));

        $response->assertStatus(400);
        $this->assertSame('Invalid Razorpay signature.', $response->json('message'));
    }
}
