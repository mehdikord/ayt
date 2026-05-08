<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuVariant;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminMenuVariantController extends Controller
{
    public function index(int $menuItemId)
    {
        MenuItem::query()->findOrFail($menuItemId);

        $variants = MenuVariant::query()
            ->where('menu_item_id', $menuItemId)
            ->orderBy('sort_order')
            ->get();

        return ApiResponse::success(
            data: $variants->map(fn (MenuVariant $v) => $this->serializeVariant($v)),
            message: 'Variants fetched successfully.',
        );
    }

    public function store(Request $request, int $menuItemId)
    {
        MenuItem::query()->findOrFail($menuItemId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $this->assertDiscountPrice($validated['price'], $validated['discount_price'] ?? null);

        $variant = MenuVariant::query()->create([
            'menu_item_id' => $menuItemId,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return ApiResponse::success(
            data: $this->serializeVariant($variant),
            message: 'Variant created successfully.',
            status: 201,
        );
    }

    public function show(int $id)
    {
        $variant = MenuVariant::query()->findOrFail($id);

        return ApiResponse::success(
            data: $this->serializeVariant($variant),
            message: 'Variant fetched successfully.',
        );
    }

    public function update(Request $request, int $id)
    {
        $variant = MenuVariant::query()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:140'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $price = $validated['price'] ?? $variant->price;
        $discountPrice = array_key_exists('discount_price', $validated)
            ? $validated['discount_price']
            : $variant->discount_price;

        $this->assertDiscountPrice($price, $discountPrice);

        $variant->fill([
            'name' => $validated['name'] ?? $variant->name,
            'description' => array_key_exists('description', $validated) ? $validated['description'] : $variant->description,
            'price' => $price,
            'discount_price' => $discountPrice,
            'sort_order' => $validated['sort_order'] ?? $variant->sort_order,
            'is_active' => $validated['is_active'] ?? $variant->is_active,
        ])->save();

        return ApiResponse::success(
            data: $this->serializeVariant($variant->fresh()),
            message: 'Variant updated successfully.',
        );
    }

    public function destroy(int $id)
    {
        MenuVariant::query()->findOrFail($id)->delete();

        return ApiResponse::success(
            data: null,
            message: 'Variant deleted successfully.',
        );
    }

    public function reorder(Request $request, int $menuItemId)
    {
        MenuItem::query()->findOrFail($menuItemId);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', Rule::exists('menu_variants', 'id')->where('menu_item_id', $menuItemId)],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                MenuVariant::query()->whereKey($id)->update(['sort_order' => $index]);
            }
        });

        return ApiResponse::success(
            data: ['ids' => $ids],
            message: 'Variant order updated successfully.',
        );
    }

    private function assertDiscountPrice(float|string $price, null|float|string $discountPrice): void
    {
        if ($discountPrice === null || $discountPrice === '') {
            return;
        }

        $p = (float) $price;
        $d = (float) $discountPrice;

        if ($d > $p) {
            throw ValidationException::withMessages([
                'discount_price' => ['The discount price must be less than or equal to the price.'],
            ]);
        }
    }

    private function serializeVariant(MenuVariant $v): array
    {
        return [
            'id' => $v->id,
            'menu_item_id' => $v->menu_item_id,
            'name' => $v->name,
            'description' => $v->description,
            'price' => $v->price,
            'discount_price' => $v->discount_price,
            'sort_order' => $v->sort_order,
            'is_active' => $v->is_active,
            'created_at' => $v->created_at?->toIso8601String(),
            'updated_at' => $v->updated_at?->toIso8601String(),
        ];
    }
}
