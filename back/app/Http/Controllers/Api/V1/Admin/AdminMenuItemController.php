<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuVariant;
use App\Support\AdminUniqueSlug;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMenuItemController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:menu_categories,id'],
        ]);

        $includeInactive = $request->boolean('include_inactive');

        $q = MenuItem::query()->orderBy('sort_order');
        if ($request->filled('category_id')) {
            $q->where('category_id', (int) $request->query('category_id'));
        }
        if (! $includeInactive) {
            $q->where('is_active', true);
        }

        return ApiResponse::success(
            data: $q->get()->map(fn (MenuItem $i) => $this->serializeItem($i)),
            message: 'Menu items fetched successfully.',
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:menu_categories,id'],
            'name' => ['required', 'string', 'max:140'],
            'slug' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string', 'max:512'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $slug = AdminUniqueSlug::forModel(
            MenuItem::class,
            'slug',
            $validated['slug'] ?? null,
            $validated['name'],
        );

        $item = MenuItem::query()->create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return ApiResponse::success(
            data: $this->serializeItem($item),
            message: 'Menu item created successfully.',
            status: 201,
        );
    }

    public function show(int $id)
    {
        $item = MenuItem::query()->findOrFail($id);

        return ApiResponse::success(
            data: $this->serializeItem($item),
            message: 'Menu item fetched successfully.',
        );
    }

    public function update(Request $request, int $id)
    {
        $item = MenuItem::query()->findOrFail($id);

        $validated = $request->validate([
            'category_id' => ['sometimes', 'required', 'integer', 'exists:menu_categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:140'],
            'slug' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string', 'max:512'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('name', $validated) || array_key_exists('slug', $validated)) {
            $item->slug = AdminUniqueSlug::forModel(
                MenuItem::class,
                'slug',
                $validated['slug'] ?? $item->slug,
                $validated['name'] ?? $item->name,
                $item->id,
            );
        }

        $item->fill([
            'category_id' => $validated['category_id'] ?? $item->category_id,
            'name' => $validated['name'] ?? $item->name,
            'description' => array_key_exists('description', $validated) ? $validated['description'] : $item->description,
            'image_url' => array_key_exists('image_url', $validated) ? $validated['image_url'] : $item->image_url,
            'sort_order' => $validated['sort_order'] ?? $item->sort_order,
            'is_active' => $validated['is_active'] ?? $item->is_active,
        ])->save();

        return ApiResponse::success(
            data: $this->serializeItem($item->fresh()),
            message: 'Menu item updated successfully.',
        );
    }

    public function destroy(int $id)
    {
        $item = MenuItem::query()->findOrFail($id);

        if (MenuVariant::query()->where('menu_item_id', $item->id)->exists()) {
            return ApiResponse::error(
                message: 'Cannot delete menu item while it has variants.',
                status: 409,
            );
        }

        $item->delete();

        return ApiResponse::success(
            data: null,
            message: 'Menu item deleted successfully.',
        );
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct'],
        ]);

        $ids = $validated['ids'];
        $items = MenuItem::query()->whereIn('id', $ids)->get();
        if ($items->count() !== count($ids)) {
            return ApiResponse::error(
                message: 'One or more menu item ids are invalid.',
                status: 422,
            );
        }

        $categoryIds = $items->pluck('category_id')->unique()->values();
        if ($categoryIds->count() !== 1) {
            return ApiResponse::error(
                message: 'All items in a reorder request must belong to the same category.',
                status: 422,
            );
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                MenuItem::query()->whereKey($id)->update(['sort_order' => $index]);
            }
        });

        return ApiResponse::success(
            data: ['ids' => $ids],
            message: 'Menu item order updated successfully.',
        );
    }

    private function serializeItem(MenuItem $i): array
    {
        return [
            'id' => $i->id,
            'category_id' => $i->category_id,
            'name' => $i->name,
            'slug' => $i->slug,
            'description' => $i->description,
            'image_url' => $i->image_url,
            'sort_order' => $i->sort_order,
            'is_active' => $i->is_active,
            'created_at' => $i->created_at?->toIso8601String(),
            'updated_at' => $i->updated_at?->toIso8601String(),
        ];
    }
}
