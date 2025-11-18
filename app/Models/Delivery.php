<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Delivery extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'products' => 'array',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /** 🔹 Get all deliveries with related shops */
    public function getAllDeliveries()
    {
        return self::with('shop')->latest()->get();
    }

    /** 🔹 Filter deliveries by shop ID */
    public function getFilteredDeliveries($shopId)
    {
        return self::where('shop_id', $shopId)->with('shop')->get();
    }

    /** 🔹 Create a new delivery and update product stocks */
    public function createDelivery(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create delivery
            $delivery = self::create([
                'date' => $data['date'],
                'product_orders_id' => $data['product_orders_id'],
                'shop_id' => $data['shop_id'],
                'products' => $data['products'],
                'distance' => $data['distance'],
                'approximate_time' => $data['approximate_time'],
            ]);

            // Decode and update product stock
            $products = json_decode($data['products'], true);
            foreach ($products as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Product not found: ID {$item['product_id']}");
                }

                if ($product->units < $item['units']) {
                    throw new \Exception("Not enough stock for product ID {$item['product_id']}");
                }

                $product->decrement('units', $item['units']);
            }

            return $delivery;
        });
    }

    /** 🔹 Update existing delivery */
    public function updateDelivery(Delivery $delivery, array $data)
    {
        $delivery->update($data);
        return $delivery->fresh();
    }

    /** 🔹 Delete delivery */
    public function deleteDelivery(Delivery $delivery)
    {
        return $delivery->delete();
    }
}
