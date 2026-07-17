<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;

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
}