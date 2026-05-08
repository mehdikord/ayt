<?php

use App\Http\Controllers\Api\V1\Admin\AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\AdminHealthCheckController;
use App\Http\Controllers\Api\V1\Admin\AdminMenuCategoryController;
use App\Http\Controllers\Api\V1\Admin\AdminMenuItemController;
use App\Http\Controllers\Api\V1\Admin\AdminMenuVariantController;
use App\Http\Controllers\Api\V1\Admin\AdminProfileController;
use App\Http\Controllers\Api\V1\Admin\AdminStaticPageController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\Admin\AdminDiscountController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/admin')->group(function () {
    Route::get('/health', AdminHealthCheckController::class)->name('api.v1.admin.health');

    Route::middleware('throttle:api')->group(function () {
        Route::post('/auth/login', [AdminAuthController::class, 'login'])
            ->middleware('throttle:admin-login');

        Route::middleware('auth.admin')->group(function () {
            Route::post('/auth/logout', [AdminAuthController::class, 'logout']);
            Route::get('/auth/me', [AdminAuthController::class, 'me']);
            Route::patch('/profile', [AdminProfileController::class, 'update']);
            Route::post('/profile/avatar', [AdminProfileController::class, 'uploadAvatar']);

            Route::get('/dashboard/summary', [AdminDashboardController::class, 'summary']);

            Route::get('/menu/categories', [AdminMenuCategoryController::class, 'index']);
            Route::post('/menu/categories', [AdminMenuCategoryController::class, 'store']);
            Route::post('/menu/categories/reorder', [AdminMenuCategoryController::class, 'reorder']);
            Route::get('/menu/categories/{id}', [AdminMenuCategoryController::class, 'show'])->whereNumber('id');
            Route::patch('/menu/categories/{id}', [AdminMenuCategoryController::class, 'update'])->whereNumber('id');
            Route::delete('/menu/categories/{id}', [AdminMenuCategoryController::class, 'destroy'])->whereNumber('id');

            Route::get('/menu/items', [AdminMenuItemController::class, 'index']);
            Route::post('/menu/items', [AdminMenuItemController::class, 'store']);
            Route::post('/menu/items/reorder', [AdminMenuItemController::class, 'reorder']);
            Route::get('/menu/items/{id}', [AdminMenuItemController::class, 'show'])->whereNumber('id');
            Route::patch('/menu/items/{id}', [AdminMenuItemController::class, 'update'])->whereNumber('id');
            Route::delete('/menu/items/{id}', [AdminMenuItemController::class, 'destroy'])->whereNumber('id');

            Route::get('/menu/items/{itemId}/variants', [AdminMenuVariantController::class, 'index'])->whereNumber('itemId');
            Route::post('/menu/items/{itemId}/variants', [AdminMenuVariantController::class, 'store'])->whereNumber('itemId');
            Route::post('/menu/items/{itemId}/variants/reorder', [AdminMenuVariantController::class, 'reorder'])->whereNumber('itemId');
            Route::get('/menu/variants/{id}', [AdminMenuVariantController::class, 'show'])->whereNumber('id');
            Route::patch('/menu/variants/{id}', [AdminMenuVariantController::class, 'update'])->whereNumber('id');
            Route::delete('/menu/variants/{id}', [AdminMenuVariantController::class, 'destroy'])->whereNumber('id');

            Route::get('/users', [AdminUserController::class, 'index']);
            Route::get('/users/{userId}/discounts', [AdminDiscountController::class, 'forUser'])->whereNumber('userId');
            Route::get('/users/{id}', [AdminUserController::class, 'show'])->whereNumber('id');
            Route::patch('/users/{id}', [AdminUserController::class, 'update'])->whereNumber('id');

            Route::get('/discounts', [AdminDiscountController::class, 'index']);
            Route::post('/discounts', [AdminDiscountController::class, 'store']);
            Route::get('/discounts/{id}', [AdminDiscountController::class, 'show'])->whereNumber('id');
            Route::patch('/discounts/{id}', [AdminDiscountController::class, 'update'])->whereNumber('id');
            Route::delete('/discounts/{id}', [AdminDiscountController::class, 'destroy'])->whereNumber('id');

            Route::get('/pages', [AdminStaticPageController::class, 'index']);
            Route::get('/pages/key/{pageKey}', [AdminStaticPageController::class, 'showByKey']);
            Route::get('/pages/{id}', [AdminStaticPageController::class, 'show'])->whereNumber('id');
            Route::patch('/pages/{id}', [AdminStaticPageController::class, 'update'])->whereNumber('id');
        });
    });
});
