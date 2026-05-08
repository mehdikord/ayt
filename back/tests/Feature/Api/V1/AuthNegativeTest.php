<?php

namespace Tests\Feature\Api\V1;

use App\Models\OtpSession;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthNegativeTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_otp_rejects_invalid_mobile_format(): void
    {
        $this->postJson('/api/v1/auth/request-otp', [
            'mobile' => '08123456789',
        ])->assertStatus(422);
    }

    public function test_verify_otp_rejects_wrong_code(): void
    {
        $mobile = '09123456789';
        $sessionId = $this->postJson('/api/v1/auth/request-otp', [
            'mobile' => $mobile,
        ])->json('data.otp_session_id');

        $this->postJson('/api/v1/auth/verify-otp', [
            'mobile' => $mobile,
            'otp_code' => '000000',
            'otp_session_id' => $sessionId,
        ])->assertStatus(422)
            ->assertJsonPath('message', 'Invalid OTP code.');
    }

    public function test_verify_otp_rejects_expired_session(): void
    {
        $mobile = '09123456789';

        $session = OtpSession::query()->create([
            'mobile' => $mobile,
            'otp_code_hash' => Hash::make('123456'),
            'expires_at' => CarbonImmutable::now()->subMinute(),
            'resend_available_at' => CarbonImmutable::now()->subMinutes(2),
            'attempt_count' => 0,
            'max_attempts' => 5,
            'is_verified' => false,
        ]);

        $this->postJson('/api/v1/auth/verify-otp', [
            'mobile' => $mobile,
            'otp_code' => '123456',
            'otp_session_id' => $session->id,
        ])->assertStatus(422)
            ->assertJsonPath('message', 'OTP has expired.');
    }

    public function test_discounts_my_returns_unauthenticated_without_token(): void
    {
        $this->getJson('/api/v1/discounts/my')->assertStatus(401);
    }

    public function test_me_requires_bearer_token(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_request_otp_omits_dev_otp_when_debug_disabled(): void
    {
        config(['app.debug' => false]);

        $this->postJson('/api/v1/auth/request-otp', [
            'mobile' => '09123456789',
        ])->assertOk()
            ->assertJsonMissingPath('data.dev_otp');
    }
}
