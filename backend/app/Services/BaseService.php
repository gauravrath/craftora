<?php

namespace App\Services;

abstract class BaseService
{
    protected function validateNotNull(mixed $value, string $field): void
    {
        if ($value === null || $value === '') {
            throw new \InvalidArgumentException(sprintf('%s is required.', $field));
        }
    }
}
