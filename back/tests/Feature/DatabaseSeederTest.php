<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\MenuCategory;
use App\Models\StaticPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_completes_and_inserts_core_rows(): void
    {
        $this->seed();

        $this->assertTrue(StaticPage::query()->where('page_key', 'about')->exists());
        $this->assertTrue(MenuCategory::query()->where('slug', 'coffee-based-drinks')->exists());
        $this->assertTrue(Admin::query()->where('phone', '09120000000')->exists());
    }
}
