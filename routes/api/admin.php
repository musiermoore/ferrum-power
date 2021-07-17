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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::get('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:api');

/*
 * Admin's routes
 */
Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
});

/*
 * Operator's routes
 */
Route::group(['middleware' => ['role:admin|operator']], function () {
    Route::resource('/categories', \App\Http\Controllers\Api\Admin\CategoryProductController::class);
    Route::resource('/products', \App\Http\Controllers\Api\Admin\ProductController::class);
    Route::resource('/orders', \App\Http\Controllers\Api\Admin\OrderController::class);
});



