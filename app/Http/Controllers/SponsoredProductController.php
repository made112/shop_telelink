<?php

namespace App\Http\Controllers;

use App\SponsoredProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SponsoredProductController extends Controller
{
    protected function validator(array $data, $type = 'add')
    {
        $validators = [
            "title"  => "required|string|max:255",
            "sub_title"  => "required|string|max:255",
            'banner' => 'required|mimes:jpeg,bmp,png,jpg,svg|max:2000',
            'start_date' => 'required|date|date_format:m/d/Y',
            'end_date' => 'required|date|date_format:m/d/Y|after:start_date',
            'product' => 'required|string',

        ];
        $messages = [
            'title.string'=>'Title must be an string',
            'sub_title.string'=>'Sub-title must be string',
            'banner.required' => 'Banner must be required.',
            'banner.mimes' => 'Banner extension must be [png, jpg, jpeg, bmp]',
            'banner.max'=>' The file must be less than 2MB',
        ];

        if($type === 'update') {
            $validators['banner'] = 'nullable|mimes:jpeg,bmp,png,jpg,svg|max:2000';
        }

        return Validator::make($data, $validators, $messages);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $sponsored_products = SponsoredProduct::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $flash_deals = $sponsored_products->where('title', 'like', '%'.$sort_search.'%');
        }
        $sponsored_products = $sponsored_products->paginate(15);
        return view('sponsored_products.index', compact('sponsored_products', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sponsored_products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $sponsored_product = new SponsoredProduct();
        $sponsored_product->product_id = $request->product;
        $sponsored_product->title = $request->title;
        $sponsored_product->sub_title = $request->sub_title;
        $sponsored_product->start_date = strtotime($request->start_date);
        $sponsored_product->end_date = strtotime($request->end_date);
        $sponsored_product->slug = strtolower(str_replace(' ', '-', $request->title).'-'.Str::random(5));
        if($request->hasFile('banner')){
            $sponsored_product->banner = $request->file('banner')->store('uploads/sponsored/banner');
        }
        if($sponsored_product->save()){
            flash(__('Sponsored Product has been inserted successfully'))->success();
            return redirect()->route('sponsored_products.index');
        }
        else{
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SponsoredProduct  $sponsoredProduct
     * @return \Illuminate\Http\Response
     */
    public function show(SponsoredProduct $sponsoredProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SponsoredProduct  $sponsoredProduct
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sponsored_product = SponsoredProduct::findOrFail(decrypt($id));
        return view('sponsored_products.edit', compact('sponsored_product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SponsoredProduct  $sponsoredProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validations = $this->validator($request->all(), 'update');
        if($validations->fails()){
            $errors = '';
            foreach ($validations->errors()->all('') as $key=> $value) {
                $errors .= $value;
            }
            flash($errors)->error();
            return  back();
        }
        $sponsored_product = SponsoredProduct::findOrFail($id);
        $sponsored_product->title = $request->title;
        $sponsored_product->sub_title = $request->sub_title;
        $sponsored_product->product_id = $request->product;
        $sponsored_product->start_date = strtotime($request->start_date);
        $sponsored_product->end_date = strtotime($request->end_date);
        if (($sponsored_product->slug == null) || ($sponsored_product->title != $request->title)) {
            $sponsored_product->slug = strtolower(str_replace(' ', '-', $request->title) . '-' . Str::random(5));
        }
        if($request->hasFile('banner')){
            $sponsored_product->banner = $request->file('banner')->store('uploads/sponsored/banner');
        }

        if($sponsored_product->save()){
            flash(__('Sponsored Product has been updated successfully'))->success();
            return redirect()->route('sponsored_products.index');
        }
        else{
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SponsoredProduct  $sponsoredProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sponsored_product = SponsoredProduct::findOrFail($id);

        if(SponsoredProduct::destroy($id)){
            flash(__('Sponsored Product has been deleted successfully'))->success();
            return redirect()->route('sponsored_products.index');
        }
        else{
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function update_status(Request $request)
    {
        $current_sponsored = SponsoredProduct::findOrFail($request->id);
        $current_sponsored->status = $request->status;
        if($current_sponsored->save()){
            flash(__('Sponsored Product status updated successfully'))->success();
            return 1;
        }
        return 0;
    }

    public function update_featured(Request $request)
    {
        foreach (SponsoredProduct::all() as $key => $sponsored_product) {
            $sponsored_product->featured = 0;
            $sponsored_product->save();
        }
        $current_sponsored = SponsoredProduct::findOrFail($request->id);
        $current_sponsored->featured = $request->featured;
        if($current_sponsored->save()){
            flash(__('Sponsored Product status updated successfully'))->success();
            return 1;
        }
        return 0;
    }
}
