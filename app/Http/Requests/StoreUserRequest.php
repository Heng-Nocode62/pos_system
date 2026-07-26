<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name'=>'required|string|max:255',
            'email'=>'required|string|max:255|unique:users,email',
            'password'=>'required|string|min:8',
            'role_id'=>'required|numeric|exists:roles,id',
            'address'=>'required|string|max:255',
            'phone'=>'required|string|max:255',
            'date_of_birth'=>'required|date',
            'image_url'=>'required|string'
        ];
    }
}
