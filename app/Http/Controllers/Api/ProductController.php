<?php

namespace App\Http\Controllers\Api;

use App\Attribute;
use App\Http\Controllers\SearchController;
use App\Http\Resources\FlashDealCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductDetailCollection;
use App\Http\Resources\SearchProductCollection;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Seller;
use App\SubCategory;
use App\SubSubCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
//
        return new ProductCollection(Product::latest()->paginate(10));
    }

    public function show($id)
    {
//
        return new ProductDetailCollection(Product::where('id', $id)->get());
    }

    public function admin()
    {
//
        return new ProductCollection(Product::where('added_by', 'admin')->latest()->paginate(10));
    }

    public function seller()
    {
//
        return new ProductCollection(Product::where('added_by', 'seller')->latest()->paginate(10));
    }

    public function category($id)
    {
//
        return new ProductDetailCollection(Product::where('category_id', $id)->latest()->paginate(10));
    }

    public function subCategory($id)
    {
//
        return new ProductDetailCollection(Product::where('subcategory_id', $id)->latest()->paginate(10));
    }

    public function subSubCategory($id)
    {
//
        return new ProductDetailCollection(Product::where('subsubcategory_id', $id)->latest()->paginate(10));
    }

    public function brand($id)
    {
//
        return new ProductCollection(Product::where('brand_id', $id)->latest()->paginate(10));
    }

    public function todaysDeal()
    {
//
        return new ProductCollection(Product::where('todays_deal', 1)->latest()->get());
    }

    public function flashDeal()
    {
//
        $flash_deals = FlashDeal::where('status', 1)->where('featured', 1)->where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->get();
        return new FlashDealCollection($flash_deals);
    }

    public function featured()
    {
//
        return new ProductDetailCollection(Product::where('featured', 1)->latest()->get());
    }

    public function bestSeller()
    {
//
        return new ProductDetailCollection(Product::orderBy('num_of_sale', 'desc')->limit(20)->get());
    }

    public function related($id)
    {
//
        $product = Product::find($id);
        return new ProductDetailCollection(Product::where('subcategory_id', $product->subcategory_id)->where('id', '!=', $id)->limit(10)->get());
    }

    public function topFromSeller($id)
    {
//
        $product = Product::find($id);
        return new ProductDetailCollection(Product::where('user_id', $product->user_id)->orderBy('num_of_sale', 'desc')->limit(4)->get());
    }

    public function search()
    {
//
        $key = request('key');
        $scope = request('scope');

        switch ($scope) {

            case 'price_low_to_high':
                $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('unit_price', 'asc')->paginate(10));
                $collection->appends(['key' => $key, 'scope' => $scope]);
                return $collection;

            case 'price_high_to_low':
                $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('unit_price', 'desc')->paginate(10));
                $collection->appends(['key' => $key, 'scope' => $scope]);
                return $collection;

            case 'new_arrival':
                $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('created_at', 'desc')->paginate(10));
                $collection->appends(['key' => $key, 'scope' => $scope]);
                return $collection;

            case 'popularity':
                $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('num_of_sale', 'desc')->paginate(10));
                $collection->appends(['key' => $key, 'scope' => $scope]);
                return $collection;

            case 'top_rated':
                $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('rating', 'desc')->paginate(10));
                $collection->appends(['key' => $key, 'scope' => $scope]);
                return $collection;

            // case 'category':
            //
            //     $categories = Category::select('id')->where('name', 'like', "%{$key}%")->get()->toArray();
            //     $collection = new SearchProductCollection(Product::where('category_id', $categories)->orderBy('num_of_sale', 'desc')->paginate(10));
            //     $collection->appends(['key' =>  $key, 'scope' => $scope]);
            //     return $collection;
            //
            // case 'brand':
            //
            //     $brands = Brand::select('id')->where('name', 'like', "%{$key}%")->get()->toArray();
            //     $collection = new SearchProductCollection(Product::where('brand_id', $brands)->orderBy('num_of_sale', 'desc')->paginate(10));
            //     $collection->appends(['key' =>  $key, 'scope' => $scope]);
            //     return $collection;
            //
            // case 'shop':
            //
            //     $shops = Shop::select('user_id')->where('name', 'like', "%{$key}%")->get()->toArray();
            //     $collection = new SearchProductCollection(Product::where('user_id', $shops)->orderBy('num_of_sale', 'desc')->paginate(10));
            //     $collection->appends(['key' =>  $key, 'scope' => $scope]);
            //     return $collection;

            default:
                $collection = new SearchProductCollection(Product::where('name', 'like', "%{$key}%")->orWhere('tags', 'like', "%{$key}%")->orderBy('num_of_sale', 'desc')->paginate(10));
                $collection->appends(['key' => $key, 'scope' => $scope]);
                return $collection;
        }
    }

    public function variantPrice(Request $request)
    {
//
        $product = Product::findOrFail($request->id);
        $str = '';
        $tax = 0;

        if ($request->has('color')) {
            $data['color'] = $request['color'];
            $str = Color::where('code', $request['color'])->first()->name;
        }

        foreach (json_decode($request->choice) as $option) {
            $str .= $str != '' ? '-' . str_replace(' ', '', $option->name) : str_replace(' ', '', $option->name);
        }

        if ($str != null && $product->variant_product) {
            $product_stock = $product->stocks->where('variant', $str)->first();
            $price = $product_stock->price;
            $stockQuantity = $product_stock->qty;
        } else {
            $price = $product->unit_price;
            $stockQuantity = $product->current_stock;
        }

        //discount calculation
        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $key => $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return response()->json([
            'product_id' => $product->id,
            'variant' => $str,
            'price' => (double)$price,
            'in_stock' => $stockQuantity < 1 ? false : true
        ]);
    }

    public function filter(Request $request)
    {
//
        $query = $request->q;
        if (preg_match('/^[^a-zA-Z0-9]+$/', $query)) // '/[^a-z\d]/i' should also work.
        {
            $str = '';
            $query = str_replace("\"", "", json_encode(strtolower($query)));
            $pos = strpos($query, '\\');
            if ($pos !== false) {
                $str = substr($query, 0, $pos + 1) . str_replace('\\', '\\\\', substr($query, $pos + 1));
            }
            $query = $str;
        }
        $brand_id = (\App\Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
        $sort_by = $request->sort_by;
        $category_id = (\App\Category::where('id', $request->category)->first() != null) ? Category::where('id', $request->category)->first()->id : null;
        $subcategory_id = (SubCategory::where('id', $request->subcategory)->first() != null) ? SubCategory::where('id', $request->subcategory)->first()->id : null;
        $subsubcategory_id = (SubSubCategory::where('id', $request->subsubcategory)->first() != null) ? SubSubCategory::where('id', $request->subsubcategory)->first()->id : null;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;

        $conditions = ['published' => 1];

        if ($brand_id != null) {
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        }
        if ($category_id != null) {
            $conditions = array_merge($conditions, ['category_id' => $category_id]);
        }
        if ($subcategory_id != null) {
            $conditions = array_merge($conditions, ['subcategory_id' => $subcategory_id]);
        }
        if ($subsubcategory_id != null) {
            $conditions = array_merge($conditions, ['subsubcategory_id' => $subsubcategory_id]);
        }
        if ($seller_id != null) {
            $conditions = array_merge($conditions, ['user_id' => Seller::findOrFail($seller_id)->user->id]);
        }
        $products = \App\Product::where($conditions);

        if ($min_price != null && $max_price != null) {
            $products = $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
        }

        if ($query != null) {
            $searchController = new SearchController;
            $searchController->store($request);
            $products = $products->where('name', 'like', '%' . $query . '%')->orWhere('tags', 'like', '%' . $query . '%');
        }

        if ($sort_by != null) {
            switch ($sort_by) {
                case '1':
                    $products->orderBy('created_at', 'desc');
                    break;
                case '2':
                    $products->orderBy('created_at', 'asc');
                    break;
                case '3':
                    $products->orderBy('unit_price', 'asc');
                    break;
                case '4':
                    $products->orderBy('unit_price', 'desc');
                    break;
                default:
                    // code...
                    break;
            }
        }


        $non_paginate_products = filter_products($products)->get();

        //Attribute Filter

        $attributes = array();
        foreach ($non_paginate_products as $key => $product) {
            if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                foreach (json_decode($product->attributes) as $key => $value) {
                    $flag = false;
                    $pos = 0;
                    foreach ($attributes as $key => $attribute) {
                        if ($attribute['id'] == $value) {
                            $flag = true;
                            $pos = $key;
                            break;
                        }
                    }
                    if (!$flag) {
                        $item['id'] = $value;
                        $item['values'] = array();
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                $item['values'] = $choice_option->values;
                                break;
                            }
                        }
                        array_push($attributes, $item);
                    } else {
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                foreach ($choice_option->values as $key => $value) {
                                    if (!in_array($value, $attributes[$pos]['values'])) {
                                        array_push($attributes[$pos]['values'], $value);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $selected_attributes = array();

        foreach ($attributes as $key => $attribute) {
            if ($request->has("attribute") && $request->attribute !== '' && $request->has('attribute_term') && $request->attribute_term != '') {
                $split_attr = explode(',', $request->attribute_term);
                foreach ($split_attr as $val) {
                    $str = '"' . $val . '"';
                    $products = $products->where('choice_options', 'like', '%' . $str . '%');
                }
            }
            if ($request->has('attribute_' . $attribute['id'])) {
                foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                    $str = '"' . $value . '"';
                    $products = $products->where('choice_options', 'like', '%' . $str . '%');
                }

                $item['id'] = $attribute['id'];
                $item['values'] = $request['attribute_' . $attribute['id']];
                array_push($selected_attributes, $item);
            }
        }


        //Color Filter
        $all_colors = array();

        foreach ($non_paginate_products as $key => $product) {
            if ($product->colors != null) {
                foreach (json_decode($product->colors) as $key => $color) {
                    if (!in_array($color, $all_colors)) {
                        array_push($all_colors, $color);
                    }
                }
            }
        }

        $selected_color = null;

        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            $products = $products->where('colors', 'like', '%' . $str . '%');
            $selected_color = $request->color;
        }


        $products = new ProductDetailCollection(filter_products($products)->paginate(12)->appends(request()->query()));
        return $products;
        //
//        return response()->json(
//            $products
////            [
////            'products' => ,
////            'query' => $query,
////            'category_id' => $category_id,
////            'subcategory_id' => $subcategory_id,
////            'subsubcategory_id' => $subsubcategory_id,
////            'brand_id' => $brand_id,
////            'sort_by' => $sort_by,
////            'seller_id' => $seller_id,
////            'min_price' => $min_price,
////            'max_price' => $max_price,
////            'all_colors' => $all_colors,
////            'selected_color' => $selected_color,
////            'attributes' => $attributes,
////            'selected_attributes' => $selected_attributes
////        ]
//    );
    }

    public function getTags()
    {
        $tags = Product::select('tags')->get();
        $all_tags = array();
        foreach ($tags as $tag) {
            if ($tag->tags) {
                $values = explode('|', $tag->tags);
                foreach ($values as $val) {
                    if (!in_array((object)['name' => $val], $all_tags) and $val != '') {
                        if (count(explode(',', $val)) > 0) {
                            $arr_name = explode(',', $val);
                            foreach ($arr_name as $key => $item) {
                                array_push($all_tags, (object)['name' => $item]);
                            }
                        }
                    }
                }
            }
        }
        return response()->json($all_tags);
    }

    public function getAllAttributes()
    {
        $attributes = Attribute::select(['id', 'name'])->get();
        return response()->json($attributes);
    }

    public function attributes(Request $request, $id)
    {
        $attr = Attribute::findOrFail($id);
        if (!$attr) {
            return response()->json('fail');
        }
        $non_paginate_products = \App\Product::all();
        $attributes = array();
        $i = 1;
        foreach ($non_paginate_products as $key => $product) {
            if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                foreach (json_decode($product->attributes) as $key => $value) {
                    foreach (json_decode($product->choice_options) as $key2 => $choice_option) {
                        if ($choice_option->attribute_id == $attr->id) {
                            foreach ($choice_option->values as $key3 => $value) {
                                if (!in_array($value, $attributes) && $value !== "") {
                                    array_push($attributes, (object)['id' => $i, 'name' => $value]);
                                    $i++;
                                }
                            }
                        }
                    }

                }
            }
        }
        return response()->json($attributes);
    }

    public function home()
    {
        return new ProductCollection(Product::inRandomOrder()->take(50)->get());
    }
}
