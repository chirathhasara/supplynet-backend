<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WareHouse extends Model
{
    /** @use HasFactory<\Database\Factories\WareHouseFactory> */
    use HasFactory;
    protected $guarded =[];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function productOrders(): HasMany
    {
        return $this->hasMany(ProductOrders::class);
    }

}
