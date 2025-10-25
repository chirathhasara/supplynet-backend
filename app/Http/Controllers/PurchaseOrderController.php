<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PurchaseOrderController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['rawMaterial', 'supplier'])->get();
        return response()->json(['data' => $purchaseOrders], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'date' => 'required|date',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:date',
        ]);

        // Calculate total price
        $fields['total_price'] = $fields['quantity'] * $fields['unit_price'];

        $purchaseOrder = PurchaseOrder::create($fields);

        return response()->json([
            'message' => 'Purchase Order created successfully!',
            'data' => $purchaseOrder
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        return response()->json([
            'data' => $purchaseOrder->load(['rawMaterial', 'supplier'])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $fields = $request->validate([
            'date' => 'sometimes|required|date',
            'raw_material_id' => 'sometimes|required|exists:raw_materials,id',
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'quantity' => 'sometimes|required|numeric|min:0',
            'unit_price' => 'sometimes|required|numeric|min:0',
            'due_date' => 'sometimes|required|date|after:date',
        ]);

        // Recalculate total price if quantity or unit price is updated
        if (isset($fields['quantity']) || isset($fields['unit_price'])) {
            $quantity = $fields['quantity'] ?? $purchaseOrder->quantity;
            $unitPrice = $fields['unit_price'] ?? $purchaseOrder->unit_price;
            $fields['total_price'] = $quantity * $unitPrice;
        }

        $purchaseOrder->update($fields);

        return response()->json([
            'message' => 'Purchase Order updated successfully!',
            'data' => $purchaseOrder
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return response()->json([
            'message' => 'Purchase Order deleted successfully!'
        ], 200);
    }
}
