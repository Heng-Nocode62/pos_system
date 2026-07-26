<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'purchase_number' => $this->purchase_number,

            'supplier' => new SupplierResource(
                $this->supplier
            ),

            'received_by' => $this->user->name,

            'total_amount' => $this->total_amount,

            'status' => $this->status,

            'created_at' => $this->created_at,

            'items' => PurchaseItemResource::collection(
                $this->items
            )

        ];
    }
}