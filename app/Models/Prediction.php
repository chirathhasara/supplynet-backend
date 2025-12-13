<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    /** @use HasFactory<\Database\Factories\PredictionFactory> */
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'product_id',
        'start_date',
        'end_date',
        'predicted_units_for_week',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'predicted_units_for_week' => 'integer',
    ];

    /**
     * Get the shop that owns the prediction.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the product that owns the prediction.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
