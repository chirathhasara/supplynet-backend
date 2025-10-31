<?php

namespace Database\Seeders;

use App\Models\RawMaterial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RawMaterial::insert([[
            'supplier_id'=>'1',
            'name'=>'Apples',
            'price'=>'20',
            'stock'=>'600'
        ],
        [
            'supplier_id'=>'1',
            'name'=>'Orange',
            'price'=>'20',
            'stock'=>'700'
        ],
        [
            'supplier_id'=>'1',
            'name'=>'Strawberry',
            'price'=>'30',
            'stock'=>'800'
        ],
        [
            'supplier_id'=>'1',
            'name'=>'Blueberry',
            'price'=>'350',
            'stock'=>'600'
        ],
        [
            'supplier_id'=>'2',
            'name'=>'Suger',
            'price'=>'15',
            'stock'=>'2200.5'
        ],
        [
            'supplier_id'=>'2',
            'name'=>'Sweets',
            'price'=>'30',
            'stock'=>'1500.5'
        ],
        [
            'supplier_id'=>'3',
            'name'=>'Critic Acid',
            'price'=>'20',
            'stock'=>'600'
        ],
        [
            'supplier_id'=>'4',
            'name'=>'Caffeine',
            'price'=>'40',
            'stock'=>'800.5'
        ],
        [
            'supplier_id'=>'5',
            'name'=>'Aluminum Cans',
            'price'=>'10',
            'stock'=>'6000'
        ],
        [
            'supplier_id'=>'5',
            'name'=>'Labels',
            'price'=>'5',
            'stock'=>'7000'
        ],
    ]);
    }
}
