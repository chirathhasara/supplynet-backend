<?php

namespace App\Http\Controllers;

use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class WareHouseController extends Controller implements HasMiddleware
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
        $warehouses = WareHouse::all();
        return response()->json(['data' => $warehouses], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $warehouse = WareHouse::create($fields);

        return response()->json([
            'message' => 'Warehouse created successfully!',
            'data' => $warehouse
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(WareHouse $wareHouse)
    {
        return response()->json([
            'data' => $wareHouse
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WareHouse $wareHouse)
    {
        $fields = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
        ]);

        $wareHouse->update($fields);

        return response()->json([
            'message' => 'Warehouse updated successfully!',
            'data' => $wareHouse
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WareHouse $wareHouse)
    {
        $wareHouse->delete();
        return response()->json([
            'message' => 'Warehouse deleted successfully!'
        ], 200);
    }
}
