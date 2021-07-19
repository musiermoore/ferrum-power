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

    /**
     * Update the specified resource in storage.
     *
     * @param  CategoryProductUpdateRequest  $request
     * @param  \App\Models\CategoryProduct  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryProductUpdateRequest $request, $id)
    {
        if ($id == CategoryProduct::DEFAULT_CATEGORY_ID) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Главную категорию изменить нельзя."
                ],
            ])->setStatusCode(422);
        }

        $data = $request->all();

        $category =  CategoryProduct::find($id);

        if ($category->id == $data["parent_id"]) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Категория не должна ссылаться сама на себя."
                ],
            ])->setStatusCode(422);
        }

        if (empty($category)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Категория не найдена."
                ],
            ])->setStatusCode(404);
        }

        $checkTitle = CategoryProduct::query()
            ->whereNotIn('id', [$id])
            ->where('title', $data['title'])
            ->count();

        if (! empty($checkTitle)) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Продукт с таким названием уже существует."
                ],
            ])->setStatusCode(422);
        }

        $checkSlug = CategoryProduct::whereNotIn('id', [$id])
            ->where('slug', $data['slug'])
            ->count();

        if (! empty($checkSlug)) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Продукт с такой ссылкой уже существует."
                ],
            ])->setStatusCode(422);
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
            ])->setStatusCode(422);
        }

        $category =  CategoryProduct::find($id);

        if (empty($category)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Категория не найдена."
                ],
            ])->setStatusCode(404);
        }

        Product::setDefaultCategoryForProduct($id);

        $category->delete();

        return response()->json([
            'code'      => 200,
            'message'   => "Категория №{$id} была удалена. Все затронутые товары были перенесены в главную категорию.",
        ]);
    }
}
