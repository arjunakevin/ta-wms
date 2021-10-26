<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoodReceiveController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\MovementOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'appLogin']);

Route::middleware('auth:sanctum')->get('/session', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/clients', [ClientController::class, 'list'])->name('clients.list');
    Route::get('/products', [ProductController::class, 'list'])->name('products.list');
    Route::get('/locations', [LocationController::class, 'list'])->name('locations.list');
    
    Route::get('/dashboard', [DashboardController::class, 'appDashboardData'])->name('app.dashboard.data');
    
    Route::group(['prefix' => '/gr_check'], function () {
        Route::get('/{good_receive}/status', [GoodReceiveController::class, 'appGoodReceiveCheckSearch'])->name('app.inbound_check.search');
        Route::get('/{good_receive}/data', [GoodReceiveController::class, 'appGoodReceiveCheckData'])->name('app.inbound_check.get');
        Route::post('/{good_receive}/check', [GoodReceiveController::class, 'submitCheck'])->name('app.inbound_check.submit');
    });
    
    Route::group(['prefix' => '/putaway'], function () {
        Route::get('/{good_receive}/status', [GoodReceiveController::class, 'appGoodReceivePutawaySearch'])->name('app.putaway.search');
        Route::get('/{good_receive}/data', [GoodReceiveController::class, 'appGoodReceivePutawayData'])->name('app.putaway.get');
        Route::post('/{good_receive}/init', [GoodReceiveController::class, 'initPutaway'])->name('app.putaway.init');
        Route::post('/{movement_order}/submit', [GoodReceiveController::class, 'submitPutaway'])->name('app.putaway.submit');
    });
    
    Route::group(['prefix' => '/picking'], function () {
        Route::get('/{movement_order}/search', [MovementOrderController::class, 'appMovementSearch'])->name('app.picking.search');
        Route::get('/{movement_order}/data', [MovementOrderController::class, 'appPicklist'])->name('app.picking.get');
        Route::post('/{movement_order}/submit', [MovementOrderController::class, 'appSubmitPick'])->name('app.picking.submit');
    });
    
    Route::group(['prefix' => '/do_check'], function () {
        Route::get('/{delivery_order}/status', [DeliveryOrderController::class, 'appDeliveryOrderCheckSearch'])->name('app.outbound_check.search');
        Route::get('/{delivery_order}/data', [DeliveryOrderController::class, 'appDeliveryOrderCheckData'])->name('app.outbound_check.get');
        Route::post('/{delivery_order}/check', [DeliveryOrderController::class, 'submitCheck'])->name('app.outbound_check.submit');
    });
});
