<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubSubCategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            return [
                'id' => $data->id,
                'name' => $data->name,
                'parent' => $data->sub_category_id,
                'count' => Product::where('subsubcategory_id', $data->id)->where('published', 1)->count(),
                'links' => [
                    'products' => route('products.subSubCategory', $data->id)
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
