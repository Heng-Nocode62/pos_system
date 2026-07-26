<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'product_id' => $this->product_id,

            'product_name' => $this->product->name,

            'quantity' => $this->quantity,

            'cost_price' => $this->cost_price,

            'line_total' => $this->line_total,

        ];
    }
}