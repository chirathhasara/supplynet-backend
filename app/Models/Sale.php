<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'shop_id',
        'product_id',
        'units_sold',
        'price',
        'promotion_flag',
        'day_of_week',
        'is_weekend',
        'is_holiday',
        'lag_7_units_sold',
    ];

    protected $casts = [
        'date' => 'date',
        'units_sold' => 'integer',
        'price' => 'float',
        'promotion_flag' => 'boolean',
        'day_of_week' => 'integer',
        'is_weekend' => 'boolean',
        'is_holiday' => 'boolean',
        'lag_7_units_sold' => 'integer',
    ];

    /**
     * Get the shop that owns the sale.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the product that owns the sale.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
