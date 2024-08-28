<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductStock;
use App\Category;
use App\Language;
use Auth;
use App\SubSubCategory;
use Illuminate\Support\Facades\Validator;
use Session;
use ImageOptimizer;
use DB;
use CoreComponentRepository;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    protected function validator(array $data, $type = 'add')
    {
        $validation_array = [

            'name' => 'required|array|min:2',
            "name.*" => "nullable|string|max:255",
            'category_id' => 'required|string',
            'subcategory_id' => 'required|string',
            'brand_id' => 'nullable|string',
            'unit' => 'nullable|string|max:255',
            'min_qty' => 'nullable|numeric|min:1',
            'tags' => 'required|array|min:1',
            'tags.*.*' => 'nullable',
            'photos' => 'required|string|max:100',
            'thumbnail_img' => 'required|string|max:100',
            'video_provider' => 'nullable|string|max:100',
            'video_link' => "nullable|string",
            'meta_title' => 'nullable|array|min:2',
            'meta_description' => 'nullable|array|min:2',
            'meta_img' => 'nullable|string|max:100',
            'unit_price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'flat_shipping_cost' => 'nullable|numeric|min:0',
            'free_shipping_cost' => 'nullable|numeric|min:0',
            'description' => 'required|array|min:2',
            'pdf' => 'nullable|string|max:100',
            'meta_title.*' => 'nullable|string|max:255',
            'meta_description.*' => 'nullable|string|max:255',
            'description.*' => 'nullable|string',
        ];
        $validation_messages = [
            'meta_description.*.required' => 'The field meta description is required*.',
            'meta_description.*.string' => 'The field meta description is string.',
            'description.*.required' => 'The field description is required*.',
            'description.*.string' => 'The field description is string.',
            'meta_title.*.required' => 'The field title is required*.',
            'meta_title.*.string' => 'The field title is string.',
            'name.*.required' => 'The field name is required*.',
            'name.*.string' => 'The field name is string.',
            'name.array' => 'Name must be array.',
            'min_qty.min' => 'Minimum quantity must be one at least',
            'min_qty.numeric' => 'Minimum quantity must be numeric',
            'tax.numeric' => 'Tax must be numeric',
            'discount.numeric' => 'Discount must be numeric',
            'flat_shipping_cost.numeric' => 'Flat Shipping Cost must be numeric',
            'free_shipping_cost.numeric' => 'Free Shipping Cost must be numeric',
            'description.required' => 'Description must be required.',
            'description.array' => 'Description must be array.',
            'tags.*.required' => 'The tags field is required*.',
            'tags.*.string' => 'The tags field is string.',
            'tags.required' => 'The tags field is required.',
            'tags.array' => 'The tags field must be an array.',
            'tags.*.*.required' => 'The tags field is required',
            'meta_title.required' => 'Meta title must be required.',
            'meta_title.array' => 'Meta title must be array.',
            'meta_description.required' => 'Meta description must be required*.',
            'meta_description.array' => 'Meta description must be array.',
            'unit.required' => 'Unit must be required.',
            'unit.array' => 'Unit must be array.',
            'category_id.required' => 'Category is required*.',
            'subcategory_id.required' => 'Subcategory must be required.',
            'brand_id.string' => 'Brand must be string.',
            'unit_price.required' => 'Unit price is required.',
            'purchase_price.required' => 'Purchase price is required.',
            'photos.required' => 'You must add at least one image.',
            'photos.array' => 'Main images must be an array.',
            'photos.*.mimes' => 'Image extension must be [png, jpg, jpeg, bmp].',
            'thumbnail_img.required' => 'The field thumbnail image must be required.',
            'thumbnail_img.mimes' => 'The field thumbnail image extension must be [png, jpg, jpeg, bmp].',
            'featured_img.mimes' => 'featured image extension must be [png, jpg, jpeg, bmp].',
            'flash_deal_img.mimes' => 'flash deal image extension must be [png, jpg, jpeg, bmp].',
            'meta_img.required' => 'The field meta image must be required.*',
            'meta_img.mimes' => 'The field meta image extension must be [png, jpg, jpeg, bmp].',
            'current_stock.required' => 'Current stock is required',
            'current_stock.numeric' => 'Current stock must be numeric'
        ];
        if ($type === 'update') {
            if (array_key_exists('previous_thumbnail_img', $data)) {
                $validation_array['thumbnail_img'] = 'nullable|mimes:jpeg,bmp,png,jpg';
            }
            if (array_key_exists('previous_photos', $data)) {
                $validation_array['photos'] = 'nullable|array';
                $validation_array['photos.0'] = 'nullable|mimes:jpeg,bmp,png,jpg';
            }
            if (array_key_exists('previous_meta_img', $data)) {
                $validation_array['meta_img'] = 'nullable|string';
            }
        }

        return Validator::make($data, $validation_array, $validation_messages);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_products(Request $request)
    {
        //CoreComponentRepository::instantiateShopRepository();

        $type = 'In House';
        $col_name = null;
        $query = null;
        $sort_search = null;

        $products = Product::where('added_by', 'admin');

        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
//        if ($request->search != null) {
//            $sort_search = $request->search;
//            if (preg_match('/^[^a-zA-Z0-9]+$/', $sort_search)) // '/[^a-z\d]/i' should also work.
//            {
//                $sort_search = str_replace("\"", "", json_encode(strtolower($sort_search)));
//                $pos = strpos($sort_search,'\\');
//                if ($pos !== false) {
//                    $str = substr($sort_search,0,$pos+1) . str_replace('\\','\\\\',substr($sort_search,$pos+1));
//                }
//                $sort_search = $str;
//            }
//            $products = $products
//                ->where('name', 'like', '%' . $sort_search . '%');
//            $sort_search = $request->search;
//        }
        if ($request->search != null) {
            $sort_search = $request->search;
            if (preg_match('/^[^a-zA-Z0-9]+$/', $sort_search)) {
                $sort_search = str_replace("\"", "", json_encode(strtolower($sort_search)));
                $pos = strpos($sort_search,'\\');
                if ($pos !== false) {
                    $str = substr($sort_search,0,$pos+1) . str_replace('\\','\\\\',substr($sort_search,$pos+1));
                }
                $sort_search = $str;
            }
            $products = $products
                ->where('name', 'like', '%' . $sort_search . '%')->orWhere('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }

        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);
        return view('products.index', compact('products', 'type', 'col_name', 'query', 'sort_search'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function seller_products(Request $request)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::where('added_by', 'seller');
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            if (preg_match('/^[^a-zA-Z0-9]+$/', $sort_search)) // '/[^a-z\d]/i' should also work.
            {
                $sort_search = str_replace("\"", "", json_encode(strtolower($sort_search)));
                $pos = strpos($sort_search,'\\');
                if ($pos !== false) {
                    $str = substr($sort_search,0,$pos+1) . str_replace('\\','\\\\',substr($sort_search,$pos+1));
                }
                $sort_search = $str;
            }
            $products = $products
                ->where('name', 'like', '%' . $sort_search . '%');
            $sort_search = $request->search;
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->orderBy('created_at', 'desc')->paginate(15);
        $type = 'Seller';
        return view('products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        // return Language::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();
        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();

        $product = new Product;
        $product->name = $request->name;
        $product->added_by = $request->added_by;
        if (Auth::user()->user_type == 'seller') {
            $product->user_id = Auth::user()->id;
        } else {
            $product->user_id = \App\User::where('user_type', 'admin')->first()->id;
        }
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->subsubcategory_id = $request->subsubcategory_id;
        $product->brand_id = $request->brand_id;
        $product->current_stock = $request->current_stock;
        $product->barcode = $request->barcode;

        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $product->refundable = 1;
            } else {
                $product->refundable = 0;
            }
        }
        if (Auth::user()->user_type == 'seller') {
            $product->refundable = 1;
        }
        $product->photos = $request->photos;
        $product->thumbnail_img = $request->thumbnail_img;
        if($request->has('unit') && $request->unit != null) {
            $product->unit = $request->unit;
        }else {
            $product->unit = 'PCS';
        }
        $product->min_qty = $request->min_qty;
        if (!$product->min_qty) {
            $product->min_qty = 1;
        }
        $tags=[];
        foreach ($request->tags as $key => $value){

            $tags[$key] = implode('|', $value);
        }
        $product->tags=$tags;

        $product->description = $request->description;
        $product->video_provider = $request->video_provider;
        $product->video_link = $request->video_link;
        $product->unit_price = $request->unit_price;
        $product->purchase_price = $request->purchase_price;
        if ($request->has('tax') && $request->tax != null){
            $product->tax = $request->tax;
            $product->tax_type = $request->tax_type;
        }
        $product->discount = $request->discount;
        $product->discount_type = $request->discount_type;
        $product->shipping_type = $request->shipping_type;
        if ($request->has('shipping_type')) {
            if ($request->shipping_type == 'free') {
                $product->shipping_cost = 0;
            } elseif ($request->shipping_type == 'flat_rate') {
                $product->shipping_cost = $request->flat_shipping_cost;
            }
        }
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;

        if($request->has('meta_img')){
            $product->meta_img = $request->meta_img;
        } else {
            $product->meta_img = $product->thumbnail_img;
        }

        if ($product->meta_title == null) {
            $product->meta_title = $product->name;
        }

        if ($product->meta_description == null) {
            $product->meta_description = $product->description;
        }

        if ($request->has('pdf') && $request->pdf != '') {
            $product->pdf = $request->pdf;
        }


        $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE', 'en')])) . '-' . Str::random(5);


        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = json_encode($request->colors);
        } else {
            $colors = array();
            $product->colors = json_encode($colors);
        }

        $choice_options = array();

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;

                $item['attribute_id'] = $no;
                $item['values'] = explode(',', implode('|', $request[$str]));

                array_push($choice_options, $item);
            }
        }

        if (!empty($request->choice_no)) {
            $product->attributes = json_encode($request->choice_no);
        } else {
            $product->attributes = json_encode(array());
        }

        $product->choice_options = json_encode($choice_options);

        //$variations = array();

        $product->save();

        //combinations start
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        //Generates the combinations of customer choice options
        $combinations = combinations($options);
        if (count($combinations[0]) > 0) {
            $product->variant_product = 1;
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $key => $item) {
                    if ($key > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = \App\Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }
                // $item = array();
                // $item['price'] = $request['price_'.str_replace('.', '_', $str)];
                // $item['sku'] = $request['sku_'.str_replace('.', '_', $str)];
                // $item['qty'] = $request['qty_'.str_replace('.', '_', $str)];
                // $variations[$str] = $item;

                $product_stock = ProductStock::where('product_id', $product->id)->where('variant', $str)->first();
                if ($product_stock == null) {
                    $product_stock = new ProductStock;
                    $product_stock->product_id = $product->id;
                }

                $product_stock->variant = $str;
                $product_stock->price = $request['price_' . str_replace('.', '_', $str)];
                $product_stock->discount = $request['discount_' . str_replace('.', '_', $str)];
                $product_stock->sku = $request['sku_' . str_replace('.', '_', $str)];
                $product_stock->qty = $request['qty_' . str_replace('.', '_', $str)];
                //todo nial fix
                $product_stock->save();
            }
        }
        //combinations end

        foreach (Language::all() as $key => $language) {
            $data = openJSONFile($language->code);
            $data[$product->name] = $product->name;
            saveJSONFile($language->code, $data);
        }

        $product->save();

        flash(__('Product has been inserted successfully'))->success();
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return redirect()->route('products.admin');
        } else {
            if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                $seller = Auth::user()->seller;
                $seller->remaining_uploads -= 1;
                $seller->save();
            }
            return redirect()->route('seller.products');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function admin_product_edit($id)
    {
        $product = Product::findOrFail(decrypt($id));
        $tags = json_decode($product->tags);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories', 'tags'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function seller_product_edit($id)
    {
        $product = Product::findOrFail(decrypt($id));
        $tags = json_decode($product->tags);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

//        $validations = $this->validator($request->all(), 'update');
//        if($validations->fails()){
//            $errors = '';
//            foreach ($validations->errors()->all('') as $key=> $value) {
//                $errors .= $value;
//            }
//            flash($errors)->error();
//            return  back();
//        }
        $this->validator($request->all(), 'update')->validate();

        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->subsubcategory_id = $request->subsubcategory_id;
        $product->brand_id = $request->brand_id;
        $product->current_stock = $request->current_stock;
        $product->barcode = $request->barcode;

        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $product->refundable = 1;
            } else {
                $product->refundable = 0;
            }
        }

//        if ($request->has('previous_photos')) {
//            $photos = $request->previous_photos;
//        } else {
//            $photos = array();
//        }
//
//        if ($request->hasFile('photos')) {
//            foreach ($request->photos as $key => $photo) {
//                $path = $photo->store('uploads/products/photos');
//                array_push($photos, $path);
//                //ImageOptimizer::optimize(base_path('public/').$path);
//            }
//        }
//        $product->photos = json_encode($photos);

//        $product->thumbnail_img = $request->previous_thumbnail_img;
//        if ($request->hasFile('thumbnail_img')) {
//            $product->thumbnail_img = $request->thumbnail_img->store('uploads/products/thumbnail');
//            //ImageOptimizer::optimize(base_path('public/').$product->thumbnail_img);
//        }
        $product->photos         = $request->photos;
        $product->thumbnail_img  = $request->thumbnail_img;

        $product->unit = $request->unit;
        $product->min_qty = $request->min_qty;
        if (!$product->min_qty) {
            $product->min_qty = 1;
        }
        $tags=[];
        foreach ($request->tags as $key => $value){

            $tags[$key] = implode('|', $value);
        }
        $product->tags=$tags;
        $product->featured_img = $request->previous_featured_img;
        if ($request->hasFile('featured_img')) {
            $product->featured_img = $request->featured_img->store('uploads/products/featured');
            //ImageOptimizer::optimize(base_path('public/').$product->featured_img);
        }

        $product->flash_deal_img = $request->previous_flash_deal_img;
        if ($request->hasFile('flash_deal_img')) {
            $product->flash_deal_img = $request->flash_deal_img->store('uploads/products/flash_deal');
            //ImageOptimizer::optimize(base_path('public/').$product->flash_deal_img);
        }

        $product->description = $request->description;
        $product->video_provider = $request->video_provider;
        $product->video_link = $request->video_link;
        $product->unit_price = $request->unit_price;
        $product->purchase_price = $request->purchase_price;
        $product->tax = $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount;
        $product->shipping_type = $request->shipping_type;
        if ($request->has('shipping_type')) {
            if ($request->shipping_type == 'free') {
                $product->shipping_cost = 0;
            } elseif ($request->shipping_type == 'flat_rate') {
                $product->shipping_cost = $request->flat_shipping_cost;
            }
        }
        $product->discount_type = $request->discount_type;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;

        $product->meta_img = $request->meta_img;

//        $product->meta_img = $request->previous_meta_img;
//        if ($request->hasFile('meta_img')) {
//            $product->meta_img = $request->meta_img->store('uploads/products/meta');
//            //ImageOptimizer::optimize(base_path('public/').$product->meta_img);
//        }

//        if ($request->hasFile('pdf')) {
//            $product->pdf = $request->pdf->store('uploads/products/pdf');
//        }
        if ($request->has('pdf') && $request->pdf != '') {
            $product->pdf = $request->pdf;
        }

        // $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE','en')])) . '-' .Str::random(5);

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = json_encode($request->colors);
        } else {
            $colors = array();
            $product->colors = json_encode($colors);
        }

        $choice_options = array();

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;

                $item['attribute_id'] = $no;
                $item['values'] = explode(',', implode('|', $request[$str]));

                array_push($choice_options, $item);
            }
        }

        if ($product->attributes != json_encode($request->choice_attributes)) {
            foreach ($product->stocks as $key => $stock) {
                $stock->delete();
            }
        }

        if (!empty($request->choice_no)) {
            $product->attributes = json_encode($request->choice_no);
        } else {
            $product->attributes = json_encode(array());
        }

        $product->choice_options = json_encode($choice_options);

        //        foreach (Language::all() as $key => $language) {
        //            $data = openJSONFile($language->code);
        //            unset($data[$product->name]);
        //            $data[$request->name] = "";
        //            saveJSONFile($language->code, $data);
        //        }

        //combinations start
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $combinations = combinations($options);
        if (count($combinations[0]) > 0) {
            $product->variant_product = 1;
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $key => $item) {
                    if ($key > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = \App\Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }

                $product_stock = ProductStock::where('product_id', $product->id)->where('variant', $str)->first();
                if ($product_stock == null) {
                    $product_stock = new ProductStock;
                    $product_stock->product_id = $product->id;
                }

                $product_stock->variant = $str;
                $product_stock->price = $request['price_' . str_replace('.', '_', $str)];
                $product_stock->discount = $request['discount_' . str_replace('.', '_', $str)];
                $product_stock->sku = $request['sku_' . str_replace('.', '_', $str)];
                $product_stock->qty = $request['qty_' . str_replace('.', '_', $str)];
                //todo nial fix
                $product_stock->save();
            }
        }

        $product->save();

        flash(__('Product has been updated successfully'))->success();
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return redirect()->route('products.admin');
        } else {
            return redirect()->route('seller.products');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if (Product::destroy($id)) {
            foreach (Language::all() as $key => $language) {
                $data = openJSONFile($language->code);
                unset($data[$product->name]);
                saveJSONFile($language->code, $data);
            }
            flash(__('Product has been deleted successfully'))->success();
            if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
                return redirect()->route('products.admin');
            } else {
                return redirect()->route('seller.products');
            }
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate($id)
    {
        $product = Product::find($id);
        $product_new = $product->replicate();
        $product_new->slug = substr($product_new->slug, 0, -5) . Str::random(5);

        if ($product_new->save()) {
            flash(__('Product has been duplicated successfully'))->success();
            if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
                return redirect()->route('products.admin');
            } else {
                return redirect()->route('seller.products');
            }
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function get_products_by_subsubcategory(Request $request)
    {
        $products = Product::where('subsubcategory_id', $request->subsubcategory_id)->get();
        return $products;
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();
        return view('partials.product_select', compact('products'));
    }

    public function updateTodaysDeal(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->todays_deal = $request->status;
        if ($product->save()) {
            return 1;
        }
        return 0;
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;

        if ($product->added_by == 'seller' && \App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            $seller = $product->user->seller;
            if ($seller->invalid_at != null && Carbon::now()->diffInDays(Carbon::parse($seller->invalid_at), false) <= 0) {
                return 0;
            }
        }

        $product->save();
        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->featured = $request->status;
        if ($product->save()) {
            return 1;
        }
        return 0;
    }

    public function sku_combination(Request $request)
    {
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $discount = $request->discount;
        $product_name = '';
        if ($request->name['en'] !== null) {
            $product_name = $request->name["en"];
        }

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $combinations = combinations($options);
        return view('partials.sku_combinations', compact('combinations', 'unit_price', 'discount', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $product_name = '';
        if ($request->name['en'] !== null) {
            $product_name = $request->name["en"];
        }
        $product_id = $product->id;


        $unit_price = $request->unit_price;
        $discount = $request->discount;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $combinations = combinations($options);

        return view('partials.sku_combinations_edit', compact('product_id', 'combinations', 'unit_price', 'discount', 'colors_active', 'product_name', 'product'));
    }
}
