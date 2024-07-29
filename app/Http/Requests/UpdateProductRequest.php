<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string' , 'max:255'],
            'description' => ['nullable' , 'string'],
            'price'=> ['nullable' , 'numeric'],
            'category_id'=>['nullable' , 'exists:categories,id'],
            'images' => ['nullable'],
            'images.*' => ['nullable','image','mimes:jpeg,png,jpg,gif,svg'],

        ];
    }
}
