<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Category;
use App\Models\Back\Catalog\Company;

class HomeController extends Controller
{

    public function index()
    {
        $featured = Company::query()->where('is_published', true)->latest()->take(12)->get();
        $cats     = Category::defaultOrder()->get()->toTree();

        return view('front.home', compact('featured', 'cats'));
    }
}