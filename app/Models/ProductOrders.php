<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOrders extends Model
{
    protected $fillable = [
        'shop_id',
        'ware_house_id',
        'products',
        'due_date'
    ];

    protected $casts = [
        'products' => 'json',
        'due_date' => 'datetime'
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WareHouse::class);
    }
}
