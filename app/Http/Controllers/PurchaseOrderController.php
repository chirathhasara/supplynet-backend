<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchaseOrderMail;

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
            'total_price'=>'required|numeric|min:0',
            'due_date' => 'required|date|after:date',
            'status'=>'nullable'
        ]);

        // Calculate total price
        $purchaseOrder = PurchaseOrder::create($fields);

        // notify supplier by email if available
        $supplier = Supplier::find($request->supplier_id);
        $email = $supplier->email ?? null;

        if ($email) {
            Mail::to($email)->send(new PurchaseOrderMail($purchaseOrder));
        }

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
            'total_price'=>'sometimes|required|numeric|min:0',
            'due_date' => 'sometimes|required|date|after:date',
            'status'=>'sometimes|nullable'
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
