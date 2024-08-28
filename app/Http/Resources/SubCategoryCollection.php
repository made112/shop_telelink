<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubCategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function($data) {
            return [
                'id' => $data->id,
                'name' => $data->name,
                'parent' => $data->category_id,
                'count' => Product::where('subcategory_id', $data->id)->where('published', 1)->count(),
                'sub_sub_categories' => (new SubSubCategoryCollection($data->subSubCategories)),
                'links' => [
                    'products' => route('products.subCategory', $data->id)
                ]
            ];
        });
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
