<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        return SupplierResource::collection(
            Supplier::latest()->paginate(15)
        );
    }

    public function store(StoreSupplierRequest $request)
    {
        $supplier = Supplier::create(
            $request->validated()
        );

        return new SupplierResource($supplier);
    }

    public function show(Supplier $supplier)
    {
        return new SupplierResource($supplier);
    }

    public function update(
        UpdateSupplierRequest $request,
        Supplier $supplier
    ) {
        $supplier->update(
            $request->validated()
        );

        return new SupplierResource($supplier);
    }
}
