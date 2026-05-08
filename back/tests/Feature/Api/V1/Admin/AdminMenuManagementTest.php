<?php

namespace Tests\Feature\Api\V1\Admin;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMenuManagementTest extends TestCase
{
    use RefreshDatabase;

    private function adminHeaders(): array
    {
        $this->seed(DatabaseSeeder::class);

        $login = $this->postJson('/api/v1/admin/auth/login', [
            'phone' => '09120000000',
            'password' => 'ChangeMe123!',
        ]);

        $login->assertOk();

        return ['Authorization' => 'Bearer '.$login->json('data.access_token')];
    }

    public function test_dashboard_summary_returns_counts(): void
    {
        $h = $this->adminHeaders();

        $this->getJson('/api/v1/admin/dashboard/summary', $h)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'menu_categories_count',
                    'menu_items_count',
                    'menu_variants_count',
                    'users_count',
                    'active_discounts_count',
                ],
            ]);
    }

    public function test_admin_menu_crud_and_delete_constraints(): void
    {
        $h = $this->adminHeaders();

        $cat = $this->postJson('/api/v1/admin/menu/categories', [
            'name' => 'دسته تست',
            'is_active' => true,
        ], $h)->assertCreated();

        $categoryId = $cat->json('data.id');

        $item = $this->postJson('/api/v1/admin/menu/items', [
            'category_id' => $categoryId,
            'name' => 'آیتم تست',
        ], $h)->assertCreated();

        $itemId = $item->json('data.id');

        $this->deleteJson("/api/v1/admin/menu/categories/{$categoryId}", [], $h)
            ->assertStatus(409);

        $var = $this->postJson("/api/v1/admin/menu/items/{$itemId}/variants", [
            'name' => 'سایز کوچک',
            'price' => '100.00',
        ], $h)->assertCreated();

        $variantId = $var->json('data.id');

        $this->deleteJson("/api/v1/admin/menu/items/{$itemId}", [], $h)
            ->assertStatus(409);

        $this->deleteJson("/api/v1/admin/menu/variants/{$variantId}", [], $h)
            ->assertOk();

        $this->deleteJson("/api/v1/admin/menu/items/{$itemId}", [], $h)
            ->assertOk();

        $this->deleteJson("/api/v1/admin/menu/categories/{$categoryId}", [], $h)
            ->assertOk();
    }

    public function test_variant_discount_price_must_not_exceed_price(): void
    {
        $h = $this->adminHeaders();

        $categoryId = $this->postJson('/api/v1/admin/menu/categories', ['name' => 'کافه'], $h)
            ->assertCreated()->json('data.id');

        $itemId = $this->postJson('/api/v1/admin/menu/items', [
            'category_id' => $categoryId,
            'name' => 'اسپرسو',
        ], $h)->assertCreated()->json('data.id');

        $this->postJson("/api/v1/admin/menu/items/{$itemId}/variants", [
            'name' => 'نامعتبر',
            'price' => 100,
            'discount_price' => 150,
        ], $h)->assertStatus(422);
    }
}
