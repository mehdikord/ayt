<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DiscountController;
use App\Http\Controllers\Api\V1\HealthCheckController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', HealthCheckController::class)->name('api.v1.health');

    Route::middleware('throttle:api')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/request-otp', [AuthController::class, 'requestOtp'])
                ->middleware('throttle:otp');
            Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])
                ->middleware('throttle:otp');
            Route::post('/logout', [AuthController::class, 'logout'])
                ->middleware('auth.token');
            Route::get('/me', [AuthController::class, 'me'])
                ->middleware('auth.token');
        });

        Route::middleware('auth.token')->group(function () {
            Route::patch('/profile', [ProfileController::class, 'update']);
            Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
            Route::get('/discounts/my', [DiscountController::class, 'myDiscounts']);
        });

        Route::get('/menu/categories', [MenuController::class, 'categories']);
        Route::get('/menu/categories/{id}/items', [MenuController::class, 'categoryItems']);
        Route::get('/menu/items/{id}', [MenuController::class, 'itemDetail']);

        Route::get('/pages/about', [PageController::class, 'about']);
    });
});
