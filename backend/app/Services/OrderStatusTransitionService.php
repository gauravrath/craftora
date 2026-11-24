<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Validation\ValidationException;

class OrderStatusTransitionService
{
    public function transition(Order $order, OrderStatus|string $target): Order
    {
        $status = $target instanceof OrderStatus ? $target->value : strtolower((string) $target);

        $current = $order->status;
        $allowed = OrderStatus::allowedTransitions()[$current] ?? [];

        if (! in_array($status, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => ['Invalid status transition from ' . $current . ' to ' . $status . '.'],
            ]);
        }

        $order->status = $status;
        $order->save();

        return $order->fresh();
    }
}
