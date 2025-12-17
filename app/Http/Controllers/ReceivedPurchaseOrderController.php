<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\ReceivedPurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReceivedPurchaseOrderMail;

class ReceivedPurchaseOrderController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show', 'getAnomalyStatistics'])
        ];
    }

    /**
     * Display a listing of the resource.
     * 
     */
    public function index()
    {
        $receivedOrders = ReceivedPurchaseOrder::with(['purchaseOrder.rawMaterial', 'purchaseOrder.supplier'])->get();
        return response()->json(['data' => $receivedOrders], 200);
    }

    /**
     * Get anomaly detection statistics for management
     */
    public function getAnomalyStatistics()
    {
        try {
            // Overall Statistics
            $totalOrders = ReceivedPurchaseOrder::count();
            $lateDeliveries = ReceivedPurchaseOrder::where('no_of_late_days', '>', 0)->count();
            $qualityIssues = ReceivedPurchaseOrder::whereIn('quality_status', ['damaged', 'expired'])->count();
            $shortageOrders = ReceivedPurchaseOrder::where('unit_shortage', '>', 0)->count();
            $totalLossAmount = ReceivedPurchaseOrder::sum('loss_amount');

            // Anomaly Breakdown
            $anomalyBreakdown = [
                'late_deliveries' => $lateDeliveries,
                'quality_issues' => $qualityIssues,
                'shortage_issues' => $shortageOrders,
                'on_time_good_quality' => $totalOrders - $lateDeliveries - $qualityIssues
            ];

            // Quality Status Distribution
            $qualityDistribution = ReceivedPurchaseOrder::select('quality_status', DB::raw('count(*) as count'))
                ->groupBy('quality_status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->quality_status ?? 'unknown' => $item->count];
                });

            // Monthly Trend (Last 6 months)
            $monthlyTrend = ReceivedPurchaseOrder::select(
                    DB::raw('DATE_FORMAT(received_date, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(CASE WHEN no_of_late_days > 0 THEN 1 ELSE 0 END) as late_orders'),
                    DB::raw('SUM(CASE WHEN unit_shortage > 0 THEN 1 ELSE 0 END) as shortage_orders'),
                    DB::raw('SUM(CASE WHEN quality_status IN ("damaged", "expired") THEN 1 ELSE 0 END) as quality_issues')
                )
                ->where('received_date', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            // Supplier Performance Analysis - FIXED FIELD NAMES
            $supplierPerformance = DB::table('received_purchase_orders')
                ->join('purchase_orders', 'received_purchase_orders.purchase_order_id', '=', 'purchase_orders.id')
                ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
                ->select(
                    'suppliers.id as supplier_id',
                    'suppliers.name as supplier_name',
                    'suppliers.email as supplier_email',
                    'suppliers.mobile_number as supplier_mobile',  // FIXED: Changed from phone_no to mobile_number
                    'suppliers.location as supplier_location',      // ADDED: Location field
                    DB::raw('COUNT(received_purchase_orders.id) as total_orders'),
                    DB::raw('SUM(CASE WHEN received_purchase_orders.no_of_late_days > 0 THEN 1 ELSE 0 END) as late_deliveries'),
                    DB::raw('AVG(received_purchase_orders.no_of_late_days) as avg_late_days'),
                    DB::raw('SUM(CASE WHEN received_purchase_orders.unit_shortage > 0 THEN 1 ELSE 0 END) as shortage_orders'),
                    DB::raw('SUM(received_purchase_orders.unit_shortage) as total_shortage_units'),
                    DB::raw('SUM(received_purchase_orders.loss_amount) as total_loss_amount'),
                    DB::raw('SUM(CASE WHEN received_purchase_orders.quality_status IN ("damaged", "expired") THEN 1 ELSE 0 END) as quality_issues'),
                    DB::raw('ROUND((COUNT(received_purchase_orders.id) - SUM(CASE WHEN received_purchase_orders.no_of_late_days > 0 THEN 1 ELSE 0 END)) / COUNT(received_purchase_orders.id) * 100, 2) as on_time_delivery_rate'),
                    DB::raw('ROUND((COUNT(received_purchase_orders.id) - SUM(CASE WHEN received_purchase_orders.quality_status IN ("damaged", "expired") THEN 1 ELSE 0 END)) / COUNT(received_purchase_orders.id) * 100, 2) as quality_rate'),
                    DB::raw('ROUND((SUM(received_purchase_orders.received_units) / SUM(received_purchase_orders.requested_units)) * 100, 2) as fulfillment_rate')
                )
                ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.mobile_number', 'suppliers.location')
                ->orderBy('on_time_delivery_rate', 'desc')
                ->get();

            // Calculate overall supplier metrics
            $overallSupplierMetrics = [
                'avg_on_time_rate' => round($supplierPerformance->avg('on_time_delivery_rate'), 2),
                'avg_quality_rate' => round($supplierPerformance->avg('quality_rate'), 2),
                'avg_fulfillment_rate' => round($supplierPerformance->avg('fulfillment_rate'), 2),
                'total_suppliers' => $supplierPerformance->count(),
                'best_performing_supplier' => $supplierPerformance->first(),
                'worst_performing_supplier' => $supplierPerformance->last()
            ];

            // Top Issues by Raw Material
            $materialIssues = DB::table('received_purchase_orders')
                ->join('raw_materials', 'received_purchase_orders.raw_material_id', '=', 'raw_materials.id')
                ->select(
                    'raw_materials.name as material_name',
                    DB::raw('SUM(CASE WHEN received_purchase_orders.no_of_late_days > 0 THEN 1 ELSE 0 END) as late_count'),
                    DB::raw('SUM(CASE WHEN received_purchase_orders.unit_shortage > 0 THEN 1 ELSE 0 END) as shortage_count'),
                    DB::raw('SUM(CASE WHEN received_purchase_orders.quality_status IN ("damaged", "expired") THEN 1 ELSE 0 END) as quality_issue_count')
                )
                ->groupBy('raw_materials.id', 'raw_materials.name')
                ->havingRaw('(SUM(CASE WHEN received_purchase_orders.no_of_late_days > 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN received_purchase_orders.unit_shortage > 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN received_purchase_orders.quality_status IN ("damaged", "expired") THEN 1 ELSE 0 END)) > 0')
                ->orderByDesc(DB::raw('SUM(CASE WHEN received_purchase_orders.no_of_late_days > 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN received_purchase_orders.unit_shortage > 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN received_purchase_orders.quality_status IN ("damaged", "expired") THEN 1 ELSE 0 END)'))
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_orders' => $totalOrders,
                        'late_deliveries' => $lateDeliveries,
                        'quality_issues' => $qualityIssues,
                        'shortage_orders' => $shortageOrders,
                        'total_loss_amount' => round($totalLossAmount, 2),
                        'late_delivery_rate' => $totalOrders > 0 ? round(($lateDeliveries / $totalOrders) * 100, 2) : 0,
                        'quality_issue_rate' => $totalOrders > 0 ? round(($qualityIssues / $totalOrders) * 100, 2) : 0,
                        'shortage_rate' => $totalOrders > 0 ? round(($shortageOrders / $totalOrders) * 100, 2) : 0
                    ],
                    'anomaly_breakdown' => $anomalyBreakdown,
                    'quality_distribution' => $qualityDistribution,
                    'monthly_trend' => $monthlyTrend,
                    'supplier_performance' => $supplierPerformance,
                    'overall_supplier_metrics' => $overallSupplierMetrics,
                    'material_issues' => $materialIssues
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch anomaly statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $fields = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'raw_material_id' => 'required|exists:raw_materials,id',
                'requested_units' => 'required|numeric|min:0',
                'received_units' => 'required|numeric|min:0',
                'due_date' => 'required|date',
                'received_date' => 'required|date',
                'quality_status' => 'sometimes|in:good,damaged,expired',
                'batch_no' => 'nullable|string',
                'expiry_date' => 'nullable|date',
                'attachment' => 'nullable|string',
                'note' => 'nullable|string',
                'difference_reason' => 'nullable|string',
                'status' => 'sometimes|in:received,partially_received,pending',
            ]);

            // Calculate unit shortage
            $fields['unit_shortage'] = max(0, $fields['requested_units'] - $fields['received_units']);

            // Calculate number of late days
            $dueDate = Carbon::parse($fields['due_date']);
            $receivedDate = Carbon::parse($fields['received_date']);
            $fields['no_of_late_days'] = max(0, $receivedDate->diffInDays($dueDate, false) * -1);

            // Calculate loss amount based on shortage
            $purchaseOrder = \App\Models\PurchaseOrder::find($fields['purchase_order_id']);
            $fields['loss_amount'] = $fields['unit_shortage'] * ($purchaseOrder->unit_price ?? 0);

            // Update raw material stock
            $rawMaterial = RawMaterial::where('id', $request->raw_material_id)->first();
            $rawMaterial->update([
                'stock' => $rawMaterial->stock + $fields['received_units']
            ]);

            // Create received purchase order
            $receivedOrder = ReceivedPurchaseOrder::create($fields);

            // Get supplier information through raw material
            $supplier = $rawMaterial->supplier;

            // Send email notification to supplier
            Mail::to($supplier->email)->send(
                new ReceivedPurchaseOrderMail($receivedOrder, $supplier, $rawMaterial)
            );

            DB::commit();

            return response()->json([
                'message' => 'Received Purchase Order created successfully and email sent to supplier!',
                'data' => $receivedOrder->load('purchaseOrder')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create Received Purchase Order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReceivedPurchaseOrder $receivedPurchaseOrder)
    {
        return response()->json([
            'data' => $receivedPurchaseOrder->load(['purchaseOrder.rawMaterial', 'purchaseOrder.supplier'])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReceivedPurchaseOrder $receivedPurchaseOrder)
    {
        $fields = $request->validate([
            'purchase_order_id' => 'sometimes|required|exists:purchase_orders,id',
            'raw_material_id'=>'sometimes|required|exists:raw_materials,id',
            'requested_units' => 'sometimes|required|numeric|min:0',
            'received_units' => 'sometimes|required|numeric|min:0',
            'due_date' => 'sometimes|required|date',
            'received_date' => 'sometimes|required|date',
            'quality_status' => 'sometimes|in:good,damaged,expired',
            'batch_no' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'attachment' => 'nullable|string',
            'note' => 'nullable|string',
            'difference_reason' => 'nullable|string',
            'status' => 'sometimes|in:received,partially_received,pending',
        ]);

        // Recalculate unit shortage if units changed
        if (isset($fields['requested_units']) || isset($fields['received_units'])) {
            $requestedUnits = $fields['requested_units'] ?? $receivedPurchaseOrder->requested_units;
            $receivedUnits = $fields['received_units'] ?? $receivedPurchaseOrder->received_units;
            $fields['unit_shortage'] = max(0, $requestedUnits - $receivedUnits);
        }

        // Recalculate late days if dates changed
        if (isset($fields['due_date']) || isset($fields['received_date'])) {
            $dueDate = Carbon::parse($fields['due_date'] ?? $receivedPurchaseOrder->due_date);
            $receivedDate = Carbon::parse($fields['received_date'] ?? $receivedPurchaseOrder->received_date);
            $fields['no_of_late_days'] = max(0, $receivedDate->diffInDays($dueDate, false) * -1);
        }

        // Recalculate loss amount if needed
        if (isset($fields['unit_shortage']) || isset($fields['purchase_order_id'])) {
            $purchaseOrderId = $fields['purchase_order_id'] ?? $receivedPurchaseOrder->purchase_order_id;
            $purchaseOrder = \App\Models\PurchaseOrder::find($purchaseOrderId);
            $unitShortage = $fields['unit_shortage'] ?? $receivedPurchaseOrder->unit_shortage;
            $fields['loss_amount'] = $unitShortage * ($purchaseOrder->unit_price ?? 0);
        }

        $receivedPurchaseOrder->update($fields);

        return response()->json([
            'message' => 'Received Purchase Order updated successfully!',
            'data' => $receivedPurchaseOrder->load('purchaseOrder')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReceivedPurchaseOrder $receivedPurchaseOrder)
    {
        $receivedPurchaseOrder->delete();
        return response()->json([
            'message' => 'Received Purchase Order deleted successfully!'
        ], 200);
    }
}