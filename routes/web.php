<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\GoodReceiveController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\MovementOrderController;
use App\Http\Controllers\InboundDeliveryController;
use App\Http\Controllers\OutboundDeliveryController;
use App\Http\Controllers\MovementOrderDetailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'guest'], function () {
    Route::get('login', [AuthController::class, 'index'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    
    Route::resource('/users', UserController::class);
    Route::resource('/products', ProductController::class);
    Route::resource('/clients', ClientController::class);
    Route::resource('/locations', LocationController::class);
    
    Route::resource('inbounds', InboundDeliveryController::class);
    Route::post('inbound_details', [InboundDeliveryController::class, 'storeDetail'])->name('inbound_details.store');
    Route::put('inbound_details/{detail}', [InboundDeliveryController::class, 'updateDetail'])->name('inbound_details.update');
    Route::delete('inbound_details/{detail}', [InboundDeliveryController::class, 'destroyDetail'])->name('inbound_details.destroy');
    
    Route::resource('good_receives', GoodReceiveController::class)
        ->parameter('good_receives', 'good_receive')
        ->except('create');
    Route::get('good_receives/{inbound}/create', [GoodReceiveController::class, 'create'])->name('good_receives.create');
    Route::post('good_receives/inbound/search', [GoodReceiveController::class, 'searchInbound'])->name('good_receives.inbound.search');
    
    Route::get('good_receives/{good_receive}/check', [GoodReceiveController::class, 'check'])->name('good_receives.check');
    Route::post('good_receives/{good_receive}/check', [GoodReceiveController::class, 'submitCheck'])->name('good_receives.check.submit');
    Route::post('good_receives/{good_receive}/receive', [GoodReceiveController::class, 'receive'])->name('good_receives.receive');
    Route::get('good_receives/{good_receive}/report', [GoodReceiveController::class, 'report'])->name('good_receives.report');
    
    Route::get('inventories', [InventoryController::class, 'index'])->name('inventories.index');
    
    Route::get('putaway_picking', [MovementOrderController::class, 'list'])->name('movement_orders.list');
    Route::resource('movement_orders', MovementOrderController::class)->except('create');
    Route::get('movement_orders/{type}/{document_id}/create', [MovementOrderController::class, 'create'])->name('movement_orders.create');
    Route::post('movement_orders/document/search', [MovementOrderController::class, 'searchDocument'])->name('movement_orders.document.search');
    Route::post('movement_orders/process/confirm', [MovementOrderController::class, 'confirm'])->name('movement_orders.process.confirm');
    Route::post('movement_orders/process/cancel', [MovementOrderController::class, 'cancel'])->name('movement_orders.process.cancel');
    
    Route::get('movement_order_details/{movement_order}/create', [MovementOrderDetailController::class, 'create'])->name('movement_order_details.create');
    Route::post('movement_order_details/{movement_order}', [MovementOrderDetailController::class, 'store'])->name('movement_order_details.store');

    Route::resource('outbounds', OutboundDeliveryController::class);
    Route::post('outbound_details', [OutboundDeliveryController::class, 'storeDetail'])->name('outbound_details.store');
    Route::put('outbound_details/{detail}', [OutboundDeliveryController::class, 'updateDetail'])->name('outbound_details.update');
    Route::delete('outbound_details/{detail}', [OutboundDeliveryController::class, 'destroyDetail'])->name('outbound_details.destroy');

    Route::resource('delivery_orders', DeliveryOrderController::class)
        ->parameter('delivery_orders', 'delivery_order')
        ->except('create');
    Route::get('delivery_orders/{outbound}/create', [DeliveryOrderController::class, 'create'])->name('delivery_orders.create');
    Route::post('delivery_orders/outbound/search', [DeliveryOrderController::class, 'searchOutbound'])->name('delivery_orders.outbound.search');
    
    Route::get('delivery_orders/{delivery_order}/check', [DeliveryOrderController::class, 'check'])->name('delivery_orders.check');
    Route::post('delivery_orders/{delivery_order}/check', [DeliveryOrderController::class, 'submitCheck'])->name('delivery_orders.check.submit');
    Route::post('delivery_orders/{delivery_order}/good_issue', [DeliveryOrderController::class, 'goodIssue'])->name('delivery_orders.good_issue');
    Route::get('delivery_orders/{delivery_order}/report', [DeliveryOrderController::class, 'report'])->name('delivery_orders.report');
});
