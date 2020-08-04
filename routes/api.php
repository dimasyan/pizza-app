<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('register', 'API\RegisterController@register');

Route::get('products', 'API\ProductController@index');
Route::post('product', 'API\ProductController@create');

Route::post('order', 'API\OrderController@create');

Route::resource('cart', 'API\CartController');

Route::middleware('auth:api')->group( function () {
    Route::get('orders', 'API\OrderController@index');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
