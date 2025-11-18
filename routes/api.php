<?php

use App\Http\Controllers\AcceptProductOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductOrdersController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ReceivedOrderController;
use App\Http\Controllers\ReceivedPurchaseOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\WareHouseController;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

Route::apiResource('products', ProductController::class);
Route::apiResource('raw-materials', RawMaterialController::class);
Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('shops', ShopController::class);
Route::apiResource('sales', SaleController::class);
Route::apiResource('purchase-orders', PurchaseOrderController::class);
Route::apiResource('quotations', QuotationController::class);
Route::apiResource('deliveries', DeliveryController::class);
Route::apiResource('warehouses',WareHouseController::class);
Route::apiResource('received-orders',ReceivedOrderController::class);
Route::apiResource('product-orders',ProductOrdersController::class);
Route::apiResource('accept-products',AcceptProductOrderController::class);
Route::apiResource('accept-pruchase-orders',ReceivedPurchaseOrderController::class);

Route::get('/received-purchase-orders/anomaly-statistics', [ReceivedPurchaseOrderController::class, 'getAnomalyStatistics']);
Route::get('/deliveries/get-shops/{shopId}',[DeliveryController::class,'getShops']);
Route::get('/shops/{shop_id}/products', [ShopController::class, 'getShopProducts']);








