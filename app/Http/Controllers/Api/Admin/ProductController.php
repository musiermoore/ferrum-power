<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Str;

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
     * Store a newly created resource in storage.
     *
     * @param ProductCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductCreateRequest $request)
    {
        $data = $request->all();
        if (empty($request->slug)) {
            $data["slug"] = Str::slug($data["title"]);
        }

        $product = Product::create($data);

        return response()->json([
            'code'      => 201,
            'product'  => ProductResource::make($product),
        ])->setStatusCode(201);
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
            ])->setStatusCode(404);
        }

        return response()->json([
            'code'      => 200,
            'product'  => ProductResource::make($product),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProductUpdateRequest  $request
     * @param  \App\Models\Product  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        $data = $request->all();

        $product =  Product::find($id);

        if (empty($product)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Продукт не найден."
                ],
            ])->setStatusCode(404);
        }

        $checkProduct = Product::whereNotIn('id', [$id])
            ->where('title', $data['title'])
            ->count();

        if ($checkProduct) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Продукт с таким названием уже существует."
                ],
            ])->setStatusCode(422);
        }

        $product->update($data);
        $product->save();

        return response()->json([
            'code'      => 200,
            'message'   => "Продукт №{$id} был изменен",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product =  Product::find($id);

        if (empty($product)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Продукт не найден."
                ],
            ])->setStatusCode(404);
        }

        $product->delete();

        return response()->json([
            'code'      => 200,
            'message'   => "Продукт №{$id} был удален.",
        ]);
    }
}
