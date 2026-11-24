<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAYMENT_PENDING = 'payment_pending';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case PACKED = 'packed';
    case SHIPPED = 'shipped';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case RETURNED = 'returned';

    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }

    public static function allowedTransitions(): array
    {
        return [
            self::PENDING->value => [self::PAYMENT_PENDING->value, self::CANCELLED->value],
            self::PAYMENT_PENDING->value => [self::PAID->value, self::CANCELLED->value],
            self::PAID->value => [self::PROCESSING->value, self::CANCELLED->value],
            self::PROCESSING->value => [self::PACKED->value, self::CANCELLED->value],
            self::PACKED->value => [self::SHIPPED->value, self::CANCELLED->value],
            self::SHIPPED->value => [self::OUT_FOR_DELIVERY->value, self::CANCELLED->value],
            self::OUT_FOR_DELIVERY->value => [self::DELIVERED->value, self::CANCELLED->value],
            self::DELIVERED->value => [self::RETURNED->value],
            self::CANCELLED->value => [self::REFUNDED->value],
            self::REFUNDED->value => [self::RETURNED->value],
            self::RETURNED->value => [],
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAYMENT_PENDING => 'Payment pending',
            self::PAID => 'Paid',
            self::PROCESSING => 'Processing',
            self::PACKED => 'Packed',
            self::SHIPPED => 'Shipped',
            self::OUT_FOR_DELIVERY => 'Out for delivery',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::RETURNED => 'Returned',
        };
    }
}
