<?php

namespace App\Http\Controllers;

use App\Models\AcceptProductOrder;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;

class AcceptProductOrderController extends Controller implements HasMiddleware
{
    private $acceptProductOrder;

    public function __construct(AcceptProductOrder $acceptProductOrder)
    {
        $this->acceptProductOrder = $acceptProductOrder;
    }

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show' ,'store'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $acceptOrders = AcceptProductOrder::with(['shop', 'delivery'])->get();
        return response()->json(['data' => $acceptOrders], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    
    {
    DB::beginTransaction();

    try {
        // ✅ Step 1: Validate all required fields
        $fields = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'delivery_id' => 'required|exists:deliveries,id',
            'received_products' => 'required|json',
            'accepted_products' => 'required|json',
            'rejected_products' => 'required|json',
            'note' => 'required|string',
        ]);

        // ✅ Step 2: Create AcceptProductOrder record
        $acceptOrder = AcceptProductOrder::create($fields);

        // ✅ Step 3: Decode JSON strings into arrays
        $accepted_products = json_decode($request->accepted_products, true);
        $received_products = json_decode($request->received_products, true);
        $rejected_products = json_decode($request->rejected_products, true);

        // ✅ Step 4: Get shop model
        $shop = Shop::findOrFail($request->shop_id);

        // ✅ Step 5: Prepare pivot data for sync/update
        $pivotData = [];
        foreach ($accepted_products as $item) {
            $productId = $item['product_id'];
            $acceptedUnits = $item['units'] ?? 0;

            // If shop already has this product, increase existing stock
            if ($shop->products()->where('product_id', $productId)->exists()) {
                $currentStock = $shop->products()->where('product_id', $productId)->first()->pivot->stock;
                $pivotData[$productId] = ['stock' => $currentStock + $acceptedUnits];
            } else {
                // Otherwise, create new pivot record
                $pivotData[$productId] = ['stock' => $acceptedUnits];
            }
        }

        // ✅ Step 6: Update pivot table (products of shop)
        foreach ($pivotData as $productId => $data) {
            $shop->products()->syncWithoutDetaching([$productId => $data]);
        }

        DB::commit();

        // ✅ Step 7: Return success response
        return response()->json([
            'message' => 'Product order acceptance created successfully!',
            'data' => $acceptOrder->load(['shop', 'delivery']),
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Failed to create product acceptance record.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(AcceptProductOrder $acceptProductOrder)
    {
        return response()->json([
            'data' => $acceptProductOrder->load(['shop', 'delivery'])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcceptProductOrder $acceptProductOrder)
    {
        $fields = $request->validate([
            'shop_id' => 'sometimes|required|exists:shops,id',
            'delivery_id' => 'sometimes|required|exists:deliveries,id',
            'received_products' => 'sometimes|required|json',
            'accepted_products' => 'sometimes|required|json',
            'rejected_products' => 'sometimes|required|json',
            'note' => 'sometimes|required|string'
        ]);

        $acceptProductOrder->update($fields);

        return response()->json([
            'message' => 'Product order acceptance updated successfully!',
            'data' => $acceptProductOrder->fresh(['shop', 'delivery'])
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcceptProductOrder $acceptProductOrder)
    {
        $acceptProductOrder->delete();
        return response()->json([
            'message' => 'Product order acceptance deleted successfully!'
        ], 200);
    }
}
