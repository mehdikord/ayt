<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OtpSession;
use App\Models\User;
use App\Models\UserAuthToken;
use App\Support\ApiResponse;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const STATIC_OTP = '123456';

    public function requestOtp(Request $request)
    {
        $validated = $request->validate([
            'mobile' => ['required', 'regex:/^09[0-9]{9}$/'],
        ]);

        $now = CarbonImmutable::now();

        $otpSession = OtpSession::create([
            'mobile' => $validated['mobile'],
            'otp_code_hash' => Hash::make(self::STATIC_OTP),
            'expires_at' => $now->addMinutes(2),
            'resend_available_at' => $now->addMinute(),
            'attempt_count' => 0,
            'max_attempts' => 5,
            'is_verified' => false,
            'request_ip' => $request->ip(),
            'request_user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);

        $data = [
            'otp_session_id' => $otpSession->id,
            'expires_at' => $otpSession->expires_at?->toIso8601String(),
            'resend_available_at' => $otpSession->resend_available_at?->toIso8601String(),
        ];

        if (config('app.debug')) {
            $data['dev_otp'] = self::STATIC_OTP;
        }

        return ApiResponse::success(
            data: $data,
            message: 'OTP generated successfully.',
        );
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'mobile' => ['required', 'regex:/^09[0-9]{9}$/'],
            'otp_code' => ['required', 'digits:6'],
            'otp_session_id' => ['nullable', 'integer'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $otpQuery = OtpSession::query()
            ->where('mobile', $validated['mobile'])
            ->whereNull('consumed_at')
            ->orderByDesc('id');

        if (! empty($validated['otp_session_id'])) {
            $otpQuery->where('id', $validated['otp_session_id']);
        }

        $otpSession = $otpQuery->first();

        if (! $otpSession) {
            return ApiResponse::error('OTP session not found.', 404);
        }

        if ($otpSession->expires_at->isPast()) {
            return ApiResponse::error('OTP has expired.', 422);
        }

        if ($otpSession->attempt_count >= $otpSession->max_attempts) {
            return ApiResponse::error('OTP attempt limit reached.', 429);
        }

        if (! Hash::check($validated['otp_code'], $otpSession->otp_code_hash)) {
            $otpSession->increment('attempt_count');

            return ApiResponse::error('Invalid OTP code.', 422);
        }

        $now = CarbonImmutable::now();

        $otpSession->forceFill([
            'is_verified' => true,
            'verified_at' => $now,
            'consumed_at' => $now,
        ])->save();

        $user = User::query()->firstOrCreate(
            ['mobile' => $validated['mobile']],
            ['name' => '']
        );

        $user->forceFill([
            'last_login_at' => $now,
        ])->save();

        $plainToken = Str::random(80);

        UserAuthToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'token_type' => 'access',
            'expires_at' => $now->addDays(7),
            'device_name' => $validated['device_name'] ?? 'mobile-web',
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
            'ip_address' => $request->ip(),
        ]);

        return ApiResponse::success(
            data: [
                'token_type' => 'Bearer',
                'access_token' => $plainToken,
                'expires_at' => $now->addDays(7)->toIso8601String(),
                'user' => [
                    'id' => $user->id,
                    'mobile' => $user->mobile,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url,
                ],
            ],
            message: 'OTP verified successfully.',
        );
    }

    public function me(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success(
            data: [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
            ]
        );
    }

    public function logout(Request $request)
    {
        $plainToken = $request->bearerToken();

        if ($plainToken) {
            UserAuthToken::query()
                ->where('token_hash', hash('sha256', $plainToken))
                ->whereNull('revoked_at')
                ->update(['revoked_at' => CarbonImmutable::now()]);
        }

        return ApiResponse::success(message: 'Logged out successfully.');
    }
}
