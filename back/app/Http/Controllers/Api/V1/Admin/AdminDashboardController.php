<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuVariant;
use App\Models\User;
use App\Models\UserDiscount;
use App\Support\ApiResponse;

class AdminDashboardController extends Controller
{
    public function summary()
    {
        return ApiResponse::success(
            data: [
                'menu_categories_count' => MenuCategory::query()->count(),
                'menu_items_count' => MenuItem::query()->count(),
                'menu_variants_count' => MenuVariant::query()->count(),
                'users_count' => User::query()->count(),
                'active_discounts_count' => UserDiscount::query()->where('is_active', true)->count(),
            ],
            message: 'Dashboard summary fetched successfully.',
        );
    }
}
