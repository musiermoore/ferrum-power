<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends AdminBaseController
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
            'code'      => 200,
            'products'  => ProductResource::collection($products),
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

        $checkSlug = Product::query()->where('slug', $data["slug"])->exists();

        if ($checkSlug) {
            return $this->errorResponse(422, "Продукт с такой ссылкой уже существует");
        }

        $product = Product::create($data);

        $data = [
            'product'  => ProductResource::make($product),
        ];

        return $this->successResponse(201, "Продукт создан.", $data);
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
            'product'  => ProductResource::make($product),
        ];

        return $this->successResponse(200, null, $data);
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
            return $this->errorResponse(404, "Продукт не найден.");
        }

        $checkProduct = Product::whereNotIn('id', [$id])
            ->where('title', $data['title'])
            ->count();

        if ($checkProduct) {
            return $this->errorResponse(422, "Продукт с таким названием уже существует.");
        }

        $product->update($data);
        $product->save();

        return $this->successResponse(200, "Продукт №{$id} был изменен.");
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
            return $this->errorResponse(404, "Продукт не найден.");
        }

        $product->delete();

        return $this->successResponse(200, "Продукт №{$id} был удален.");
    }
}
