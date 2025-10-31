<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Shop;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                WareHouseSeeder::class,
                ShopSeeder::class,
                UserSeeder::class,
                ProductSeeder::class,
                SupplierSeeder::class,
                RawMaterialSeeder::class,
            ]
            );
    }
}
