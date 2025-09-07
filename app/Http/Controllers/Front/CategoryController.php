<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Category;

class CategoryController extends Controller
{

    public function show(Category $category)
    {
        $companies = $category->companies()->where('is_published', true)->paginate(24);

        // v6: ancestorsAndSelf() returns a Collection → sort by _lft to mimic defaultOrder()
        $breadcrumbs = Category::ancestorsAndSelf($category->getKey())
                               ->sortBy('_lft')
                               ->values();

        // optional: immediate children for “Subcategories”
        $children = $category->children()->defaultOrder()->get();

        return view('front.category-show', compact('category','companies','breadcrumbs','children'));
    }
}