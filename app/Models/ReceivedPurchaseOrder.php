<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivedPurchaseOrder extends Model
{
    /** @use HasFactory<\Database\Factories\ReceivedPurchaseOrderFactory> */
    use HasFactory;

    protected $guarded = [];

    public function purchaseOrder(){

        return $this->belongsTo(PurchaseOrder::class);
    }
}
