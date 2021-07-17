<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'category'              => CategoryProductResource::make($this->category),
            'price'                 => $this->price,
            'stock_availability'    => $this->stock_availability,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'image_path'            => $this->image_path,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
