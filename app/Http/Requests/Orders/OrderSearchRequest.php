<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class OrderSearchRequest extends FormRequest
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
            'name'  => ['max:32', 'regex:/^[a-zA-Z-А-ЯЁ-а-яё]*$/'],
            'phone'  => ['max:32', 'regex:/^[0-9()]*$/'],
        ];
    }
}
