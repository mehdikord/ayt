<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAuthFlowTest extends TestCase
{
    use RefreshDatabase;

    private function seedAdmin(): void
    {
        $this->seed(AdminSeeder::class);
    }

    private function adminAuthHeaders(): array
    {
        $this->seedAdmin();

        $login = $this->postJson('/api/v1/admin/auth/login', [
            'phone' => '09120000000',
            'password' => 'ChangeMe123!',
        ]);

        $login->assertOk();
        $token = $login->json('data.access_token');

        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_admin_can_login_and_access_me_and_logout(): void
    {
        $this->seedAdmin();

        $login = $this->postJson('/api/v1/admin/auth/login', [
            'phone' => '09120000000',
            'password' => 'ChangeMe123!',
        ]);

        $login->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_at',
                    'admin' => ['id', 'name', 'phone', 'is_active'],
                ],
            ]);

        $headers = ['Authorization' => 'Bearer '.$login->json('data.access_token')];

        $this->getJson('/api/v1/admin/auth/me', $headers)
            ->assertOk()
            ->assertJsonPath('data.phone', '09120000000');

        $this->postJson('/api/v1/admin/auth/logout', [], $headers)
            ->assertOk();

        $this->getJson('/api/v1/admin/auth/me', $headers)
            ->assertUnauthorized();
    }

    public function test_admin_login_fails_with_invalid_credentials(): void
    {
        $this->seedAdmin();

        $this->postJson('/api/v1/admin/auth/login', [
            'phone' => '09120000000',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    public function test_inactive_admin_cannot_login(): void
    {
        Admin::query()->create([
            'name' => 'غیرفعال',
            'phone' => '09121111111',
            'image' => null,
            'password_hash' => Hash::make('Secret123!'),
            'is_active' => false,
        ]);

        $this->postJson('/api/v1/admin/auth/login', [
            'phone' => '09121111111',
            'password' => 'Secret123!',
        ])->assertForbidden();
    }

    public function test_admin_can_update_profile_and_avatar(): void
    {
        $headers = $this->adminAuthHeaders();

        $this->patchJson('/api/v1/admin/profile', ['name' => 'مدیر جدید'], $headers)
            ->assertOk()
            ->assertJsonPath('data.name', 'مدیر جدید');

        Storage::fake('public');

        $this->post('/api/v1/admin/profile/avatar', [
            'avatar' => UploadedFile::fake()->image('a.jpg'),
        ], $headers)->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_admin_routes_require_bearer_token(): void
    {
        $this->getJson('/api/v1/admin/auth/me')->assertUnauthorized();
    }
}
