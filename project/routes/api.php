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
    CartController,
    AuthController,
    UserController
};


Route::prefix('store-front')->group(function (){
    Route::get('index',[FrontEndController::class,'index']);
    Route::get('show-product/{id}',[FrontEndController::class,'showProduct']);
    Route::get('get-products',[FrontEndController::class,'getProducts']);
    Route::middleware('auth:api')->group(function (){
        Route::get('cart',[FrontEndController::class,'cart']);
        Route::prefix('cart')->group(function () {
            Route::post('add', [CartController::class, 'addToCart']);
            Route::post('update', [CartController::class, 'updateCart']);
            Route::post('delete', [CartController::class, 'deleteCart']);
            Route::post('clear', [CartController::class, 'deleteAllCart']);
            Route::get('items', [CartController::class, 'getCart']);
        });
    });
    Route::get('product-reviews/{id}',[FrontEndController::class,'get_product_reviews']);
    Route::get('user/get-info',[UserController::class,'getUserInfo']);
    Route::post('auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
});



