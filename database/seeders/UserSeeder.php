<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          User::factory()->create([
            'name' => 'top manager',
            'role' => 'top_management',
            'email'=>'topmanager@gmail.com',
            'password'=> Hash::make('topmanager123')
        ],
        );

          User::factory()->create([
            'name' => 'warehouse manager',
            'ware_house_id'=>'1',
            'role' => 'warehouse_manager',
            'email'=>'waremanager@gmail.com',
            'password'=> Hash::make('waremanager123')
        ],
        );

          User::factory()->create([
            'name' => 'warehouse storekeeper',
            'ware_house_id'=>'1',
            'role' => 'warehouse_storekeeper',
            'email'=>'warestore@gmail.com',
            'password'=> Hash::make('warestore123')
        ],
        );

          User::factory()->create([
            'name' => 'branch storekeeper',
            'shop_id'=>'1',
            'role' => 'branch_storekeeper',
            'email'=>'branchstore@gmail.com',
            'password'=> Hash::make('branchstore123')
        ],
        );

          User::factory()->create([
            'name' => 'Branch Manager',
            'shop_id'=>'1',
            'role' => 'branch_manager',
            'email'=>'branchmanager@gmail.com',
            'password'=> Hash::make('branchmanager123')
        ],
        );
    }
}
