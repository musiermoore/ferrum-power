<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryProductCreateRequest;
use App\Http\Requests\CategoryProductUpdateRequest;
use App\Http\Resources\CategoryProductCollection;
use App\Http\Resources\CategoryProductResource;
use App\Http\Resources\ProductResource;
use App\Models\CategoryProduct;
use App\Models\Product;
use Illuminate\Support\Str;

class CategoryProductController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param CategoryProductCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryProductCreateRequest $request)
    {
        $data = $request->all();
        if (empty($request->slug)) {
            $data["slug"] = Str::slug($data["title"]);
        }

        $category = CategoryProduct::create($data);

        return response()->json([
            'code'      => 201,
            'category'  => CategoryProductResource::make($category),
        ])->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CategoryProduct  $categoryProduct
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category =  CategoryProduct::find($id);

        if (empty($category)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Категория не найдена."
                ],
            ]);
        }

        return response()->json([
            'code'      => 200,
            'category'  => CategoryProductResource::make($category),
            'products'  => ProductResource::collection($category->products),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CategoryProductUpdateRequest  $request
     * @param  \App\Models\CategoryProduct  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryProductUpdateRequest $request, $id)
    {
        $data = $request->all();

        $category =  CategoryProduct::find($id);

        if ($category->id == $data["parent_id"]) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Категория не должна ссылаться сама на себя."
                ],
            ]);
        }

        if (empty($category)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Категория не найдена."
                ],
            ]);
        }

        $category->update($data);
        $category->save();

        return response()->json([
            'code'      => 200,
            'message'   => "Категория №{$id} была изменена",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CategoryProduct  $categoryProduct
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if ($id == CategoryProduct::DEFAULT_CATEGORY_ID) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Главную категорию удалить нельзя."
                ],
            ]);
        }

        $category =  CategoryProduct::find($id);

        if (empty($category)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Категория не найдена."
                ],
            ]);
        }

        Product::setDefaultCategoryForProduct($id);

        $category->delete();

        return response()->json([
            'code'      => 200,
            'message'   => "Категория №{$id} была удалена. Все затронутые товары были перенесены в главную категорию.",
        ]);
    }
}
