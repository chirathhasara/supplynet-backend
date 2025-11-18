<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    private $delivery;

    public function __construct()
    {
        $this->delivery = new Delivery();
    }

    public function index()
    {
        $deliveries = $this->delivery->getAllDeliveries();
        return response()->json($deliveries);
    }

    public function store(Request $request)
    {
        try {
            $delivery = $this->delivery->createDelivery($request->all());
            return response()->json([
                'message' => 'Delivery created successfully!',
                'delivery' => $delivery,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Delivery creation failed.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Delivery $delivery)
    {
        return response()->json($delivery->load('shop'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $updated = $this->delivery->updateDelivery($delivery, $request->all());
        return response()->json($updated);
    }

    public function destroy(Delivery $delivery)
    {
        $this->delivery->deleteDelivery($delivery);
        return response()->json(null, 204);
    }

    public function getShops($shopId)
    {
        $deliveries = $this->delivery->getFilteredDeliveries($shopId);
        return response()->json($deliveries);
    }
}
