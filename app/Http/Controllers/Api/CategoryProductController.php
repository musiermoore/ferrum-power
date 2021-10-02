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

        return $this->successResponse(200, null, $categories);
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

        if (empty($category)) {
            return $this->errorResponse(404, "Категория не найдена.");
        }

        $childCategories = CategoryProduct::where('parent_id', $category->id)->get();

        $data = [
            'category'          => CategoryProductResource::make($category),
            'child_categories'  => CategoryProductResource::collection($childCategories),
            'products'          => ProductResource::collection($category->products),
        ];

        return $this->successResponse(200, null, $data);
    }
}
