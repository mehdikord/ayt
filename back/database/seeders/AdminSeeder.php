<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::query()->updateOrCreate(
            ['phone' => '09120000000'],
            [
                'name' => 'ادمین سیستم',
                'image' => null,
                'password_hash' => Hash::make('ChangeMe123!'),
                'is_active' => true,
            ]
        );
    }
}
