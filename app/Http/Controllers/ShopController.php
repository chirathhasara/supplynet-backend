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

    public function getAllProductsWithShops()
    {
        try {
            // Get all products with their shop relationships
            $products = \App\Models\Product::with(['shops' => function ($query) {
                $query->withPivot('stock');
            }])->get();

            // Format the response
            $formattedProducts = $products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price ?? null,
                    'total_shops' => $product->shops->count(),
                    'shops' => $product->shops->map(function ($shop) {
                        return [
                            'shop_id' => $shop->id,
                            'shop_name' => $shop->name,
                            'shop_location' => $shop->location,
                            'shop_mobile' => $shop->mobile_number,
                            'stock' => (int) $shop->pivot->stock,
                        ];
                    }),
                    'total_stock_across_shops' => $product->shops->sum('pivot.stock'),
                ];
            });

            return response()->json([
                'message' => 'Products with shop details fetched successfully',
                'total_products' => $products->count(),
                'data' => $formattedProducts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch products with shops',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProductStock($shop_id, $product_id)
    {
        try {
            // Find the shop
            $shop = Shop::findOrFail($shop_id);
            
            // Get the specific product with its stock for this shop
            $product = $shop->products()
                ->where('products.id', $product_id)
                ->withPivot('stock')
                ->first();

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found in this shop'
                ], 404);
            }

            return response()->json([
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'stock' => (int) $product->pivot->stock,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Shop not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch product stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
