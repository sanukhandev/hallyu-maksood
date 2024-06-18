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
use App\Http\Controllers\Api\FrontEndController;
Route::prefix('store-front')->group(function (){
    Route::get('index',[FrontEndController::class,'index']);
    Route::get('show-product/{id}',[FrontEndController::class,'showProduct']);
    Route::post('search-product',[FrontEndController::class,'searchProduct']);
    Route::post('search-product-by-category',[FrontEndController::class,'searchProductByCategory']);

});
