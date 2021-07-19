<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for user
| Without a prefix
|--------------------------------------------------------------------------
*/

Route::apiResource('/categories', \App\Http\Controllers\Api\CategoryProductController::class)->only(['index', 'show']);
Route::apiResource('/products', \App\Http\Controllers\Api\ProductController::class)->only(['index', 'show']);
Route::apiResource('/orders', \App\Http\Controllers\Api\ProductController::class)->only(['store']);


