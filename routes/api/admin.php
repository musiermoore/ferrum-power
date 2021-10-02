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


Route::get('/user/auth', [\App\Http\Controllers\Api\AuthController::class, 'getUser'])
    ->middleware(['auth:api'])
    ->name('user.auth');
Route::post('/user/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])
    ->name('user.login');;
Route::post('/user/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])
    ->middleware('auth:api')
    ->name('user.logout');;

/*
 * Admin's routes
 */
Route::group(['middleware' => ['role:admin']], function () {
    Route::post('/user/register', [\App\Http\Controllers\Api\AuthController::class, 'register'])->name('user.register');
    Route::get('/roles', [\App\Http\Controllers\Api\RoleController::class, 'getListRoles']);
    Route::group(['prefix' => '/orders/{orderId}'], function () {
        Route::patch('/operator/change', [\App\Http\Controllers\Api\Admin\OrderController::class, 'changeOrderOperator'])
            ->name('orders.operator.change');
    });
});

/*
 * Operator's routes
 */
Route::group(['middleware' => ['role:admin|operator']], function () {
    Route::apiResource('/categories', \App\Http\Controllers\Api\Admin\CategoryProductController::class);
    Route::apiResource('/products', \App\Http\Controllers\Api\Admin\ProductController::class);
    Route::apiResource('/orders', \App\Http\Controllers\Api\Admin\OrderController::class)->except('store');
    Route::apiResource('/users', \App\Http\Controllers\Api\Admin\UserController::class)->except('store');

    Route::group(['prefix' => '/orders/{orderId}'], function () {
        Route::apiResource('/products', \App\Http\Controllers\Api\Admin\OrderProductController::class, ['parameters' => [
            'products' => 'productId',
        ]])->except('show');
        Route::patch('/operator/set', [\App\Http\Controllers\Api\Admin\OrderController::class, 'setOperatorToOrder'])
            ->name('orders.operator.set');;
        Route::patch('/operator/unset', [\App\Http\Controllers\Api\Admin\OrderController::class, 'unsetOperatorToOrder'])
            ->name('orders.operator.unset');;
    });
});



