<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\GoodReceiveController;
use App\Http\Controllers\InboundDeliveryController;
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
