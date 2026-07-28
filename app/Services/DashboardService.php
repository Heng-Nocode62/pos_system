<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;

 class DashboardService{
    public function dashboard(){
        return [
            'total_sales'=>Order::where('status','COMPLETED')->whereDate('created_at',today())->sum('total_amount'),
            'today_orders'=>Order::where('status','COMPLETED')->count(),
            'monthly_sales'=>Order::where('status','COMPLETED')->whereDate('created_at',now()->month)->sum('total_amount'),
            'monthly_orders'=>Order::where('status','COMPLETED')->whereDate('created_at',now()->month)->count(),
            'low_stock_products'=>Product::whereHas('inventory', function($query){
                $query->where('quantity','<',5);
            })->count()
        ];

    }


     public function getStats(): array
    {
        $today = today();

        return [

            'total_sales_today' => (string) Order::whereDate(
                'created_at',
                $today
            )->where('status', 'COMPLETED')
             ->sum('total_amount'),

            'total_orders_today' => Order::whereDate(
                'created_at',
                $today
            )->where('status', 'COMPLETED')
             ->count(),

            'total_products' => Product::count(),

            'low_stock_count' => Inventory::where(
                'quantity',
                '<=',
                10
            )->count(),

            // You don't have customers yet, so return 0 for now
            'total_customers' => 0,

            'total_suppliers' => Supplier::count(),

            'sales_trend' => $this->getSalesTrend(),

            'recent_orders' => $this->getRecentOrders(),

            'low_stock_products' => $this->getLowStockProducts(),

        ];
    }

    private function getSalesTrend(): array
    {
        return Order::query()
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(total_amount) as total')
            ->where('status', 'COMPLETED')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'total' => (string) $row->total,
            ])
            ->values()
            ->all();
    }

    private function getRecentOrders(): array
    {
        return Order::query()
            ->select([
                'id',
                'order_number',
                'total_amount',
                'created_at',
            ])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => (string) $order->total_amount,
                'created_at' => $order->created_at->toISOString(),
            ])
            ->values()
            ->all();
    }

    private function getLowStockProducts(): array
    {
        return Product::query()
            ->join('inventories', 'products.id', '=', 'inventories.product_id')
            ->where('inventories.quantity', '<=', 10)
            ->orderBy('inventories.quantity')
            ->limit(5)
            ->get([
                'products.id',
                'products.name',
                'inventories.quantity',
            ])
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => (int) $product->quantity,
            ])
            ->values()
            ->all();
    }
}