<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            'product_info' => ProductResource::make($this),
            'quantity' => $this->pivot->quantity,
            'full_price' => $this->pivot->full_price,
        ];
    }
}
