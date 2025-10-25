<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class SaleController extends Controller implements HasMiddleware
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
        $sales = Sale::with(['shop'])->get();
        return response()->json(['data' => $sales], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'date' => 'required|date',
            'total_sale' => 'required|numeric|min:0',
        ]);

        $sale = Sale::create($fields);

        return response()->json([
            'message' => 'Sale created successfully!',
            'data' => $sale
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return response()->json([
            'data' => $sale->load(['shop'])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $fields = $request->validate([
            'shop_id' => 'sometimes|required|exists:shops,id',
            'date' => 'sometimes|required|date',
            'total_sale' => 'sometimes|required|numeric|min:0',
        ]);

        $sale->update($fields);

        return response()->json([
            'message' => 'Sale updated successfully!',
            'data' => $sale
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();
        return response()->json([
            'message' => 'Sale deleted successfully!'
        ], 200);
    }
}
