<?php

use Illuminate\Http\Request;

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

// store-front apis mobile
use App\Http\Controllers\Api\{
    FrontEndController,
    CartController
};


Route::prefix('store-front')->group(function (){
    Route::get('index',[FrontEndController::class,'index']);
    Route::get('show-product/{id}',[FrontEndController::class,'showProduct']);
    Route::get('get-products',[FrontEndController::class,'getProducts']);
    Route::get('cart',[FrontEndController::class,'cart']);
    Route::prefix('cart')->group(function () {
        Route::post('add', [CartController::class, 'addToCart']);
        Route::post('update', [CartController::class, 'updateCart']);
        Route::post('delete', [CartController::class, 'deleteCart']);
        Route::post('clear', [CartController::class, 'deleteAllCart']);
        Route::get('items', [CartController::class, 'getCart']);
    });
});
