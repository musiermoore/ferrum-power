<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id'           => ['required', 'exists:category_products,id'],
            'title'                 => ['required', 'string', 'min:2', 'max:128', 'unique:products,title'],
            'slug'                  => ['nullable', 'string', 'min:2', 'max:128', 'unique:products,slug'],
            'description'           => ['string', 'max:255'],
            'image_path'            => ['nullable', 'string'],
            'stock_availability'    => ['required', 'boolean'],
            'price'                 => ['required', 'integer'],
        ];
    }
}
