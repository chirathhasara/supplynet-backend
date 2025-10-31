<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::insert([[
            'name'=>'Lanka Fruits',
            'location'=>'Mathara',
            'mobile_number'=>'0768678955'
        ],[
            'name'=>'Gross Lanka',
            'location'=>'Kelaniya',
            'mobile_number'=>'0768678957'
        ],
    [
            'name'=>'Chem Units',
            'location'=>'Maharagama',
            'mobile_number'=>'0768678935'
        ],
    [
            'name'=>'Energyfy',
            'location'=>'Kiribathgoda',
            'mobile_number'=>'0768678947'
        ],
    [
            'name'=>'Packing Lanka',
            'location'=>'Wattala',
            'mobile_number'=>'0768678936'
        ],]);
    }
}
