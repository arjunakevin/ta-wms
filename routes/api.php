<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoodReceiveController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/clients', [ClientController::class, 'list'])->name('clients.list');
Route::get('/products', [ProductController::class, 'list'])->name('products.list');
Route::get('/locations', [LocationController::class, 'list'])->name('locations.list');

Route::get('/dashboard', [DashboardController::class, 'appDashboardData'])->name('app.dashboard.data');
Route::get('/gr_check/{good_receive}/status', [GoodReceiveController::class, 'appGoodReceiveCheckSearch'])->name('app.inbound_check.search');
Route::get('/gr_check/{good_receive}/data', [GoodReceiveController::class, 'appGoodReceiveCheckData'])->name('app.inbound_check.get');
Route::post('/gr_check/{good_receive}/check', [GoodReceiveController::class, 'submitCheck'])->name('app.inbound_check.submit');