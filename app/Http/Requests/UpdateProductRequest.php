<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        return [
            'name'=>[
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id)
            ],
            'description'=>[
                'sometimes',
                'nullable',
                'string'
            ],
            'barcode'=>[
                'sometimes',
                'nullable',
                'string',
                Rule::unique('products')->ignore($product->id)
            ],
            'cost_price'=>[
                'sometimes',
                'numeric',
                'min:0'
            ],
            'selling_price'=>[
                'sometimes',
                'numeric',
                'min:0'
            ],
            'category_id'=>[
                'sometimes',
                'exists:categories,id'
            ],
            'is_active'=>[
                'sometimes',
                'boolean'
            ]
        ];
    }
}
