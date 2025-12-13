<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller implements HasMiddleware
{
    private $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show', 'store'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with(['shop', 'product'])->get();
        return response()->json(['data' => $sales], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate all required fields
            $fields = $request->validate([
                'date' => 'required|date',
                'shop_id' => 'required|exists:shops,id',
                'product_id' => 'required|exists:products,id',
                'units_sold' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'promotion_flag' => 'required|boolean',
                'day_of_week' => 'required|integer|min:0|max:6',
                'is_weekend' => 'required|boolean',
                'is_holiday' => 'required|boolean',
                'lag_7_units_sold' => 'required|integer|min:0',
            ]);

            // Create sale record
            $sale = Sale::create($fields);

            DB::commit();

            return response()->json([
                'message' => 'Sale created successfully!',
                'data' => $sale->load(['shop', 'product']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create sale record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return response()->json([
            'data' => $sale->load(['shop', 'product'])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $fields = $request->validate([
            'date' => 'sometimes|required|date',
            'shop_id' => 'sometimes|required|exists:shops,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'units_sold' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            'promotion_flag' => 'sometimes|required|boolean',
            'day_of_week' => 'sometimes|required|integer|min:0|max:6',
            'is_weekend' => 'sometimes|required|boolean',
            'is_holiday' => 'sometimes|required|boolean',
            'lag_7_units_sold' => 'sometimes|required|integer|min:0',
        ]);

        $sale->update($fields);

        return response()->json([
            'message' => 'Sale updated successfully!',
            'data' => $sale->fresh(['shop', 'product'])
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

    /**
     * Get sales for a specific shop.
     */
    public function getByShop($shopId)
    {
        try {
            $sales = Sale::where('shop_id', $shopId)
                ->with(['product'])
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'data' => $sales
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch sales.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales for a specific product.
     */
    public function getByProduct($productId)
    {
        try {
            $sales = Sale::where('product_id', $productId)
                ->with(['shop'])
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'data' => $sales
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch sales.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistical details of sales.
     */
    public function statistics()
    {
        try {
            $totalSales = Sale::count();
            $totalRevenue = Sale::sum('price');
            $averageSalePrice = Sale::avg('price');
            
            // Sales by shop
            $salesByShop = Sale::select('shop_id', DB::raw('SUM(price) as total_revenue'), DB::raw('count(*) as total_sales'))
                ->with('shop:id,name')
                ->groupBy('shop_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'shop_id' => $item->shop_id,
                        'shop_name' => $item->shop->name ?? 'Unknown',
                        'total_sales' => $item->total_sales,
                        'total_revenue' => round($item->total_revenue, 2)
                    ];
                });
            
            // Sales by product
            $salesByProduct = Sale::select('product_id', DB::raw('SUM(price) as total_revenue'), DB::raw('count(*) as total_sales'))
                ->with('product:id,name')
                ->groupBy('product_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Unknown',
                        'total_sales' => $item->total_sales,
                        'total_revenue' => round($item->total_revenue, 2)
                    ];
                });
            
            // Promotion vs non-promotion sales
            $promotionSales = Sale::where('promotion_flag', true)->count();
            $nonPromotionSales = Sale::where('promotion_flag', false)->count();
            
            // Weekend vs weekday sales
            $weekendSales = Sale::where('is_weekend', true)->count();
            $weekdaySales = Sale::where('is_weekend', false)->count();
            
            // Holiday sales
            $holidaySales = Sale::where('is_holiday', true)->count();
            
            // Recent sales (last 10)
            $recentSales = Sale::with(['shop:id,name', 'product:id,name'])
                ->latest()
                ->take(10)
                ->get();

            return response()->json([
                'statistics' => [
                    'total_sales' => $totalSales,
                    'total_revenue' => round($totalRevenue, 2),
                    'average_sale_price' => round($averageSalePrice, 2),
                    'sales_by_shop' => $salesByShop,
                    'sales_by_product' => $salesByProduct,
                    'promotion_analysis' => [
                        'promotion_sales' => $promotionSales,
                        'non_promotion_sales' => $nonPromotionSales,
                        'promotion_percentage' => $totalSales > 0 ? round(($promotionSales / $totalSales) * 100, 2) : 0
                    ],
                    'day_analysis' => [
                        'weekend_sales' => $weekendSales,
                        'weekday_sales' => $weekdaySales,
                        'holiday_sales' => $holidaySales
                    ],
                    'recent_sales' => $recentSales
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
