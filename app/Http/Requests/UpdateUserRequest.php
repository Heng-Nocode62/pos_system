<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name'=>['sometimes','string'],
            'email'=>[
                'sometimes',
                'email',
                Rule::unique('users')->ignore($this->user()->id)
            ],
            'role_id'=>[
                'sometimes',
                'exists:roles,id'
            ],
            'image_url'=>['sometimes','string'],
            'phone'=>[
                'sometimes',
                'string',
                Rule::unique('users')->ignore($this->route('user'))
            ],
            'date_of_birth'=>['sometimes','date'],
            'address'=>['sometimes'=>'string']
        ];
    }
}
