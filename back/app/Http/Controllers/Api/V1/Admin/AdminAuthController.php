<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminAuthToken;
use App\Support\ApiResponse;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $admin = Admin::query()
            ->where('phone', $validated['phone'])
            ->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password_hash)) {
            return ApiResponse::error(
                message: 'Invalid phone or password.',
                status: Response::HTTP_UNAUTHORIZED,
            );
        }

        if (! $admin->is_active) {
            return ApiResponse::error(
                message: 'Account is disabled.',
                status: Response::HTTP_FORBIDDEN,
            );
        }

        $plainToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $plainToken);
        $expiresAt = CarbonImmutable::now()->addDays(max(1, (int) config('admin.token_ttl_days', 7)));

        AdminAuthToken::query()->create([
            'admin_id' => $admin->id,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);

        $admin->forceFill(['last_login_at' => CarbonImmutable::now()])->save();

        return ApiResponse::success(
            data: [
                'access_token' => $plainToken,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->toIso8601String(),
                'admin' => $this->adminPayload($admin),
            ],
            message: 'Logged in successfully.',
        );
    }

    public function logout(Request $request)
    {
        $plainToken = $request->bearerToken();
        if ($plainToken) {
            $tokenHash = hash('sha256', $plainToken);
            AdminAuthToken::query()
                ->where('token_hash', $tokenHash)
                ->whereNull('revoked_at')
                ->update(['revoked_at' => CarbonImmutable::now()]);
        }

        return ApiResponse::success(
            data: null,
            message: 'Logged out successfully.',
        );
    }

    public function me(Request $request)
    {
        /** @var Admin $admin */
        $admin = $request->user();

        return ApiResponse::success(
            data: $this->adminPayload($admin),
            message: 'OK.',
        );
    }

    private function adminPayload(Admin $admin): array
    {
        return [
            'id' => $admin->id,
            'name' => $admin->name,
            'phone' => $admin->phone,
            'image' => $admin->image,
            'is_active' => $admin->is_active,
            'last_login_at' => $admin->last_login_at?->toIso8601String(),
        ];
    }
}
