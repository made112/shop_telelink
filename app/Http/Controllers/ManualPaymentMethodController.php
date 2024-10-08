<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ManualPaymentMethod;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AffiliateController;
use App\Order;
use App\Category;
use App\BusinessSetting;
use App\Coupon;
use App\CouponUsage;
use App\User;
use Session;
use Auth;

class ManualPaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manual_payment_methods = ManualPaymentMethod::all();
        return view('manual_payment_methods.index', compact('manual_payment_methods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('manual_payment_methods.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->hasFile('photo'))
        {
            $manual_payment_method = new ManualPaymentMethod;
            $manual_payment_method->type = $request->type;
            $manual_payment_method->photo = $request->photo->store('uploads/payment_method');
            $manual_payment_method->heading = $request->heading;
            $manual_payment_method->description = $request->description;

            if($request->type == 'bank_payment')
            {
                $banks_informations = array();
                for ($i=0; $i < count($request->bank_name); $i++) {
                    $item = array();
                    $item['bank_name'] = $request->bank_name[$i];
                    $item['account_name'] = $request->account_name[$i];
                    $item['account_number'] = $request->account_number[$i];
                    $item['routing_number'] = $request->routing_number[$i];
                    array_push($banks_informations, $item);
                }

                $manual_payment_method->bank_info = json_encode($banks_informations);
            }

            $manual_payment_method->save();
            flash(__('Method has been inserted successfully'))->success();
        }
        return redirect()->route('manual_payment_methods.index');
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
        $manual_payment_method = ManualPaymentMethod::findOrFail(decrypt($id));
        return view('manual_payment_methods.edit', compact('manual_payment_method'));
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
        $manual_payment_method = ManualPaymentMethod::findOrFail($id);
        $manual_payment_method->type = $request->type;
        $manual_payment_method->heading = $request->heading;
        $manual_payment_method->description = $request->description;

        if($request->type == 'bank_payment')
        {
            $banks_informations = array();
            for ($i=0; $i < count($request->bank_name); $i++) {
                $item = array();
                $item['bank_name'] = $request->bank_name[$i];
                $item['account_name'] = $request->account_name[$i];
                $item['account_number'] = $request->account_number[$i];
                $item['routing_number'] = $request->routing_number[$i];
                array_push($banks_informations, $item);
            }

            $manual_payment_method->bank_info = json_encode($banks_informations);
        }

        if($request->hasFile('photo')){
            $manual_payment_method->photo = $request->photo->store('uploads/payment_method');
        }

        $manual_payment_method->save();
        flash(__('Method has been inserted successfully'))->success();
        return redirect()->route('manual_payment_methods.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(ManualPaymentMethod::destroy($id)){
            flash(__('Method has been deleted successfully'))->success();
        }
        else{
            flash(__('Something went wrong'))->error();
        }
        return redirect()->route('manual_payment_methods.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show_payment_modal(Request $request)
    {
        $order = Order::find($request->order_id);
        if($order != null){
            return view('frontend.partials.payment_modal', compact('order'));
        }
        retrun;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function submit_offline_payment(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        if($request->name != null && $request->phone != null && $request->trx_id != null){
            $data['name'] = $request->name;
            $data['phone'] = $request->phone;
            $data['trx_id'] = $request->trx_id;
            if($request->hasFile('photo')){
                $path = $request->photo->store('uploads/manual_payment');
            }
            else {
                $path = null;
            }
            $data['photo'] = $path;
        }
        else {
            flash('Please fill all the fields')->warning();
            return back();
        }

        $order->manual_payment_data = json_encode($data);
        $order->payment_type = $request->payment_option;
        $order->payment_status = 'Submitted';
        $order->manual_payment = 1;

        $order->save();

        // if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
        //     $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
        //     foreach ($order->orderDetails as $key => $orderDetail) {
        //         $orderDetail->payment_status = 'paid';
        //         $orderDetail->save();
        //         if($orderDetail->product->user->user_type == 'seller'){
        //             $seller = $orderDetail->product->user->seller;
        //             $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price*(100-$commission_percentage))/100;
        //             $seller->save();
        //         }
        //     }
        // }
        // else{
        //     foreach ($order->orderDetails as $key => $orderDetail) {
        //         $orderDetail->payment_status = 'paid';
        //         $orderDetail->save();
        //         if($orderDetail->product->user->user_type == 'seller'){
        //             $commission_percentage = $orderDetail->product->category->commision_rate;
        //             $seller = $orderDetail->product->user->seller;
        //             $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price*(100-$commission_percentage))/100;
        //             $seller->save();
        //         }
        //     }
        // }

        flash(__('Your payment data has been submitted successfully'))->success();
        return redirect()->route('home');
    }

    public function offline_recharge_modal(Request $request)
    {
        return view('frontend.partials.offline_recharge_modal');
    }
}
