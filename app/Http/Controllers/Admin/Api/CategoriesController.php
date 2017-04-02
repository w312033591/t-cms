<?php

namespace App\Http\Controllers\Admin\Api;


use App\Entities\Category;
use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Transformers\CategoryTransformer;

class CategoriesController extends ApiController
{
    public function nav()
    {

    }

    public function store(CategoryCreateRequest $request)
    {
        Category::create($request->all());
        return $this->response->noContent();
    }

    public function update(Category $category, CategoryUpdateRequest $request)
    {
        $request->performUpdate($category);
        return $this->response->noContent();
    }

    public function getTopCategories()
    {
        $topCategories = Category::topCategories()
            ->withSimpleSearch()
            ->ordered()
            ->recent()
            ->get();
        return $this->response->collection($topCategories, new CategoryTransformer())
            ->setMeta(Category::getAllowSearchFieldsMeta());

    }

    public function getChildren(Category $category)
    {
        $childrenCategories = Category::childrenCategories($category->id)
            ->withSimpleSearch()
            ->ordered()
            ->recent()
            ->get();
        return $this->response->collection($childrenCategories, new CategoryTransformer())
            ->setMeta(Category::getAllowSearchFieldsMeta());
    }
}