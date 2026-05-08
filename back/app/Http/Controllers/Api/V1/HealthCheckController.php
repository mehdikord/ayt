<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class HealthCheckController extends Controller
{
    public function __invoke(Request $request)
    {
        return ApiResponse::success(
            data: [
                'service' => 'ayt-backend',
                'version' => 'v1',
                'timestamp' => now()->toIso8601String(),
                'request_id' => $request->attributes->get('request_id'),
            ],
            message: 'Service is healthy.',
        );
    }
}
