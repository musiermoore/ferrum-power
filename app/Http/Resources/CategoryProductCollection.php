<?php

namespace App\Http\Resources;

use App\Models\CategoryProduct;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($category) {
            $childCategories = CategoryProduct::where('parent_id', $category->id)->get();
            return [
                'category'  => CategoryProductResource::make($category),
                'child_categories'  => CategoryProductResource::collection($childCategories),
                'products'  => ProductResource::collection($category->products),
            ];
        });
    }
}
