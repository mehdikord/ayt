<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = min(100, max(1, (int) $request->query('per_page', 15)));

        $q = User::query()->orderByDesc('id');

        if ($request->filled('search')) {
            $s = '%'.$request->query('search').'%';
            $q->where(function ($w) use ($s) {
                $w->where('mobile', 'like', $s)
                    ->orWhere('name', 'like', $s);
            });
        }

        if ($request->has('is_active')) {
            $q->where('is_active', $request->boolean('is_active'));
        }

        $paginator = $q->paginate($perPage);

        return ApiResponse::success(
            data: [
                'items' => $paginator->items() ? collect($paginator->items())->map(fn (User $u) => $this->serializeUser($u))->values()->all() : [],
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
            ],
            message: 'Users fetched successfully.',
        );
    }

    public function show(int $id)
    {
        $user = User::query()->findOrFail($id);

        return ApiResponse::success(
            data: $this->serializeUser($user),
            message: 'User fetched successfully.',
        );
    }

    public function update(Request $request, int $id)
    {
        $user = User::query()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:120'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user->fill([
            'name' => $validated['name'] ?? $user->name,
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ])->save();

        return ApiResponse::success(
            data: $this->serializeUser($user->fresh()),
            message: 'User updated successfully.',
        );
    }

    private function serializeUser(User $u): array
    {
        return [
            'id' => $u->id,
            'mobile' => $u->mobile,
            'name' => $u->name,
            'avatar_url' => $u->avatar_url,
            'is_active' => $u->is_active,
            'last_login_at' => $u->last_login_at?->toIso8601String(),
            'created_at' => $u->created_at?->toIso8601String(),
        ];
    }
}
