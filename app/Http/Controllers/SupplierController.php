<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;


class SupplierController extends Controller implements HasMiddleware
{
    
    public static function middleware()
    {

        return
        [
            new Middleware('auth:sanctum', except:['index','show'])
        ];

    }
    
    public function index()
    {
    $suppliers = Supplier::all();
    return response()->json(['data' => $suppliers], 200);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name'          => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'mobile_number' => 'required|digits_between:7,15',
        ]);

    $supplier = Supplier::create($fields);

        return response()->json([
            'message' => 'Supplier created successfully!',
            'data'    => $supplier
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
    return response()->json(['data' => $supplier], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $fields = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'location'      => 'sometimes|required|string|max:255',
            'mobile_number' => 'sometimes|required|digits_between:7,15',
        ]);

        $supplier->update($fields);

        return response()->json([
            'message' => 'Supplier updated successfully!',
            'data'    => $supplier
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json([
            'message' => 'Supplier deleted successfully!'
        ], 200);
    }
}
