<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\StaticPage;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\StaticPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersDiscountsPagesTest extends TestCase
{
    use RefreshDatabase;

    private function adminHeaders(): array
    {
        $this->seed(AdminSeeder::class);

        $login = $this->postJson('/api/v1/admin/auth/login', [
            'phone' => '09120000000',
            'password' => 'ChangeMe123!',
        ]);
        $login->assertOk();

        return ['Authorization' => 'Bearer '.$login->json('data.access_token')];
    }

    public function test_admin_can_list_and_update_users(): void
    {
        $h = $this->adminHeaders();

        $user = User::query()->create([
            'mobile' => '09123334444',
            'name' => 'کاربر نمونه',
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/admin/users?search=0912333', $h)
            ->assertOk()
            ->assertJsonPath('data.items.0.mobile', '09123334444');

        $this->patchJson("/api/v1/admin/users/{$user->id}", [
            'name' => 'نام جدید',
            'is_active' => false,
        ], $h)->assertOk()
            ->assertJsonPath('data.name', 'نام جدید')
            ->assertJsonPath('data.is_active', false);
    }

    public function test_admin_discount_crud_and_unique_code_validation(): void
    {
        $h = $this->adminHeaders();

        $user = User::query()->create([
            'mobile' => '09124445555',
            'name' => 'target',
        ]);

        $discountId = $this->postJson('/api/v1/admin/discounts', [
            'user_id' => $user->id,
            'title' => 'خوش آمد',
            'code' => 'WELCOME-10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'is_active' => true,
        ], $h)->assertCreated()
            ->json('data.id');

        $this->postJson('/api/v1/admin/discounts', [
            'user_id' => $user->id,
            'title' => 'تکراری',
            'code' => 'WELCOME-10',
            'discount_type' => 'fixed_amount',
            'discount_value' => 5000,
            'is_active' => true,
        ], $h)->assertStatus(422);

        $this->getJson("/api/v1/admin/users/{$user->id}/discounts", $h)
            ->assertOk()
            ->assertJsonPath('data.0.code', 'WELCOME-10');

        $this->patchJson("/api/v1/admin/discounts/{$discountId}", [
            'discount_type' => 'fixed_amount',
            'discount_value' => 20000,
        ], $h)->assertOk()
            ->assertJsonPath('data.discount_type', 'fixed_amount');

        $this->deleteJson("/api/v1/admin/discounts/{$discountId}", [], $h)
            ->assertOk();
    }

    public function test_admin_can_update_about_page_and_public_endpoint_reflects_it(): void
    {
        $h = $this->adminHeaders();
        $this->seed(StaticPageSeeder::class);

        $about = StaticPage::query()->where('page_key', 'about')->firstOrFail();

        $this->patchJson("/api/v1/admin/pages/{$about->id}", [
            'title' => 'درباره AYT',
            'content' => 'محتوای تست پنل ادمین',
            'is_active' => true,
        ], $h)->assertOk()
            ->assertJsonPath('data.title', 'درباره AYT');

        $this->getJson('/api/v1/pages/about')
            ->assertOk()
            ->assertJsonPath('data.content', 'محتوای تست پنل ادمین');
    }
}
