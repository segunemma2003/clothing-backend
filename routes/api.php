<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductOrderController;
use App\Http\Controllers\ShippingOrderController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
});


Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/products/{id}', 'show');
    Route::post('/products/create', 'store');
    Route::post('/products/update/{id}','update');
    Route::post('/products/delete/{id}', 'deleteProduct');
});


Route::controller(OrderController::class)->group(function () {
    Route::get('/orders', 'index');
    Route::group(['middleware'=>'auth:sanctum'], function(){
        Route::get('/orders/user', 'indexUser');
        Route::post('/orders/create', 'store');
        Route::post('/orders/update/{id}','update');
        Route::post('orders/status/update/{id}', 'updateOrderStatus');
    Route::post('/orders/delete/{id}', 'destroy');
    });

    Route::get('/orders/{id}', 'show');


});


Route::controller(ProductOrderController::class)->group(function () {
    Route::get('/product_orders', 'index');
    Route::group(['middleware'=>'auth:sanctum'], function(){
        Route::get('/product_orders/user', 'indexUser');
        Route::post('/product_orders/create', 'store');
        Route::post('/product_orders/update/{id}','update');
        Route::post('/product_orders/delete/{id}', 'destroy');
        Route::post('orders/status/update/{id}', 'updateOrderStatus');
    });

    Route::get('/product_orders/{id}', 'show');


});


Route::controller(ShippingOrderController::class)->group(function () {
    Route::get('/shipping', 'index');
    Route::group(['middleware'=>['auth:sanctum']], function(){
        Route::get('/shipping/user', 'indexUser');
        Route::post('/shipping/create', 'store');
        Route::post('/shipping/update/{id}','update');
        Route::post('/shipping/delete/{id}', 'destroy');
    });

    Route::get('/shipping/{id}', 'show');


});
