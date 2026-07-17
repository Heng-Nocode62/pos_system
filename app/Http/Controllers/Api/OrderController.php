<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderFilterRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //

    protected OrderService $orderService;
    public function __construct(OrderService $orderService)
    {
       $this->orderService = $orderService; 
    }

    public function index(OrderFilterRequest $request){
       $orders = $this->orderService->getAll($request->validated());
         return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request){
        return $this->orderService->create($request->validated());
    }
    public function show(Order $order){
        return new OrderResource(
            $this->orderService->getDetails($order)
        );
    }
    public function cancel(Order $order){
        $this->orderService->cancel($order);
        return response()->json(['message'=>'Order canceled successfully.']);
    }
}
