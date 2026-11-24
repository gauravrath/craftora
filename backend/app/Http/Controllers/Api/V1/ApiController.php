<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected function apiVersion(): string
    {
        return 'v1';
    }
}
