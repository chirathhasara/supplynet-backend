<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;

class PredictionController extends Controller implements HasMiddleware
{
    private $prediction;

    public function __construct(Prediction $prediction)
    {
        $this->prediction = $prediction;
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
        $predictions = Prediction::with(['shop', 'product'])->get();
        return response()->json(['data' => $predictions], 200);
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
                'shop_id' => 'required|exists:shops,id',
                'product_id' => 'required|exists:products,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'predicted_units_for_week' => 'required|integer|min:0',
            ]);

            // Create prediction record
            $prediction = Prediction::create($fields);

            DB::commit();

            return response()->json([
                'message' => 'Prediction created successfully!',
                'data' => $prediction->load(['shop', 'product']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create prediction record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Prediction $prediction)
    {
        return response()->json([
            'data' => $prediction->load(['shop', 'product'])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prediction $prediction)
    {
        $fields = $request->validate([
            'shop_id' => 'sometimes|required|exists:shops,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'predicted_units_for_week' => 'sometimes|required|integer|min:0',
        ]);

        $prediction->update($fields);

        return response()->json([
            'message' => 'Prediction updated successfully!',
            'data' => $prediction->fresh(['shop', 'product'])
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prediction $prediction)
    {
        $prediction->delete();
        return response()->json([
            'message' => 'Prediction deleted successfully!'
        ], 200);
    }

    /**
     * Get predictions for a specific shop.
     */
    public function getByShop($shopId)
    {
        try {
            $predictions = Prediction::where('shop_id', $shopId)
                ->with(['product'])
                ->orderBy('start_date', 'desc')
                ->get();

            return response()->json([
                'data' => $predictions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch predictions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get predictions for a specific product.
     */
    public function getByProduct($productId)
    {
        try {
            $predictions = Prediction::where('product_id', $productId)
                ->with(['shop'])
                ->orderBy('start_date', 'desc')
                ->get();

            return response()->json([
                'data' => $predictions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch predictions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistical details of predictions.
     */
    public function statistics()
    {
        try {
            $totalPredictions = Prediction::count();
            $totalPredictedUnits = Prediction::sum('predicted_units_for_week');
            
            // Predictions by shop
            $predictionsByShop = Prediction::select('shop_id', DB::raw('SUM(predicted_units_for_week) as total_units'), DB::raw('count(*) as total_predictions'))
                ->with('shop:id,name')
                ->groupBy('shop_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'shop_id' => $item->shop_id,
                        'shop_name' => $item->shop->name ?? 'Unknown',
                        'total_predictions' => $item->total_predictions,
                        'total_predicted_units' => $item->total_units
                    ];
                });
            
            // Predictions by product
            $predictionsByProduct = Prediction::select('product_id', DB::raw('SUM(predicted_units_for_week) as total_units'), DB::raw('count(*) as total_predictions'))
                ->with('product:id,name')
                ->groupBy('product_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Unknown',
                        'total_predictions' => $item->total_predictions,
                        'total_predicted_units' => $item->total_units
                    ];
                });
            
            // Recent predictions (last 5)
            $recentPredictions = Prediction::with(['shop:id,name', 'product:id,name'])
                ->latest()
                ->take(5)
                ->get();

            // Upcoming predictions (future dates)
            $upcomingPredictions = Prediction::where('start_date', '>=', now())
                ->with(['shop:id,name', 'product:id,name'])
                ->orderBy('start_date', 'asc')
                ->take(5)
                ->get();

            return response()->json([
                'statistics' => [
                    'total_predictions' => $totalPredictions,
                    'total_predicted_units' => $totalPredictedUnits,
                    'predictions_by_shop' => $predictionsByShop,
                    'predictions_by_product' => $predictionsByProduct,
                    'recent_predictions' => $recentPredictions,
                    'upcoming_predictions' => $upcomingPredictions
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
