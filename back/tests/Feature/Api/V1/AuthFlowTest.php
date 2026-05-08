<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_and_verify_otp_and_receive_access_token(): void
    {
        $mobile = '09123456789';

        $requestOtpResponse = $this->postJson('/api/v1/auth/request-otp', [
            'mobile' => $mobile,
        ]);

        $requestOtpResponse->assertOk()
            ->assertJsonPath('data.dev_otp', '123456');

        $verifyResponse = $this->postJson('/api/v1/auth/verify-otp', [
            'mobile' => $mobile,
            'otp_code' => '123456',
            'otp_session_id' => $requestOtpResponse->json('data.otp_session_id'),
        ]);

        $verifyResponse->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'user' => ['id', 'mobile', 'name', 'avatar_url'],
                ],
            ]);
    }

    public function test_authenticated_user_can_access_me_and_update_profile_and_avatar(): void
    {
        $mobile = '09123456789';
        $this->postJson('/api/v1/auth/request-otp', ['mobile' => $mobile]);
        $verifyResponse = $this->postJson('/api/v1/auth/verify-otp', [
            'mobile' => $mobile,
            'otp_code' => '123456',
        ]);

        $token = $verifyResponse->json('data.access_token');
        $headers = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/v1/auth/me', $headers)
            ->assertOk()
            ->assertJsonPath('data.mobile', $mobile);

        $this->patchJson('/api/v1/profile', ['name' => 'کاربر تست'], $headers)
            ->assertOk()
            ->assertJsonPath('data.name', 'کاربر تست');

        Storage::fake('public');

        $this->postJson('/api/v1/profile/avatar', [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ], $headers)->assertOk()
            ->assertJsonPath('success', true);
    }
}
