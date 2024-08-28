<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShopCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $seller = \App\Seller::where('user_id', $data->user_id)->first();
                $total = 0;
                $rating = 0;
                $average_rating = "0";
                if($seller){
                    foreach ($seller->user->products as $key => $seller_product) {
                        $total += $seller_product->reviews->count();
                        $rating += $seller_product->reviews->sum('rating');
                    }
                    if($total > 0){
                        $average_rating = number_format((double)($rating / $total),2, '.', '');
                    }
                }
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'user' => [
                        'id' => $data->user->id,
                        'name' => $data->user->name,
                        'email' => $data->user->email,
                        'avatar' => $data->user->avatar,
                        'avatar_original' => $data->user->avatar_original
                    ],
                    "average_rating"=> $average_rating,
                    "rating_count"=> $total,
                    'logo' => $data->logo,
                    'sliders' => json_decode($data->sliders),
                    'address' => $data->address,
                    'facebook' => $data->facebook,
                    'google' => $data->google,
                    'twitter' => $data->twitter,
                    'youtube' => $data->youtube,
                    'instagram' => $data->instagram,
                    'links' => [
                        'featured' => route('shops.featuredProducts', $data->id),
                        'top' => route('shops.topSellingProducts',  $data->id),
                        'new' => route('shops.newProducts', $data->id),
                        'all' => route('shops.allProducts', $data->id),
                        'brands' => route('shops.brands', $data->id)
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
