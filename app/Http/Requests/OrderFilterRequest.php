<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderFilterRequest extends FormRequest
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
            'search'=>[
                'nullable',
                'string',
            ],
            'from_date'=>[
                'nullable',
                'date',
            ],
            'to_date'=>[
                'nullable',
                'date',
            ],
            'sort_by'=>[
                'nullable',
                'in:created_at,total_amount,order_number',
            ],
            'sort_direction'=>[
                'nullable',
                'in:asc,desc',
            ],
            'per_page'=>[
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
            'status'=>[
                'nullable',
                'in:PENDING,COMPLETED,CANCELED',
            ]
        ];
    }
}
