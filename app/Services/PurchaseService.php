<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseService
{
    public function store(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Prevent duplicate products
            |--------------------------------------------------------------------------
            */

            $productIds = collect($data['items'])
                ->pluck('product_id');

            if ($productIds->count() !== $productIds->unique()->count()) {
                throw new Exception('Duplicate product detected.');
            }

            /*
            |--------------------------------------------------------------------------
            | Load products
            |--------------------------------------------------------------------------
            */

            $products = Product::with('inventory')
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            /*
            |--------------------------------------------------------------------------
            | Calculate total
            |--------------------------------------------------------------------------
            */

            $totalAmount = 0;

            foreach ($data['items'] as $item) {

                $product = $products[$item['product_id']]
                    ?? throw new Exception('Product not found.');

                $lineTotal =
                    $item['quantity'] *
                    $item['cost_price'];

                $totalAmount += $lineTotal;
            }

            /*
            |--------------------------------------------------------------------------
            | Create Purchase
            |--------------------------------------------------------------------------
            */

            $purchase = Purchase::create([

                'purchase_number' => $this->generatePurchaseNumber(),

                'supplier_id' => $data['supplier_id'],

                'user_id' => auth()->id(),

                'total_amount' => $totalAmount,

                'status' => 'COMPLETED'

            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Purchase Items
            |--------------------------------------------------------------------------
            */

            foreach ($data['items'] as $item) {

                $lineTotal =
                    $item['quantity'] *
                    $item['cost_price'];

                $purchase->items()->create([

                    'product_id' => $item['product_id'],

                    'quantity' => $item['quantity'],

                    'cost_price' => $item['cost_price'],

                    'line_total' => $lineTotal

                ]);

                /*
                |--------------------------------------------------------------------------
                | Update inventory
                |--------------------------------------------------------------------------
                */

                $inventory = $products[$item['product_id']]
                    ->inventory;

                if (!$inventory) {

                    $inventory = Inventory::create([

                        'product_id' => $item['product_id'],

                        'quantity' => 0

                    ]);

                }

                $inventory->increment(
                    'quantity',
                    $item['quantity']
                );

                /*
                |--------------------------------------------------------------------------
                | Update product cost price
                |--------------------------------------------------------------------------
                */

                $products[$item['product_id']]
                    ->update([

                        'cost_price' => $item['cost_price']

                    ]);

            }

            return $purchase->load([
                'supplier',
                'user',
                'items.product'
            ]);

        });
    }

    private function generatePurchaseNumber(): string
    {
        return sprintf(
            'PUR-%s-%05d',
            now()->format('Ymd'),
            Purchase::count() + 1
        );
    }
}