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
            'email'=>'chirathhasara2001@gmail.com',
            'location'=>'Mathara',
            'mobile_number'=>'0768678955'
        ],[
            'name'=>'Gross Lanka',
            'email'=>'chirathperera1012@gmail.com',
            'location'=>'Kelaniya',
            'mobile_number'=>'0768678957'
        ],
    [
            'name'=>'Chem Units',
            'email'=>'kushalikaaroshijayaweera@gmail.com',
            'location'=>'Maharagama',
            'mobile_number'=>'0768678935'
        ],
    [
            'name'=>'Energyfy',
            'email'=>'pmdchperera@std.appsc.sab.ac.lk',
            'location'=>'Kiribathgoda',
            'mobile_number'=>'0768678947'
        ],
    [
            'name'=>'Packing Lanka',
            'email'=>'packing@gmail.com',
            'location'=>'Wattala',
            'mobile_number'=>'0768678936'
        ],]);
    }
}
