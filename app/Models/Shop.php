<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    /** @use HasFactory<\Database\Factories\ShopFactory> */
    use HasFactory;
    protected $guarded =[];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function sales(){
        return $this->hasMany(Sale::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class);
    }
}
