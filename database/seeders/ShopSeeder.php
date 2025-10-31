<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shop::insert([
        [
            'name'=>'Shop 1',
            'location'=>'Kiribathgoda',
            'mobile_number'=>'0768612325'
        ],
        [
            'name'=>'Shop 2',
            'location'=>'Eheliyagoda',
            'mobile_number'=>'0768612326' 
        ],
        [
            'name'=>'Shop 3',
            'location'=>'Balangoda',
            'mobile_number'=>'0768612327' 
        ],
        [
            'name'=>'Shop 4',
            'location'=>'Colombo',
            'mobile_number'=>'0768612328' 
        ],
        [
            'name'=>'Shop 5',
            'location'=>'Ratnapura',
            'mobile_number'=>'0768612329' 
        ]
    ]);
    }
}
