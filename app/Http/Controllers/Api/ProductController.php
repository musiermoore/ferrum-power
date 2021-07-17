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

        return response()->json([
            'products' => ProductResource::collection($products),
        ]);
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
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Продукт не найден."
                ],
            ]);
        }

        return response()->json([
            'code'      => 200,
            'product'  => ProductResource::make($product),
        ]);
    }
}
