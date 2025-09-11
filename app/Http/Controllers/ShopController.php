<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $shops = Shop::all();
    return response()->json(['data' => $shops], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name'          => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'mobile_number' => 'required|digits_between:7,15', // better for phone numbers
        ]);

        $shop = Shop::create($fields);

        return response()->json([
            'message' => 'Shop created successfully!',
            'data'    => $shop
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Shop $shop)
    {
    return response()->json(['data' => $shop], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShopRequest $request, Shop $shop)
    {
        $fields = $request->validated();
        $shop->update($fields);
        return response()->json([
            'message' => 'Shop updated successfully!',
            'data' => $shop
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop)
    {
        $shop->delete();
        return response()->json([
            'message' => 'Shop deleted successfully!'
        ], 200);
    }
}
