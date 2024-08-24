<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


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
    // public function rules(): array
    // {
        
    //     return [
        //         'productData'=>[],
        //         'title' => ['nullable', 'string' , 'max:255', Rule::unique('products')->ignore($product->id)],
        //         'description' => ['nullable' , 'string'],
        //         'price'=> ['nullable' , 'numeric'],
        //         'category_id'=>['nullable' , 'exists:categories,id'],
        //         'images' => ['required'],
        //     ];
        // }
        public function rules(): array
        {
            // $product = $this->route('product');
            $productId = $this->input('productData.id');
            // dd(gettype($productId) , gettype($product->id));

            return [
                'productData.id' => [
                    'required', 
                    'integer', 
                    Rule::exists('products', 'id')
                ],
                'productData.title' => [
                    'nullable', 
                    'string', 
                    'max:255', 
                    Rule::unique('products', 'title')->ignore($productId)
                ],
                'productData.description' => [
                    'nullable', 
                    'string'
                ],
                'productData.price' => [
                    'nullable', 
                    'numeric'
                ],
                'productData.category_id' => [
                    'nullable', 
                    'exists:categories,id'
                ],
                'images' => [
                    'required', 
                    'array'
                ],
                
            ];
        }

}

