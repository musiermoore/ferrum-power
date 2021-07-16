<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryProductCollection;
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
            'category'  => CategoryProductCollection::make($category),
        ]);
    }
}
