<?php

namespace App\Http\Controllers;

use App\Models\ReceivedOrder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ReceivedOrderController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        $receivedOrders = ReceivedOrder::with(['purchaseOrder', 'rawMaterial', 'supplier'])->get();
        return response()->json(['data' => $receivedOrders], 200);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'date' => 'required|date',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'received_quantity' => 'required|numeric|min:0',
            'variance' => 'required|numeric',
        ]);

        $receivedOrder = ReceivedOrder::create($fields);

        return response()->json([
            'message' => 'Received Order created successfully!',
            'data' => $receivedOrder
        ], 201);
    }

    public function show(ReceivedOrder $receivedOrder)
    {
        return response()->json([
            'data' => $receivedOrder->load(['purchaseOrder', 'rawMaterial', 'supplier'])
        ], 200);
    }

    public function update(Request $request, ReceivedOrder $receivedOrder)
    {
        $fields = $request->validate([
            'date' => 'sometimes|required|date',
            'purchase_order_id' => 'sometimes|required|exists:purchase_orders,id',
            'raw_material_id' => 'sometimes|required|exists:raw_materials,id',
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'received_quantity' => 'sometimes|required|numeric|min:0',
            'variance' => 'sometimes|required|numeric',
        ]);

        $receivedOrder->update($fields);

        return response()->json([
            'message' => 'Received Order updated successfully!',
            'data' => $receivedOrder
        ], 200);
    }

    public function destroy(ReceivedOrder $receivedOrder)
    {
        $receivedOrder->delete();
        return response()->json([
            'message' => 'Received Order deleted successfully!'
        ], 200);
    }
}
