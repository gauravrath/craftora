<?php

namespace App\Actions;

class HealthCheckAction
{
    public function execute(): array
    {
        return [
            'status' => 'ok',
            'service' => 'craftora-backend',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
