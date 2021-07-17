<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for user
| Without a prefix
|--------------------------------------------------------------------------
*/

Route::resource('/categories', \App\Http\Controllers\Api\CategoryProductController::class)->only(['index', 'show']);
Route::resource('/products', \App\Http\Controllers\Api\ProductController::class)->only(['index', 'show']);


