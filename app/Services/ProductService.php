<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;


class ProductService{

    public function getAll(array $filters): LengthAwarePaginator{


        $search = $filters['search'] ?? null;
        $active =$filters['active'] ?? null;
        $sort = $filters['sort'] ?? 'id';
        $direction = $filters['direction']?? 'asc';
        $perPage = $filters['per_page'] ?? 10;
        $category_id = $filters['category_id'] ?? null;
    
        $allowedSorts = [
            'id',
            'name',
            'selling_price',
            'created_at'
        ];
        $direction = strtolower($direction)=== 'desc' ? 'desc':'asc';
        $sort = in_array($sort, $allowedSorts) ? $sort:'id';


        return Product::query()
                ->with(['category','inventory'])
                ->when($category_id, fn ($query)=>
                                    $query->where('category_id',$category_id))
                ->when($search,fn ($query)=>
                                $query->where('name','ilike',"%{$search}%"
                                )
                                ->orWhere('barcode','ilike',"%{$search}%",)
                        )
    
                ->when(!is_null($active),fn ($query)=>
                            $query->where('is_active',$active)
                    )
                ->orderBy($sort,$direction)
                ->paginate($perPage);

    }

    // public function create(array $data){
    //     return Product::create($data);
    // }
    public function create(array $data){
        return DB::transaction(function () use($data) {
        
        

        $product= Product::create($data);
        Inventory::create([
            "product_id"=>$product->id,
            "quantity"=>0
        ]);
            return $product;
        });
    }
    public function uploadProductImage(array $data){
        if(isset($data['image'])){
            $data['image'] = $data['image']->store('products','public');
        }
        return $data['image'];
    }

    public function update(Product $product, array $data): Product{
        return DB::transaction(function () use($product,$data){
            $product->update($data);
            return $product->load(['category','inventory']);
        });
    }

    public function delete(Product $product){
        $product->delete();
    }

}
