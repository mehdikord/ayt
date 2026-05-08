<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    public function run(): void
    {
        StaticPage::query()->updateOrCreate(
            ['page_key' => 'about'],
            [
                'title' => 'درباره ما',
                'content' => 'محتوای اولیه درباره ما',
                'is_active' => true,
            ]
        );
    }
}
