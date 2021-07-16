<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryProductCollection;
use App\Http\Resources\CategoryProductResource;
use App\Http\Resources\ProductResource;
use App\Models\CategoryProduct;

class CategoryProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories =  CategoryProduct::all();

        return response()->json([
            'categories' => CategoryProductCollection::make($categories),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CategoryProduct $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category =  CategoryProduct::find($id);

        return response()->json([
            'category'  => CategoryProductResource::make($category),
            'products'  => ProductResource::collection($category->products),
        ]);
    }
}
