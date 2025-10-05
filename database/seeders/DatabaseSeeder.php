<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gọi seeder cho bảng assets
        $this->call([
            AssetsTableSeeder::class,
        ]);
    }
}