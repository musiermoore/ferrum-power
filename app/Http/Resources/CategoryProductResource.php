<?php

namespace App\Http\Resources;

use App\Models\CategoryProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryProductResource extends JsonResource
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
            'id'            => $this->id,
            'parent_id'     => $this->parent_id,
            'parent_name'   => CategoryProduct::where('id', $this->parent_id)->first()->title ?? 'Отсутствует',
            'title'         => $this->title,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'image_path'    => $this->image_path,
        ];
    }
}
