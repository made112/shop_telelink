<?php

namespace App\Http\Resources;

use App\Color;
use App\Models\Attribute;
use App\Models\Review;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductDetailCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $attributes = $data->stocks->makeHidden(['created_at', 'updated_at']);
                $colors = Color::whereIn('code', json_decode($data->colors))->get(['code', 'name']);
                $attributes->map(function ($item) use ($data, $colors) {
                    $item['imageFeature'] = $data->thumbnail_img;
                    $item['choice_options'] = $this->convertToChoiceOptions(json_decode($data->choice_options), $colors);
                });
                if (count($attributes) > 0) {
                    foreach ($attributes as $attribute) {
                        $variant_sku = '';
                        foreach ($variant = explode('-', $attribute->variant) as $value) {
                            if ($color = Color::where('name', $value)->first()) {
                                $variant_sku .= $color->code . '-';
                            } else {
                                $variant_sku .= $value . '-';
                            }
                        }

                        $attribute['regular_price'] = $attribute->getOriginal('price');
                        $attribute->variant = rtrim($variant_sku, "-");
                        $attribute->variant = str_replace(' ', '', $attribute->variant);
                    }
                }

                $qty = 0;
                if ($data->variant_product) {
                    if (isset($data->stocks->first()->qty)) {
                        $qty = $data->stocks->first()->qty;
                    }
//                    foreach ($data->stocks as $key => $stock) {
//                        $qty += $stock->qty;
//                    }
                } else {
                    $qty = $data->current_stock;
                }

                return [
                    'id' => (integer)$data->id,
                    'name' => $data->name,
                    'added_by' => $data->added_by,
                    'slug' => $data->slug,
                    'user' => [
                        'id' => $data->user->id,
                        'name' => $data->user->name,
                        'email' => $data->user->email,
                        'avatar' => $data->user->avatar,
                        'document_id' => $data->user->document_id,
                        'avatar_original' => $data->user->avatar_original,
                        'shop_name' => $data->added_by == 'admin' ? '' : $data->user->shop->name,
                        'shop_logo' => $data->added_by == 'admin' ? '' : $data->user->shop->logo,
                        'shop_link' => $data->added_by == 'admin' ? '' : route('shops.info', $data->user->shop->id)
                    ],
                    'category' => [
                        'name' => $data->category->name,
                        'banner' => $data->category->banner,
                        'icon' => $data->category->icon,
                        'links' => [
                            'products' => route('api.products.category', $data->category_id),
                            'sub_categories' => route('subCategories.index', $data->category_id)
                        ]
                    ],
                    'sub_category' => [
                        'name' => $data->subCategory != null ? $data->subCategory->name : null,
                        'links' => [
                            'products' => $data->subCategory != null ? route('products.subCategory', $data->subcategory_id) : null
                        ]
                    ],
                    'brand' => [
                        'name' => $data->brand != null ? $data->brand->name : null,
                        'logo' => $data->brand != null ? $data->brand->logo : null,
                        'links' => [
                            'products' => $data->brand != null ? route('api.products.brand', $data->brand_id) : null
                        ]
                    ],
                    'attributes' => $attributes,
                    'photos' => json_decode($data->photos),
                    'thumbnail_image' => $data->thumbnail_img,
                    'tags' => explode(',', $data->tags),
                    'price_lower' => (double)explode('-', home_discounted_base_price($data->id))[0],
                    'price_higher' => (double)$data->unit_price,
                    'choice_options' => $this->convertToChoiceOptions(json_decode($data->choice_options), $colors),
                    'todays_deal' => (integer)$data->todays_deal,
                    'featured' => (integer)$data->featured,
                    'current_stock' => (integer)$qty,
                    'inStock' => (integer)$qty > 0 ? true : false,
                    'unit' => $data->unit,
                    'discount' => (double)$data->discount,
                    'discount_type' => $data->discount_type,
                    'tax' => (double)$data->tax,
                    'tax_type' => $data->tax_type,
                    'shipping_type' => $data->shipping_type,
                    'shipping_cost' => (double)$data->shipping_cost,
                    'number_of_sales' => (integer)$data->num_of_sale,
                    'average_rating' => number_format((double)$data->rating, 2, ".", ""),
                    'rating_count' => (integer)Review::where(['product_id' => $data->id])->count(),
                    'description' => $data->description,
                    'links' => [
                        'reviews' => route('api.reviews.index', $data->id),
                        'related' => route('products.related', $data->id)
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

    protected function convertToChoiceOptions($data, $colors)
    {
        $result = array();
        foreach ($data as $key => $choice) {
            $attribute = Attribute::find($choice->attribute_id);
            $item['title'] = $attribute->name;
            $item['name'] = $attribute->name;
            $values = [];
            if (count($choice->values) > 0) {
                foreach ($choice->values as $value) {
                    $values[] = str_replace(' ', '', $value);
                }
            }
            $item['options'] = $values;


            array_push($result, $item);
        }
        if (isset($colors) && count($colors) > 0) {
            $item['name'] = 'Colors';
            $item['title'] = 'color';
            $item['codes'] = array_reverse($colors->pluck("name")->toArray());
            $item['options'] = array_reverse($colors->pluck("code")->toArray());

            array_push($result, $item);
        }
        return $result;
    }
}
