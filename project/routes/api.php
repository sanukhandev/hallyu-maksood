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


Route::prefix('store-front')->group(function () {
    Route::get('index', [FrontEndController::class, 'index']);
    Route::get('show-product/{id}', [FrontEndController::class, 'showProduct']);
    Route::get('get-products', [FrontEndController::class, 'getProducts']);
    Route::middleware('auth:api')->group(function () {
        Route::get('cart', [FrontEndController::class, 'cart']);
        Route::prefix('cart')->group(function () {
            Route::post('add', [CartController::class, 'addToCart']);
            Route::post('update', [CartController::class, 'updateCart']);
            Route::post('delete', [CartController::class, 'deleteCart']);
            Route::post('clear', [CartController::class, 'deleteAllCart']);
            Route::get('items', [CartController::class, 'getCart']);
            Route::post('checkout', [CartController::class, 'checkout_cod']);
            Route::get('order-options',[CartController::class,'get_order_options']);
            Route::post('apply-coupon',[CartController::class,'apply_coupon']);
        });
        Route::get('user/get-info', [UserController::class, 'getUserInfo']);
        Route::post('add-review', [CartController::class, 'add_review_by_product_id']);
        Route::post('user/delete-my-account', [AuthController::class, 'deleteAccount']);
    });
    Route::get('product-reviews/{id}', [FrontEndController::class, 'get_product_reviews']);
    Route::group(['middleware' => ['api', 'web']], function () {
        Route::post('auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    });
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/verify-otp',[AuthController::class, 'validate_or_send_otp']);
});



