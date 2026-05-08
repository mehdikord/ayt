<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuCategoryResource;
use App\Http\Resources\MenuItemResource;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Support\ApiResponse;

class MenuController extends Controller
{
    public function categories()
    {
        $categories = MenuCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return ApiResponse::success(
            data: MenuCategoryResource::collection($categories),
            message: 'Menu categories fetched successfully.'
        );
    }

    public function categoryItems(int $id)
    {
        $category = MenuCategory::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->firstOrFail();

        $items = $category->items()
            ->where('is_active', true)
            ->with(['variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return ApiResponse::success(
            data: MenuItemResource::collection($items),
            message: 'Menu items fetched successfully.'
        );
    }

    public function itemDetail(int $id)
    {
        $item = MenuItem::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->with(['variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->firstOrFail();

        return ApiResponse::success(
            data: new MenuItemResource($item),
            message: 'Menu item detail fetched successfully.'
        );
    }
}
