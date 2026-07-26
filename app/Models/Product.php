<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;


    protected $casts=[
            'is_active'=>'boolean',
   ];
   protected $fillable =[
            'name',
            'description',
            'selling_price',
            'cost_price',
            'barcode',
            'is_active',
            'category_id',
            'image_url'
   ];

   
   public function category(){
    return $this->belongsTo(Category::class);
   }
   public function inventory(){
    return $this->hasOne(Inventory::class);
   }
   public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
   
    
}
