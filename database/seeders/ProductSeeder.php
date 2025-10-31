<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([[
            'ware_house_id'=>'1',
            'name'=> 'Apple Juice',
            'image'=>'http://127.0.0.1:8000/images/products/apple.png',
            'price'=>'150',
            'units'=>'5000'
            ],
            [
            'ware_house_id'=>'1',
            'name'=> 'Blueberry Juice',
            'image'=>'http://127.0.0.1:8000/images/products/blueberry.png',
            'price'=>'150',
            'units'=>'4000'
            ],
            [
            'ware_house_id'=>'1',
            'name'=> 'Energy Drink',
            'image'=>'http://127.0.0.1:8000/images/products/energy.png',
            'price'=>'350',
            'units'=>'7000'
            ],
            [
            'ware_house_id'=>'1',
            'name'=> 'Orange Juice',
            'image'=>'http://127.0.0.1:8000/images/products/orange.png',
            'price'=>'150',
            'units'=>'4000'
            ],
            [
            'ware_house_id'=>'1',
            'name'=> 'Strawberry Juice',
            'image'=>'http://127.0.0.1:8000/images/products/strawberry.png',
            'price'=>'150',
            'units'=>'4000'
            ],
        ]);
    }
}
