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
            'code'          => 200,
            'categories'    => CategoryProductCollection::make($categories),
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
        $childCategories = CategoryProduct::where('parent_id', $category->id)->get();

        if (empty($category)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Категория не найдена."
                ],
            ])->setStatusCode(404);
        }

        return response()->json([
            'code'              => 200,
            'category'          => CategoryProductResource::make($category),
            'child_categories'  => CategoryProductResource::collection($childCategories),
            'products'          => ProductResource::collection($category->products),
        ]);
    }
}
