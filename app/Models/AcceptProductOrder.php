<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptProductOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'received_products' => 'array',
        'accepted_products' => 'array',
        'rejected_products' => 'array',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    
}
