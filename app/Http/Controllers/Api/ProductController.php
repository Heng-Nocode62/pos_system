<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected SupabaseStorageService $storage;

    public function __construct(ProductService $productService,SupabaseStorageService $storage)
    {
        $this->productService=$productService;
        $this ->storage = $storage;
    }
    
    public function index(ProductIndexRequest $request){
        $validatedRequest = $request->validated();
    
       $product= $this->productService->getAll($request->validated());

        return ProductResource::collection($product);
    }

    public function store(StoreProductRequest $request){

        $product = $this->productService->create($request->validated());
        $product->refresh();
        return new ProductResource($product->load('category'));
    }
    public function update(UpdateProductRequest $request, Product $product){
        $product = $this->productService->update($product,$request->validated());
        return new ProductResource($product);
    }

    // TODO check logic later
    public function destroy(Product $product){

    $this->productService->delete($product);
    }

    public function storeImage(Request $request)
        {
            $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5012'
        ]);
        $url = $this->storage->upload($request->file('image'));
    return response()->json([
        'image_url'=>$url
    ],201);
}
}
