<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
   
    public function index()
    {
        $deliveries = Delivery::with('shop')->latest()->get();
        return response()->json($deliveries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    DB::beginTransaction();

    try {
        // 1️⃣ Create the delivery record
        $delivery = Delivery::create([
            'date' => $request->date,
            'product_orders_id' => $request->product_orders_id,
            'shop_id' => $request->shop_id,
            'products' => $request->products, // keep original JSON string
            'distance' => $request->distance,
            'approximate_time' => $request->approximate_time,
        ]);

        // 2️⃣ Decode the JSON string into an array
        $products = json_decode($request->products, true);

        // 3️⃣ Loop through each product and decrease its units
        foreach ($products as $item) {
            $product = Product::find($item['product_id']);

            if ($product) {
                // Check if stock is enough before reducing
                if ($product->units < $item['units']) {
                    throw new \Exception("Not enough stock for product ID {$item['product_id']}");
                }

                // Decrease units safely
                $product->decrement('units', $item['units']);
            } else {
                throw new \Exception("Product not found: ID {$item['product_id']}");
            }
        }

        // 4️⃣ Commit changes if everything is OK
        DB::commit();

        return response()->json([
            'message' => 'Delivery created successfully!',
            'delivery' => $delivery,
        ], 201);

    } catch (\Exception $e) {
        // 5️⃣ Rollback all DB changes if any error occurs
        DB::rollBack();

        return response()->json([
            'error' => 'Delivery creation failed.',
            'message' => $e->getMessage(),
        ], 500);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        return response()->json($delivery->load('shop'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $delivery->update([
            'date' => $request->date ?? $delivery->date,
            'shop_id' => $request->shop_id ?? $delivery->shop_id,
            'product_orders_id' => $request->product_orders_id ?? $delivery->product_orders_id,
            'products' => $request->products ?? $delivery->products,
            'distance' => $request->distance ?? $delivery->distance,
            'approximate_time' => $request->approximate_time ?? $delivery->approximate_time
        ]);

        return response()->json($delivery->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();
        return response()->json(null, 204);
    }
}
