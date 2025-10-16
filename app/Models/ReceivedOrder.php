<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivedOrder extends Model
{
    /** @use HasFactory<\Database\Factories\ReceivedOrderFactory> */
    use HasFactory;
    protected $guarded =[];
}
