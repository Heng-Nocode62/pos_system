<?php

namespace App\Services;
use App\Models\Category;
class CategoryService
{

    public function getAll(){
        return Category::all();
    }

    public function create(array $data)
    {
        return Category::create($data);
    }
}