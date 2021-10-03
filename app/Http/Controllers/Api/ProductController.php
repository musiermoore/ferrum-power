<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products =  Product::all();

        $data = [
            'products' => ProductResource::collection($products),
        ];

        return $this->successResponse(200, null, $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product =  Product::find($id);

        if (empty($product)) {
            return $this->errorResponse(404, "Продукт не найден.");
        }

        $data = [
            'products' => ProductResource::make($product),
        ];

        return $this->successResponse(200, null, $data);
    }
}
