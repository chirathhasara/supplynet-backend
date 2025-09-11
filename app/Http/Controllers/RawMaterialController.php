<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;



class RawMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'suppliers_id' => 'required|exists:suppliers,id',
        ]);

        $rawMaterial = RawMaterial::create($validated);

        return response()->json([
            'message' => 'Raw material created successfully.',
            'data' => $rawMaterial
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(RawMaterial $rawMaterial)
    {
        return response()->json([
            'data' => $rawMaterial
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'suppliers_id' => 'sometimes|required|exists:suppliers,id',
        ]);

        $rawMaterial->update($validated);

        return response()->json([
            'message' => 'Raw material updated successfully.',
            'data' => $rawMaterial
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RawMaterial $rawMaterial)
    {
        $rawMaterial->delete();
        return response()->json([
            'message' => 'Raw material deleted successfully.'
        ], 200);
    }
}
