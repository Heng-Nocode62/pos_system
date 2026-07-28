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


     public function stats(): array
    {
        $today = now()->toDateString();

        return [

            // Sales today
            'total_sales_today' => (string) Order::whereDate('created_at', $today)
                ->where('status', 'COMPLETED')
                ->sum('total_amount'),

            // Orders today
            'total_orders_today' => Order::whereDate('created_at', $today)
                ->where('status', 'COMPLETED')
                ->count(),

            // Products
            'total_products' => Product::count(),

            // Low stock count (<= 10)
            'low_stock_count' => Inventory::where('quantity', '<=', 10)->count(),

            // Customers (you don't have customers table yet)
            'total_customers' => 0,

            // Suppliers
            'total_suppliers' => Supplier::count(),

            // Last 7 days sales trend
            'sales_trend' => Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->where('status', 'COMPLETED')
                ->whereDate('created_at', '>=', now()->subDays(6))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn ($row) => [
                    'date' => $row->date,
                    'total' => (string) $row->total,
                ])
                ->values(),

            // Recent orders
            'recent_orders' => Order::latest()
                ->take(5)
                ->get(['id', 'order_number', 'total_amount', 'created_at'])
                ->map(fn ($order) => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => (string) $order->total_amount,
                    'created_at' => $order->created_at,
                ])
                ->values(),

            // Low stock products
            'low_stock_products' => Product::query()
                ->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->where('inventories.quantity', '<=', 10)
                ->orderBy('inventories.quantity')
                ->take(5)
                ->get([
                    'products.id',
                    'products.name',
                    'inventories.quantity',
                ])
                ->map(fn ($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $product->quantity,
                ])
                ->values(),

        ];
    }
}