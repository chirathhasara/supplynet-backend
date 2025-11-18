<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class ShopController extends Controller implements HasMiddleware
{
    public static function middleware()
    {

        return
        [
            new Middleware('auth:sanctum', except:['index','show'])
        ];

    }
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
            'mobile_number' => 'required|digits_between:7,15', 
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
    public function update(Request $request, Shop $shop)
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

    public function getShopProducts($shop_id)
    {
        try {
           
            $shop = Shop::with(['products' => function ($query) {
                $query->withPivot('stock'); 
            }])->findOrFail($shop_id);

            
            $products = $shop->products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'stock' => (int) $product->pivot->stock, 
                ];
            });

            return response()->json([
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'products' => $products,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch shop products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
