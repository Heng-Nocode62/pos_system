<?php
namespace App\Services;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService{
    public function create(array $data){
        return DB::transaction(function () use($data){

        $productIds = collect($data['items'])->pluck('product_id');
        if($productIds->count() !== $productIds->unique()->count()){
            throw new \Exception('Duplicate products are not allowed.');;
        }
        
        $totalAmount = 0;

        $inventories = Inventory::with('product')
                    ->whereIn('product_id',$productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('product_id');
        foreach($data['items'] as $item){
            
            $inventory = $inventories[$item['product_id']];
            if (
                    $inventory->quantity <
                    $item['quantity']
                ) {
                    throw new \Exception(
                        "{$inventory->product->name} has insufficient stock."
                    );
                }
            $totalAmount+=$inventory->product->selling_price*$item['quantity'];


        }

        $order = Order::create([
            'order_number'=>'',
            'total_amount'=>$totalAmount,
            'user_id'=>auth()->id()
        ]);

        $order->order_number = 'ORD-'.now()->format('Ymd').'-'. str_pad($order->id,5,'0',STR_PAD_LEFT);
        $order->save();
    
        foreach($data['items'] as $item){

        $inventory = $inventories[$item['product_id']];
        $product = $inventory->product;
        $lineTotal= $product->selling_price*$item['quantity'];

        OrderItem::create([
            'order_id'=>$order->id,
            'product_id'=>$product->id,
            'product_name'=>$product->name,
            'unit_price'=> $product->selling_price,
            'quantity'=> $item['quantity'],
            'line_total'=>$lineTotal,
            'status'=>'COMPLETED' // TODO: Change this to PENDING if you want to implement order processing
        
        ]);

        $inventory->decrement('quantity',$item['quantity']);

        }
        return $order->load('items');

        });
    }

    public function getAll(array $filters){
        return Order::query()
                ->with('user')
                ->when($filters['search'] ?? null,function($query,$search){
                    $query->where(
                        'order_number',
                        'like',
                        "%$search%"
                        );
                })
                ->when(
                    $filters['from_date'] ?? null,function($query,$date){
                        $query->whereDate('created_at','>=',$date);
                    }
                    )
                ->when(
                    $filters['to_date'] ?? null,function($query,$date){
                        $query->whereDate('created_at','<=',$date);
                    }
                    )
                ->when(
                    $filters['status'] ?? null,function($query,$status){
                        $query->where('status',$status);
                    }
                    )
                ->orderBy($filters['sort_by'] ?? 'created_at',
                    $filters['sord_direction'] ?? 'desc')
                ->paginate($filters['per_page'] ?? 10);

        
        
    }

    public function getDetails(Order $order){
        return $order->load(['items','user']);
    }

    public function cancel(Order $order): void{
        // if($order->status !== 'PENDING'){
        //     throw new \Exception('Only pending orders can be canceled.');
        // }
        

        DB::transaction(function () use($order){

            $order = Order::lockForUpdate()
            ->findOrFail($order->id);

            if($order->status === 'CANCELED'){
                throw new \Exception('Order is already canceled.');
            }

            $order->load('items.product.inventory');
            foreach($order->items as $item){
                $item->product
                ->inventory
                ->increment('quantity',$item->quantity);
            }
            $order->update([
                'status'=>'CANCELED'
            ]);
        });
    }

}