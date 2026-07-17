<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        return [
            'name'=>'required|string|max:255|unique:products,name',
            'description'=>'nullable|string',
            'cost_price'=>'required|numeric|min:0',
            'selling_price'=>'required|numeric|min:0',
            'barcode' =>'nullable|string|unique:products,barcode',
            'is_active'=>'boolean',
            'category_id'=>'required|exists:categories,id'
        ];
    }
}
