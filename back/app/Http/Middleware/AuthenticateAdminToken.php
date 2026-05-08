<?php

namespace App\Http\Middleware;

use App\Models\AdminAuthToken;
use App\Support\ApiResponse;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return ApiResponse::error('Unauthenticated.', 401);
        }

        $tokenHash = hash('sha256', $plainToken);

        $token = AdminAuthToken::query()
            ->with('admin')
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', CarbonImmutable::now())
            ->first();

        if (! $token || ! $token->admin || ! $token->admin->is_active) {
            return ApiResponse::error('Unauthenticated.', 401);
        }

        $request->setUserResolver(fn () => $token->admin);

        AdminAuthToken::query()
            ->whereKey($token->id)
            ->update(['last_used_at' => CarbonImmutable::now()]);

        return $next($request);
    }
}
