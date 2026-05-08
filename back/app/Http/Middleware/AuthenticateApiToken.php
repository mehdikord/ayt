<?php

namespace App\Http\Middleware;

use App\Models\UserAuthToken;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => [],
                'meta' => [],
            ], 401);
        }

        $tokenHash = hash('sha256', $plainToken);

        $token = UserAuthToken::query()
            ->with('user')
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', CarbonImmutable::now())
            ->first();

        if (! $token || ! $token->user || ! $token->user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => [],
                'meta' => [],
            ], 401);
        }

        $request->setUserResolver(fn () => $token->user);
        UserAuthToken::query()
            ->whereKey($token->id)
            ->update(['last_used_at' => CarbonImmutable::now()]);

        return $next($request);
    }
}
