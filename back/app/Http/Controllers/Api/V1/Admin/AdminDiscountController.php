<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDiscount;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminDiscountController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'is_active' => ['nullable', 'boolean'],
            'discount_type' => ['nullable', Rule::in(['percent', 'fixed_amount'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = min(100, max(1, (int) $request->query('per_page', 15)));

        $q = UserDiscount::query()->with('user')->orderByDesc('id');

        if ($request->filled('user_id')) {
            $q->where('user_id', (int) $request->query('user_id'));
        }

        if ($request->has('is_active')) {
            $q->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('discount_type')) {
            $q->where('discount_type', $request->query('discount_type'));
        }

        $paginator = $q->paginate($perPage);

        return ApiResponse::success(
            data: [
                'items' => collect($paginator->items())->map(fn (UserDiscount $d) => $this->serializeDiscount($d))->values()->all(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
            ],
            message: 'Discounts fetched successfully.',
        );
    }

    public function forUser(int $userId)
    {
        User::query()->findOrFail($userId);

        $items = UserDiscount::query()
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();

        return ApiResponse::success(
            data: $items->map(fn (UserDiscount $d) => $this->serializeDiscount($d)),
            message: 'User discounts fetched successfully.',
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['nullable', 'string', 'max:180'],
            'code' => ['required', 'string', 'max:64', Rule::unique('user_discounts', 'code')],
            'discount_type' => ['required', Rule::in(['percent', 'fixed_amount'])],
            'discount_value' => ['required', 'numeric', 'min:0.01'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $this->assertDiscountRules($validated['discount_type'], (float) $validated['discount_value']);

        $discount = UserDiscount::query()->create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'] ?? null,
            'code' => $validated['code'],
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return ApiResponse::success(
            data: $this->serializeDiscount($discount->load('user')),
            message: 'Discount created successfully.',
            status: 201,
        );
    }

    public function show(int $id)
    {
        $discount = UserDiscount::query()->with('user')->findOrFail($id);

        return ApiResponse::success(
            data: $this->serializeDiscount($discount),
            message: 'Discount fetched successfully.',
        );
    }

    public function update(Request $request, int $id)
    {
        $discount = UserDiscount::query()->findOrFail($id);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:180'],
            'code' => ['sometimes', 'required', 'string', 'max:64', Rule::unique('user_discounts', 'code')->ignore($discount->id)],
            'discount_type' => ['sometimes', 'required', Rule::in(['percent', 'fixed_amount'])],
            'discount_value' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $type = $validated['discount_type'] ?? $discount->discount_type;
        $value = isset($validated['discount_value']) ? (float) $validated['discount_value'] : (float) $discount->discount_value;
        $this->assertDiscountRules($type, $value);

        $discount->fill([
            'title' => array_key_exists('title', $validated) ? $validated['title'] : $discount->title,
            'code' => $validated['code'] ?? $discount->code,
            'discount_type' => $type,
            'discount_value' => $value,
            'expires_at' => array_key_exists('expires_at', $validated) ? $validated['expires_at'] : $discount->expires_at,
            'is_active' => $validated['is_active'] ?? $discount->is_active,
        ])->save();

        return ApiResponse::success(
            data: $this->serializeDiscount($discount->fresh()->load('user')),
            message: 'Discount updated successfully.',
        );
    }

    public function destroy(int $id)
    {
        UserDiscount::query()->findOrFail($id)->delete();

        return ApiResponse::success(
            data: null,
            message: 'Discount deleted successfully.',
        );
    }

    private function assertDiscountRules(string $type, float $value): void
    {
        if ($type === 'percent' && $value > 100) {
            throw ValidationException::withMessages([
                'discount_value' => ['Percent discount cannot exceed 100.'],
            ]);
        }
    }

    private function serializeDiscount(UserDiscount $d): array
    {
        return [
            'id' => $d->id,
            'user_id' => $d->user_id,
            'user' => $d->relationLoaded('user') && $d->user ? [
                'id' => $d->user->id,
                'mobile' => $d->user->mobile,
                'name' => $d->user->name,
            ] : null,
            'title' => $d->title,
            'code' => $d->code,
            'discount_type' => $d->discount_type,
            'discount_value' => $d->discount_value,
            'expires_at' => $d->expires_at?->toIso8601String(),
            'is_active' => $d->is_active,
            'created_at' => $d->created_at?->toIso8601String(),
            'updated_at' => $d->updated_at?->toIso8601String(),
        ];
    }
}
