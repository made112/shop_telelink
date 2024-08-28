<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shop;
use App\User;
use App\Seller;
use App\BusinessSetting;
use Auth;
use Hash;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    protected function validator(array $data,string $type = 'info')
    {
        $validation = [];
        $messages = [];
        if($type == 'info'){
            $validation = [
                "name"  => "required|string|max:255",
                "pick_up_point_id" => "nullable|array|min:0",
                "pick_up_point_id.*" => "nullable|string|max:255",
                "address"  => "required|string|max:255",
                "meta_title"  => "nullable|string|max:255",
                "meta_description"  => "nullable|string|max:255",
                'logo' => 'mimes:jpeg,bmp,png,jpg,svg|max:2000',
                'commercial_interest' => 'required|string|max:20',
                'phone' => 'required|string|max:20'
            ];
            $messages = [
                'name.required' => 'Shop name is required*.',
                'name.string' => 'Shop name must be string*',
                'name.max' => 'Shop name must be maximum characters 255*',
                'pick_up_point_id.array'=> 'Pickup Point must be an array',
                'pick_up_point_id.*.string'=> 'Pickup Point element must be string',
                'pick_up_point_id.*.max'=> 'Pickup Point element must be maximum characters 255*',
                'address.required' => 'Address is required*.',
                'address.string' => 'Address must be string*',
                'address.max' => 'Address must be maximum characters 255*',
                'meta_title.required' => 'Meta title is required*.',
                'meta_title.string' => 'Meta title must be string*.',
                'meta_title.max' => 'Meta title must be maximum characters 255*.',
                'meta_description.required' => 'Meta description is required*.',
                'meta_description.string' => 'Meta description must be string*.',
                'meta_description.max' => 'Meta description must be maximum characters 255*',
                'logo.max' => 'logo must be maximum size 2MB'
            ];
        }elseif ($type == 'slider') {
            $validation = [
                "sliders"  => "required|array|min:1",
                "sliders.*" => "nullable|mimes:jpg,png,jpeg,svg|max:2000"
            ];

            $messages = [
                'sliders.required' => 'You must add at lease one image*.',
                'sliders.*.mimes' => 'The file extension must include: jpg,png,jpeg,svg',
                'sliders.*.max' => 'logo must be maximum size 2MB'
            ];
            if(array_key_exists('previous_sliders', $data)) {
                $validation['sliders'] = 'nullable|array';
            }

        } elseif ($type == 'social') {
            $validation = [
                'facebook' => "nullable|string",
                'twitter' => "nullable|string",
                'google' => "nullable|string",
                'youtube' => "nullable|string",
            ];
            $messages = [
                'facebook.string' => 'Facebook url must be string*',
                'twitter.string' => 'Twitter url must be string*',
                'google.string' => 'Google url must be string*',
                'youtube.string' => 'Youtube url must be string*',
            ];

        } elseif ( $type == 'jawwal_payment') {
            $validation = [
                'pay_username' => "required|string",
                'pay_password' => "required|string",
                'pay_iframe' => "required|string",
                'pay_integration_id' => "required|string",
            ];
            $messages = [
                'pay_username.required' => "Username Payment is required",
                'pay_password.required' => "Password Payment is required",
                'pay_iframe.required' => "IFrame is required",
                'pay_integration_id.required' => "Integration Payment ID is required",
                'pay_username.string' => 'Username Payment must be string*',
                'pay_password.string' => 'Password Payment must be string*',
                'pay_iframe.string' => 'IFrame must be string*',
                'pay_integration_id.string' => 'Integration Payment ID must be string*',
            ];
        } elseif ( $type == 'delivery_settings') {
            $validation = [
                'collective_delivery' => 'nullable|string',
                'delivery_settings' => 'required|numeric|max:1',
                'shipping_cost_j' => 'nullable|numeric',
                'shipping_cost_wb' => 'nullable|numeric',
                'shipping_cost_oi' => 'nullable|numeric',
                'shipping_free' => 'nullable|numeric',
                'deal_with' => 'nullable|string'
            ];
            $messages = [];
        }
        return Validator::make($data, $validation, $messages);
    }

    public function __construct()
    {
        $this->middleware('user', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user()->shop;
        return view('frontend.seller.shop', compact('shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::check() && Auth::user()->user_type == 'admin'){
            flash(__('Admin can not be a seller'))->error();
            return back();
        }
        else{
            return view('frontend.seller_form');
        }
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
        $user = null;
        if(!Auth::check()){
            if(User::where('email', $request->email)->first() != null){
                flash(__('Email already exists!'))->error();
                return back();
            }
            if($request->password == $request->password_confirmation){
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->user_type = "seller";
                $user->password = Hash::make($request->password);
                $user->save();
            }
            else{
                flash(__('Sorry! Password did not match.'))->error();
                return back();
            }
        }
        else{
            $user = Auth::user();
            if($user->customer != null){
                $user->customer->delete();
            }
            $user->user_type = "seller";
            $user->save();
        }

        if(BusinessSetting::where('type', 'email_verification')->first()->value != 1){
            $user->email_verified_at = date('Y-m-d H:m:s');
            $user->save();
        }

        $seller = new Seller;
        $seller->user_id = $user->id;
        $seller->save();

        if(Shop::where('user_id', $user->id)->first() == null){
            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->name;
            $shop->address = $request->address;
            $shop->commercial_interest = $request->commercial_interest;
            $shop->phone = $request->phone;
            $shop->slug = preg_replace('/\s+/', '-', $request->name).'-'.$shop->id;
            if($request->hasFile('logo')){
                $shop->logo = $request->logo->store('uploads/shop/logo');
            }

            if($shop->save()){
                auth()->login($user, false);
                flash(__('Your Shop has been created successfully!').'\n'.__('Please wait approved, Your request will be reviewed by iBuy.ps team.'))->success();
                return redirect()->route('shops.index');
            }
            else{
                $seller->delete();
                $user->user_type == 'customer';
                $user->save();
            }
        }

        flash(__('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $shop = Shop::find($id);

        if($request->has('name') && $request->has('address')){
            $this->validator($request->all(), 'info')->validate();
            $shop->name = $request->name;
            if ($request->has('shipping_cost')) {
                $shop->shipping_cost = $request->shipping_cost;
            }
            if ($request->has('commercial_interest') && $request->commercial_interest != null) {
                $shop->commercial_interest = $request->commercial_interest;
            }
            if ($request->has('phone') && $request->phone != null) {
                $shop->phone = $request->phone;
            }
            $shop->address = $request->address;
            $shop->slug = preg_replace('/\s+/', '-', $request->name).'-'.$shop->id;

            $shop->meta_title = $request->meta_title;
            $shop->meta_description = $request->meta_description;

            if($request->hasFile('logo')){
                $shop->logo = $request->logo->store('uploads/shop/logo');
            }

            if ($request->has('pick_up_point_id')) {
                $shop->pick_up_point_id = json_encode($request->pick_up_point_id);
            }
            else {
                $shop->pick_up_point_id = json_encode(array());
            }
        }

        elseif($request->has('facebook') || $request->has('google') || $request->has('twitter') || $request->has('youtube') || $request->has('instagram')){
            $this->validator($request->all(), 'social')->validate();
            $shop->facebook = $request->facebook;
            $shop->google = $request->google;
            $shop->twitter = $request->twitter;
            $shop->youtube = $request->youtube;
        }

        elseif($request->has('pay_username') || $request->has('pay_password') || $request->has('pay_iframe') || $request->has('pay_integration_id')){
            $this->validator($request->all(), 'jawwal_payment')->validate();
            $data = [
                "pay_username" => $request->pay_username,
                "pay_password" => $request->pay_password,
                "pay_iframe" => $request->pay_iframe,
                "pay_integration_id" => $request->pay_integration_id
            ];
            $shop->jawwal_payment = json_encode($data);
        } elseif ($request->has('delivery_settings') && $request->delivery_settings == 1) {
            $this->validator($request->all(), 'delivery_settings')->validate();
            if ($request->has('deal_with') && $request->deal_with == 'deal_with'){
                $shop->deal_with = 1;
            } else {
                $shop->deal_with = 0;
                if ($request->has('shipping_cost_j')) {
                    $shop->shipping_cost_j = $request->shipping_cost_j;
                }
                if ($request->has('shipping_cost_wb')) {
                    $shop->shipping_cost_wb = $request->shipping_cost_wb;
                }
                if ($request->has('shipping_cost_oi')) {
                    $shop->shipping_cost_oi = $request->shipping_cost_oi;
                }
                if ($request->has('shipping_free')) {
                    $shop->shipping_free = $request->shipping_free;
                }
                if ($request->has('collective_delivery')) {
                    $shop->collective_delivery = 1;
                }else {
                    $shop->collective_delivery = 0;
                }
            }
        }
        else{
            $this->validator($request->all(), 'slider')->validate();
            if($request->has('previous_sliders')){
                $sliders = $request->previous_sliders;
            }
            else{
                $sliders = array();
            }

            if($request->hasFile('sliders')){
                foreach ($request->sliders as $key => $slider) {
                    array_push($sliders, $slider->store('uploads/shop/sliders'));
                }
            }

            $shop->sliders = json_encode($sliders);
        }

        if($shop->save()){
            flash(__('Your Shop has been updated successfully!'))->success();
            return back();
        }

        flash(__('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function verify_form(Request $request)
    {
        if(Auth::user()->seller->verification_info == null){
            $shop = Auth::user()->shop;
            return view('frontend.seller.verify_form', compact('shop'));
        }
        else {
            flash(__('Sorry! You have sent verification request already.'))->error();
            return back();
        }
    }

    public function verify_form_store(Request $request)
    {
        $data = array();
        $i = 0;
        foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
            $item = array();
            if ($element->type == 'text') {
                $item['type'] = 'text';
                $item['label'] = $element->label;
                $item['value'] = $request['element_'.$i];
            }
            elseif ($element->type == 'select' || $element->type == 'radio') {
                $item['type'] = 'select';
                $item['label'] = $element->label;
                $item['value'] = $request['element_'.$i];
            }
            elseif ($element->type == 'multi_select') {
                $item['type'] = 'multi_select';
                $item['label'] = $element->label;
                $item['value'] = json_encode($request['element_'.$i]);
            }
            elseif ($element->type == 'file') {
                $item['type'] = 'file';
                $item['label'] = $element->label;
                $item['value'] = $request['element_'.$i]->store('uploads/verification_form');
            }
            array_push($data, $item);
            $i++;
        }
        $seller = Auth::user()->seller;
        $seller->verification_info = json_encode($data);
        if($seller->save()){
            flash(__('Your shop verification request has been submitted successfully!'))->success();
            return redirect()->route('dashboard');
        }

        flash(__('Sorry! Something went wrong.'))->error();
        return back();
    }
}
