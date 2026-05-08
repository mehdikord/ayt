<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuVariant;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $category = MenuCategory::query()->updateOrCreate(
            ['slug' => 'coffee-based-drinks'],
            [
                'name' => 'نوشیدنی بر پایه قهوه',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $item = MenuItem::query()->updateOrCreate(
            ['slug' => 'espresso'],
            [
                'category_id' => $category->id,
                'name' => 'قهوه اسپرسو',
                'description' => 'اسپرسو تازه با دانه‌های منتخب',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $variants = [
            ['name' => 'اسپرسو 100% ربوستا', 'price' => 120000, 'sort_order' => 1],
            ['name' => 'اسپرسو 50/50', 'price' => 150000, 'sort_order' => 2],
            ['name' => 'اسپرسو 100% عربیکا', 'price' => 200000, 'sort_order' => 3],
        ];

        foreach ($variants as $variant) {
            MenuVariant::query()->updateOrCreate(
                [
                    'menu_item_id' => $item->id,
                    'name' => $variant['name'],
                ],
                [
                    'price' => $variant['price'],
                    'discount_price' => null,
                    'sort_order' => $variant['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
