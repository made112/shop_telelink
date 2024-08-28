<?php

namespace App;

use App\Product;
use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Auth;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $row['category_id'] = check_text_ar($row['category_id']);
        $row['subcategory_id'] = check_text_ar($row['subcategory_id']);
        $row['subsubcategory_id'] = check_text_ar($row['subsubcategory_id']);
        $row['brand_id'] = check_text_ar($row['brand_id']);
        
        $category = Category::where('id', $row['category_id'])->orWhere('name', 'like', '%'.$row['category_id'].'%')->first();
        if ($category) {
            $category = $category->id;
        }else $category = '';

        $subcategory = SubCategory::where('id', $row['subcategory_id'])->orWhere('name', 'like', '%'.$row['subcategory_id'].'%')->first();
        if ($subcategory) {
            $subcategory = $subcategory->id;
        }else $subcategory = '';

        $subsubcategory = SubSubCategory::where('id', $row['subsubcategory_id'])->orWhere('name', 'like', '%'.$row['subsubcategory_id'].'%')->first();
        if ($subsubcategory) {
            $subsubcategory = $subsubcategory->id;
        }else $subsubcategory = '';

        $brand = Brand::where('id', $row['brand_id'])->orWhere('name', 'like', '%'.$row['brand_id'].'%')->first();
        if ($brand) {
            $brand = $brand->id;
        }else $brand = '';

        return new Product([
           'name'     => $row['name'],
           'added_by'    => Auth::user()->user_type == 'seller' ? 'seller' : 'admin',
           'user_id'    => Auth::user()->user_type == 'seller' ? Auth::user()->id : User::where('user_type', 'admin')->first()->id,
           'category_id'    => $category,
           'subcategory_id'    => $subcategory,
           'subsubcategory_id'    => $subsubcategory,
           'brand_id'    => $brand,
           'video_provider'    => $row['video_provider'],
           'video_link'    => $row['video_link'],
           'unit_price'    => $row['unit_price'],
           'purchase_price'    => $row['purchase_price'],
           'unit'    => $row['unit'],
           'current_stock' => $row['current_stock'],
           'meta_title' => $row['meta_title'],
           'meta_description' => $row['meta_description'],
           'colors' => json_encode(array()),
           'choice_options' => json_encode(array()),
           'variations' => json_encode(array()),
           'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $row['slug'])).'-'.\Str::random(5),
        ]);
    }

    public function rules(): array
    {
        return [
             // Can also use callback validation rules
             'unit_price' => function($attribute, $value, $onFailure) {
                  if (!is_numeric($value)) {
                       $onFailure('Unit price is not numeric');
                  }
              }
        ];
    }
}
