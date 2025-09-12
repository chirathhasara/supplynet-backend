<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    /** @use HasFactory<\Database\Factories\RawMaterialFactory> */
    use HasFactory;
    protected $guarded =[];

    public function supplier(){

    return $this->belongsTo(Supplier::class);
    }
}
