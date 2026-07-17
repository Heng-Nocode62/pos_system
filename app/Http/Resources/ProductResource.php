<?php

namespace App\Http\Resources;

use Faker\Core\Barcode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'name'=> $this->name,
            'description'=>$this->description,
            'cost_price'=>$this->cost_price,
            'selling_price'=>$this->selling_price,
            'barcode'=>$this->barcode,
            'is_active'=>$this->is_active,
            'category'=>[
                'id'=>$this->category->id,
                'name'=>$this->category->name
            ],
            'inventory'=>[
                'quantity'=>$this->inventory?->quantity
            ]
        ];
    }
}
