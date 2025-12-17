<?php

use App\Http\Controllers\AcceptProductOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\PredictionController;
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

// Sales specific routes must be before apiResource to avoid route parameter conflicts
Route::get('/sales/statistics', [SaleController::class, 'statistics']);
Route::get('/sales/shop/{shopId}', [SaleController::class, 'getByShop']);
Route::get('/sales/product/{productId}', [SaleController::class, 'getByProduct']);
Route::apiResource('sales', SaleController::class);

Route::apiResource('purchase-orders', PurchaseOrderController::class);
Route::apiResource('quotations', QuotationController::class);
Route::apiResource('deliveries', DeliveryController::class);
Route::apiResource('warehouses',WareHouseController::class);
Route::apiResource('received-orders',ReceivedOrderController::class);
Route::apiResource('product-orders',ProductOrdersController::class);
Route::apiResource('accept-products',AcceptProductOrderController::class);
Route::apiResource('accept-pruchase-orders',ReceivedPurchaseOrderController::class);

// Predictions specific routes must be before apiResource
Route::get('/predictions/statistics', [PredictionController::class, 'statistics']);
Route::get('/predictions/shop/{shopId}', [PredictionController::class, 'getByShop']);
Route::get('/predictions/product/{productId}', [PredictionController::class, 'getByProduct']);
Route::apiResource('predictions', PredictionController::class);

Route::get('/received-purchase-orders/anomaly-statistics', [ReceivedPurchaseOrderController::class, 'getAnomalyStatistics']);
Route::get('/deliveries/get-shops/{shopId}',[DeliveryController::class,'getShops']);
Route::get('/shops/{shop_id}/products', [ShopController::class, 'getShopProducts']);
Route::get('/shops/{shop_id}/products/{product_id}/stock', [ShopController::class, 'getProductStock']);
Route::get('/products-with-shops', [ShopController::class, 'getAllProductsWithShops']);
Route::get('/product-orders/shop/{shop_id}', [ProductOrdersController::class, 'getOrdersByShop']);

Route::get('/accept-product-orders/statistics', [AcceptProductOrderController::class, 'statistics']);
Route::get('/users/exclude-top-management', [AuthController::class, 'getAllUsersExceptTopManagement']);





