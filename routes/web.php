<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\GoodReceiveController;
use App\Http\Controllers\MovementOrderController;
use App\Http\Controllers\InboundDeliveryController;
use App\Http\Controllers\MovementOrderDetailController;
use App\Http\Controllers\InboundDeliveryDetailController;

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

Route::get('/', function () {
    return inertia()->render('Dashboard');
});

Route::resource('/products', ProductController::class);
Route::resource('/clients', ClientController::class);
Route::resource('/locations', LocationController::class);

Route::resource('inbounds', InboundDeliveryController::class);
Route::resource('inbound_details', InboundDeliveryDetailController::class)
    ->parameter('inbound_details', 'detail')
    ->only('store', 'update', 'destroy');

Route::resource('grs', GoodReceiveController::class)
    ->parameter('grs', 'good_receive')
    ->except('create');
Route::get('grs/{inbound}/create', [GoodReceiveController::class, 'create'])->name('grs.create');
Route::post('grs/inbound/search', [GoodReceiveController::class, 'searchInbound'])->name('grs.inbound.search');

Route::get('grs/{good_receive}/check', [GoodReceiveController::class, 'check'])->name('grs.check');
Route::post('grs/{good_receive}/check', [GoodReceiveController::class, 'submitCheck'])->name('grs.check.submit');
Route::post('grs/{good_receive}/receive', [GoodReceiveController::class, 'receive'])->name('grs.receive');

Route::get('inventories', [InventoryController::class, 'index'])->name('inventories.index');

Route::resource('movement_orders', MovementOrderController::class)->except('create');
Route::get('movement_orders/{type}/{document_id}/create', [MovementOrderController::class, 'create'])->name('movement_orders.create');
Route::post('movement_orders/document/search', [MovementOrderController::class, 'searchDocument'])->name('movement_orders.document.search');
Route::post('movement_orders/process/confirm', [MovementOrderController::class, 'confirm'])->name('movement_orders.process.confirm');
Route::post('movement_orders/process/cancel', [MovementOrderController::class, 'cancel'])->name('movement_orders.process.cancel');

Route::get('movement_order_details/{movement_order}/create', [MovementOrderDetailController::class, 'create'])->name('movement_order_details.create');
Route::post('movement_order_details/{movement_order}', [MovementOrderDetailController::class, 'store'])->name('movement_order_details.store');
