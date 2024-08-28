<?php

namespace App\Http\Resources;

use App\Models\Review;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'photos' => json_decode($data->photos),
                    'thumbnail_image' => $data->thumbnail_img,
                    'base_price' => (float) homeBasePrice($data->id),
                    'base_discounted_price' => (float) homeDiscountedBasePrice($data->id),
                    'todays_deal' => (int) $data->todays_deal,
                    'featured' => (int) $data->featured,
                    'unit' => $data->unit,
                    'slug' => $data->slug,
                    'discount' => (float) $data->discount,
                    'discount_type' => $data->discount_type,
                    'average_rating' => number_format((double) $data->rating, 2, ".", ""),
                    'rating_count' => (integer) Review::where(['product_id' => $data->id])->count(),
                    'sales' => (int) $data->num_of_sale,
                    'links' => [
                        'details' => route('products.show', $data->id),
                        'reviews' => route('api.reviews.index', $data->id),
                        'related' => route('products.related', $data->id),
                        'top_from_seller' => route('products.topFromSeller', $data->id)
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
