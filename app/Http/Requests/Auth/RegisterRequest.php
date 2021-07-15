<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'lastname'      => 'required|string|max:64',
            'firstname'     => 'required|string|max:64',
            'patronymic'    => 'required|string|max:64',
            'login'         => 'required|string|max:32|unique:users',
            'password'      => 'required|string|max:64|confirmed',
        ];
    }
}
