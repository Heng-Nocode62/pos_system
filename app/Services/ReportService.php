<?php
namespace App\Services;

use App\Models\Order;

class ReportService{
    public function dailySales(){
        $today = now()->toDateString();
        return [
            'date'=>$today,
            'total_orders'=>Order::whereDate('created_at', $today)->count(),
            'total_amounts'=>Order::whereDate('created_at',$today)->sum('total_amount')

        ];
    }

}