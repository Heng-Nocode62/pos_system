<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Resources\PurchaseDetailResource;
use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use App\Services\PurchaseService;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService
    ) {}

    public function index()
    {
        $purchases = Purchase::with([
                'supplier',
                'user'
            ])
            ->latest()
            ->paginate(15);

        return PurchaseResource::collection($purchases);
    }

    public function store(StorePurchaseRequest $request)
    {
        $purchase = $this->purchaseService
            ->store($request->validated());

        return new PurchaseDetailResource($purchase);
    }

    public function show(Purchase $purchase)
    {
        return new PurchaseDetailResource(
            $purchase->load([
                'supplier',
                'user',
                'items.product'
            ])
        );
    }
}
