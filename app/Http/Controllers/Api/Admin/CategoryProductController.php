<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\CategoryProductCreateRequest;
use App\Http\Requests\CategoryProductUpdateRequest;
use App\Http\Resources\CategoryProductCollection;
use App\Http\Resources\CategoryProductResource;
use App\Http\Resources\ProductResource;
use App\Models\CategoryProduct;
use App\Models\Product;
use Illuminate\Support\Str;

class CategoryProductController extends AdminBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories =  CategoryProduct::all();

        $data = [
            'categories' => CategoryProductCollection::make($categories),
        ];

        return $this->successResponse(200, null, $data);
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

        $category = CategoryProduct::create([
            'parent_id' => $data['parent_id'],
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'image_path' => $data['image_path'],
        ]);

        $data = [
            'category'  => CategoryProductResource::make($category),
        ];

        return $this->successResponse(200, 'Категория была создана.', $data);
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
            return $this->errorResponse(404, 'Категория не найдена.');
        }

        $childCategories = CategoryProduct::where('parent_id', $category->id)->get();

        $data = [
            'category'          => CategoryProductResource::make($category),
            'child_categories'  => CategoryProductResource::collection($childCategories),
            'products'          => ProductResource::collection($category->products),
        ];

        return $this->successResponse(200, null, $data);
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
        if (CategoryProduct::isImmutableCategory($id)) {
            return $this->errorResponse(400, 'Данную категорию изменить нельзя.');
        }

        $data = $request->all();

        $category =  CategoryProduct::where('id', $id)->first();

        if (empty($category)) {
            return $this->errorResponse(404, 'Категория не найдена.');
        }

        if ($category->id == $data["parent_id"]) {
            return $this->errorResponse(400, 'Категория не должна ссылаться сама на себя.');
        }

        $checkTitle = CategoryProduct::query()
            ->whereNotIn('id', [$id])
            ->where('title', $data['title'])
            ->count();

        if (! empty($checkTitle)) {
            return $this->errorResponse(422, 'Продукт с таким названием уже существует.');
        }

        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $checkSlug = CategoryProduct::where('id', $id)
            ->where('slug', $data['slug'])
            ->first();

        if (! empty($checkSlug)) {
            return $this->errorResponse(422, 'Продукт с такой ссылкой уже существует.');
        }

        $category->update($data);
        $category->save();

        return $this->successResponse(200, "Категория №{$id} была изменена");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CategoryProduct  $categoryProduct
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (CategoryProduct::isImmutableCategory($id)) {
            return $this->errorResponse(400, 'Данную категорию удалить нельзя');
        }

        $category =  CategoryProduct::find($id);

        if (empty($category)) {
            return $this->errorResponse(404, 'Категория не найдена.');
        }

        Product::setDefaultCategoryForProducts($id);

        $category->delete();

        $message = "Категория №{$id} была удалена. Все затронутые товары данной категории были перенесены в общую категорию.";
        return $this->successResponse(200, $message);
    }
}
