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
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryProductController extends Controller
{
    const DEFAULT_CATEGORY = 1;

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
     * @param  \Illuminate\Http\CategoryProductCreateRequest  $request
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
            'category'  => CategoryProductCollection::make($category),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\CategoryProductUpdateRequest  $request
     * @param  \App\Models\CategoryProduct  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryProductUpdateRequest $request, $id)
    {
        $data = $request->all();

        $category =  CategoryProduct::find($id);

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
        $category =  CategoryProduct::find($id);

        if (empty($category)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Категория не найдена."
                ],
            ]);
        }

        $products = Product::where('category_id', $id)->update(['category_id' => self::DEFAULT_CATEGORY]);

        $category->delete();

        return response()->json([
            'code'      => 200,
            'message'   => "Категория №{$id} была удалена. Все затронутые товары были перенесены в главную категорию.",
        ]);
    }
}
