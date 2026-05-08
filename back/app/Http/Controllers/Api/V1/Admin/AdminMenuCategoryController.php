<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Support\AdminUniqueSlug;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        $includeInactive = $request->boolean('include_inactive');

        $q = MenuCategory::query()->orderBy('sort_order');
        if (! $includeInactive) {
            $q->where('is_active', true);
        }

        return ApiResponse::success(
            data: $q->get()->map(fn (MenuCategory $c) => $this->serializeCategory($c)),
            message: 'Categories fetched successfully.',
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140'],
            'image_url' => ['nullable', 'string', 'max:512'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $slug = AdminUniqueSlug::forModel(
            MenuCategory::class,
            'slug',
            $validated['slug'] ?? null,
            $validated['name'],
        );

        $category = MenuCategory::query()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'image_url' => $validated['image_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return ApiResponse::success(
            data: $this->serializeCategory($category),
            message: 'Category created successfully.',
            status: 201,
        );
    }

    public function show(int $id)
    {
        $category = MenuCategory::query()->findOrFail($id);

        return ApiResponse::success(
            data: $this->serializeCategory($category),
            message: 'Category fetched successfully.',
        );
    }

    public function update(Request $request, int $id)
    {
        $category = MenuCategory::query()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140'],
            'image_url' => ['nullable', 'string', 'max:512'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('name', $validated) || array_key_exists('slug', $validated)) {
            $category->slug = AdminUniqueSlug::forModel(
                MenuCategory::class,
                'slug',
                $validated['slug'] ?? $category->slug,
                $validated['name'] ?? $category->name,
                $category->id,
            );
        }

        $category->fill([
            'name' => $validated['name'] ?? $category->name,
            'image_url' => array_key_exists('image_url', $validated) ? $validated['image_url'] : $category->image_url,
            'sort_order' => $validated['sort_order'] ?? $category->sort_order,
            'is_active' => $validated['is_active'] ?? $category->is_active,
        ])->save();

        return ApiResponse::success(
            data: $this->serializeCategory($category->fresh()),
            message: 'Category updated successfully.',
        );
    }

    public function destroy(int $id)
    {
        $category = MenuCategory::query()->findOrFail($id);

        if (MenuItem::query()->where('category_id', $category->id)->exists()) {
            return ApiResponse::error(
                message: 'Cannot delete category while it has menu items.',
                status: 409,
            );
        }

        $category->delete();

        return ApiResponse::success(
            data: null,
            message: 'Category deleted successfully.',
        );
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct'],
        ]);

        $ids = $validated['ids'];
        $count = MenuCategory::query()->whereIn('id', $ids)->count();
        if ($count !== count($ids)) {
            return ApiResponse::error(
                message: 'One or more category ids are invalid.',
                status: 422,
            );
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                MenuCategory::query()->whereKey($id)->update(['sort_order' => $index]);
            }
        });

        return ApiResponse::success(
            data: ['ids' => $ids],
            message: 'Category order updated successfully.',
        );
    }

    private function serializeCategory(MenuCategory $c): array
    {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'image_url' => $c->image_url,
            'sort_order' => $c->sort_order,
            'is_active' => $c->is_active,
            'created_at' => $c->created_at?->toIso8601String(),
            'updated_at' => $c->updated_at?->toIso8601String(),
        ];
    }
}
