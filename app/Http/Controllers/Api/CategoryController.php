<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{

    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    public function index()
    {
        return CategoryResource::collection($this->categoryService->getAll());
        
    }
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());
        return new CategoryResource($category);
    }
}
