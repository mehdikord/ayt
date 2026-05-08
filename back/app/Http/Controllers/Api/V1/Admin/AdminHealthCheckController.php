<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class AdminHealthCheckController extends Controller
{
    public function __invoke(Request $request)
    {
        return ApiResponse::success(
            data: [
                'service' => 'ayt-backend-admin',
                'version' => 'v1',
                'timestamp' => now()->toIso8601String(),
                'request_id' => $request->attributes->get('request_id'),
            ],
            message: 'Admin API is healthy.',
        );
    }
}
