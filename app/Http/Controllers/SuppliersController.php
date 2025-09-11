<?php

namespace App\Http\Controllers;

use App\Models\Suppliers;
use App\Http\Requests\StoreSuppliersRequest;
use App\Http\Requests\UpdateSuppliersRequest;
use Illuminate\Http\Request;


class SuppliersController extends Controller
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
        $fields = $request->validate([
            'name'          => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'mobile_number' => 'required|digits_between:7,15',
        ]);

        $supplier = Suppliers::create($fields);

        return response()->json([
            'message' => 'Supplier created successfully!',
            'data'    => $supplier
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Suppliers $suppliers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuppliersRequest $request, Suppliers $suppliers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Suppliers $suppliers)
    {
        //
    }
}
