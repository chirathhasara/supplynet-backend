<?php

namespace App\Http\Controllers;

use App\Models\ProductOrders;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductOrdersController extends Controller implements HasMiddleware
{
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
        $productOrders = ProductOrders::with(['shop', 'warehouse'])->get();
        return response()->json(['data' => $productOrders], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
    {
        $fields = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'ware_house_id' => 'required|exists:ware_houses,id',
            'products' => 'required|json',
            'due_date' => 'required|date',
        ]);

        $productOrder = ProductOrders::create($fields);

        return response()->json([
            'message' => 'Product Order created successfully!',
            'data' => $productOrder
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductOrders $productOrder)
    {
        return response()->json([
            'data' => $productOrder->load(['shop', 'warehouse'])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductOrders $productOrder)
    {
        $fields = $request->validate([
            'shop_id' => 'sometimes|required|exists:shops,id',
            'ware_house_id' => 'sometimes|required|exists:ware_houses,id',
            'products' => 'sometimes|required|json',
            'due_date' => 'sometimes|required|date',
        ]);

        $productOrder->update($fields);

        return response()->json([
            'message' => 'Product Order updated successfully!',
            'data' => $productOrder
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductOrders $productOrder)
    {
        $productOrder->delete();
        return response()->json([
            'message' => 'Product Order deleted successfully!'
        ], 200);
    }
}
