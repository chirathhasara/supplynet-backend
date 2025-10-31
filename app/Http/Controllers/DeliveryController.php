<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;



class DeliveryController extends Controller
{
   
    public function index()
    {
        $deliveries = Delivery::with('shop')->latest()->get();
        return response()->json($deliveries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $delivery = Delivery::create([
            'date' => $request->date,
            'shop_id' => $request->shop_id,
            'products' => $request->products,
            'distance' => $request->distance,
            'approximate_time' => $request->approximate_time
        ]);

        return response()->json($delivery, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        return response()->json($delivery->load('shop'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $delivery->update([
            'date' => $request->date ?? $delivery->date,
            'shop_id' => $request->shop_id ?? $delivery->shop_id,
            'products' => $request->products ?? $delivery->products,
            'distance' => $request->distance ?? $delivery->distance,
            'approximate_time' => $request->approximate_time ?? $delivery->approximate_time
        ]);

        return response()->json($delivery->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();
        return response()->json(null, 204);
    }
}
