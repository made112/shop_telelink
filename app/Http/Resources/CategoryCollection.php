<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'slug' => $data->slug,
                    'banner' => $data->banner,
                    'icon' => $data->icon,
                    'parent' => 0,
                    'count' => Product::where('category_id', $data->id)->where('published', 1)->count(),
                    'sub_categories'=> new SubCategoryCollection($data->subCategories),
                    'links' => [
                        'products' => route('api.products.category', $data->id),
                        'sub_categories' => route('subCategories.index', $data->id)
                    ]
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
