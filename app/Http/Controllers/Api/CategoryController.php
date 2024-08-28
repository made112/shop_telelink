<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryCollection;
use App\Models\BusinessSetting;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
//
        return new CategoryCollection(Category::with('subCategories')->get());
    }

    public function featured(Request $request)
    {
//
        return new CategoryCollection(Category::where('featured', 1)->with('subCategories')->orderBy('created_at','asc')->get());
    }

    public function home(Request $request)
    {
//
        $homepageCategories = BusinessSetting::where('type', 'category_homepage')->first();
        $homepageCategories = json_decode($homepageCategories->value);
        $categories = json_decode($homepageCategories->category);
        return new CategoryCollection(Category::find($categories)->with('subCategories'));
    }
}
