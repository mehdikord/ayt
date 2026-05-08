<?php

namespace Tests\Feature\Api\V1;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuVariant;
use App\Models\StaticPage;
use App\Models\User;
use App\Models\UserAuthToken;
use App\Models\UserDiscount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuDiscountPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_endpoints_return_active_categories_items_and_variants(): void
    {
        $category = MenuCategory::query()->create([
            'name' => 'نوشیدنی گرم',
            'slug' => 'hot-drinks',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $item = MenuItem::query()->create([
            'category_id' => $category->id,
            'name' => 'اسپرسو',
            'slug' => 'espresso-hot',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        MenuVariant::query()->create([
            'menu_item_id' => $item->id,
            'name' => 'دبل',
            'price' => 100000,
            'discount_price' => 90000,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/menu/categories')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'hot-drinks');

        $this->getJson('/api/v1/menu/categories/'.$category->id.'/items')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'espresso-hot')
            ->assertJsonPath('data.0.variants.0.final_price', 90000);

        $this->getJson('/api/v1/menu/items/'.$item->id)
            ->assertOk()
            ->assertJsonPath('data.slug', 'espresso-hot');
    }

    public function test_about_page_endpoint_returns_content_from_database(): void
    {
        StaticPage::query()->create([
            'page_key' => 'about',
            'title' => 'درباره ما',
            'content' => 'توضیح نمونه',
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/pages/about')
            ->assertOk()
            ->assertJsonPath('data.page_key', 'about')
            ->assertJsonPath('data.content', 'توضیح نمونه');
    }

    public function test_discounts_endpoint_returns_only_current_user_discounts(): void
    {
        $user = User::query()->create([
            'mobile' => '09121111111',
            'name' => 'u1',
        ]);

        $otherUser = User::query()->create([
            'mobile' => '09122222222',
            'name' => 'u2',
        ]);

        UserDiscount::query()->create([
            'user_id' => $user->id,
            'title' => 'تخفیف ویژه',
            'code' => 'AYT-10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'is_active' => true,
        ]);

        UserDiscount::query()->create([
            'user_id' => $otherUser->id,
            'title' => 'غیرمجاز',
            'code' => 'OTHER-10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'is_active' => true,
        ]);

        $plainToken = 'test-token-123';
        UserAuthToken::query()->create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'token_type' => 'access',
            'expires_at' => now()->addDay(),
        ]);

        $this->getJson('/api/v1/discounts/my', [
            'Authorization' => 'Bearer '.$plainToken,
        ])->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', 'AYT-10');
    }

    public function test_expired_discount_returns_expired_status(): void
    {
        $user = User::query()->create([
            'mobile' => '09123333333',
            'name' => 'u3',
        ]);

        UserDiscount::query()->create([
            'user_id' => $user->id,
            'title' => 'منقضی',
            'code' => 'OLD-1',
            'discount_type' => 'percent',
            'discount_value' => 5,
            'expires_at' => now()->subDay(),
            'is_active' => true,
        ]);

        $plainToken = 'tok-expired-1';
        UserAuthToken::query()->create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'token_type' => 'access',
            'expires_at' => now()->addDay(),
        ]);

        $this->getJson('/api/v1/discounts/my', [
            'Authorization' => 'Bearer '.$plainToken,
        ])->assertOk()
            ->assertJsonPath('data.0.status', 'expired');
    }
}
