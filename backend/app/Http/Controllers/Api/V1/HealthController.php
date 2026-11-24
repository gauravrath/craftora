<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\HealthCheckAction;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class HealthController extends ApiController
{
    public function __construct(
        protected HealthCheckAction $healthCheckAction,
    ) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success(
            $this->healthCheckAction->execute(),
            'Backend API is healthy.',
            200,
            [
                'api_version' => $this->apiVersion(),
            ]
        );
    }
}
