<?php

namespace App\Http\Controllers;

use App\ProductStock;
use Illuminate\Http\Request;
use App\BusinessSetting;
use App\RefundRequest;
use App\OrderDetail;
use App\Seller;
use App\Wallet;
use App\User;
use Auth;

class RefundRequestController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //Store Customer Refund Request
    public function request_store(Request $request, $id)
    {
        $order_detail = OrderDetail::where('id', $id)->first();
        $refund = new RefundRequest;
        $refund->user_id = Auth::user()->id;
        $refund->order_id = $order_detail->order_id;
        $refund->order_detail_id = $order_detail->id;
        $refund->seller_id = $order_detail->seller_id;
        $refund->seller_approval = 0;
        if ($request->has('reason')){
            if ($request->reason === 'other' && $request->has('reason_details') && $request->reason_details != '' ) {
                $refund->reason = $request->reason_details;
            }else {
                $refund->reason = $request->reason;
            }
        }
        $refund->admin_approval = 0;
        $refund->admin_seen = 0;
        $refund->refund_amount = $order_detail->price + $order_detail->tax;
        $refund->refund_status = 0;
        if ($refund->save()) {
            flash("Refund Request has been sent successfully")->success();
            return redirect()->route('purchase_history.index');
        }
        else {
            flash("Something went wrong")->error();
            return back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendor_index()
    {
        $refunds = RefundRequest::where('seller_id', Auth::user()->id)->latest()->paginate(10);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('refund_request.frontend.recieved_refund_request.index', compact('refunds'));
        }
        else {
            return view('refund_request.frontend.recieved_refund_request.index', compact('refunds'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customer_index()
    {
        $refunds = RefundRequest::where('user_id', Auth::user()->id)->latest()->paginate(10);
        return view('refund_request.frontend.refund_request.index', compact('refunds'));
    }

    //Set the Refund configuration
    public function refund_config()
    {
        return view('refund_request.config');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund_time_update(Request $request)
    {
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        if ($business_settings != null) {
            $business_settings->value = $request->value;
            $business_settings->save();
        }
        else {
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            $business_settings->value = $request->value;
            $business_settings->save();
        }
        flash("Refund Request sending time has been updated successfully")->success();
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund_sticker_update(Request $request)
    {
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        if ($business_settings != null) {
            if($request->hasFile('logo')){
                $business_settings->value = $request->file('logo')->store('frontend/refund_sticker');
            }
            $business_settings->save();
        }
        else {
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            if($request->hasFile('logo')){
                $business_settings->value = $request->file('logo')->store('frontend/refund_sticker');
            }
            $business_settings->save();
        }
        flash("Refund Sticker has been updated successfully")->success();
        return back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_index(Request $request)
    {
        $sort_search = null;
        $refunds = RefundRequest::where('refund_status', 0)->latest();
        if ($request->search != null) {
            $search = $request->search;
            if (preg_match('/^[^a-zA-Z0-9]+$/', $request->search)) // '/[^a-z\d]/i' should also work.
            {
                $search = str_replace("\"", "", json_encode(strtolower($search)));
                $pos = strpos($search,'\\');
                if ($pos !== false) {
                    $str = substr($search,0,$pos+1) . str_replace('\\','\\\\',substr($search,$pos+1));
                }
                $search = $str;
            }
            $refunds = $refunds->whereHas('orderDetail', function ($q) use ($search) {
                $q->whereHas('product', function ($qu) use ($search) {
                    $qu->where('name', 'like', '%' . $search . '%');
                });
            });
            $sort_search = $request->search;
        }
        $refunds = $refunds->paginate(15);
        return view('refund_request.index', compact('refunds', 'sort_search'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paid_index(Request $request)
    {
        $sort_search = null;
        $refunds = RefundRequest::where('refund_status', 1)->latest();
        if ($request->search != null) {
            $search = $request->search;
            if (preg_match('/^[^a-zA-Z0-9]+$/', $request->search)) // '/[^a-z\d]/i' should also work.
            {
                $search = str_replace("\"", "", json_encode(strtolower($search)));
                $pos = strpos($search,'\\');
                if ($pos !== false) {
                    $str = substr($search,0,$pos+1) . str_replace('\\','\\\\',substr($search,$pos+1));
                }
                $search = $str;
            }
            $refunds = $refunds->whereHas('orderDetail', function ($q) use ($search) {
                $q->whereHas('product', function ($qu) use ($search) {
                    $qu->where('name', 'like', '%' . $search . '%');
                });
            });
            $sort_search = $request->search;
        }

        $refunds = $refunds->paginate(15);
        return view('refund_request.paid_refund', compact('refunds', 'sort_search'));
    }

    public function rejected_index(Request $request)
    {
        $sort_search = null;
        $refunds = RefundRequest::where('refund_status', -1)->latest();
        if ($request->search != null) {
            $search = $request->search;
            if (preg_match('/^[^a-zA-Z0-9]+$/', $request->search)) // '/[^a-z\d]/i' should also work.
            {
                $search = str_replace("\"", "", json_encode(strtolower($search)));
                $pos = strpos($search,'\\');
                if ($pos !== false) {
                    $str = substr($search,0,$pos+1) . str_replace('\\','\\\\',substr($search,$pos+1));
                }
                $search = $str;
            }
            $refunds = $refunds->whereHas('orderDetail', function ($q) use ($search) {
                $q->whereHas('product', function ($qu) use ($search) {
                    $qu->where('name', 'like', '%' . $search . '%');
                });
            });
            $sort_search = $request->search;
        }

        $refunds = $refunds->paginate(15);
        return view('refund_request.refund_rejected', compact('refunds', 'sort_search'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function request_approval_vendor(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->el);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->seller_approval = 1;
            $refund->admin_approval = 1;
        }
        else {
            $refund->seller_approval = 1;
        }

        if ($refund->save()) {
            return 1;
        }
        else {
            return 0;
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund_pay(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->el);
        if ($refund->seller_approval == 1) {
            $seller = Seller::where('user_id', $refund->seller_id)->first();
            if ($seller != null) {
                $seller->admin_to_pay -= $refund->refund_amount;
            }
            $seller->save();
        }
        $wallet = new Wallet;
        $wallet->user_id = $refund->user_id;
        $wallet->amount = $refund->refund_amount;
        $wallet->payment_method = 'Refund';
        $wallet->payment_details = 'Product Money Refund';
        $wallet->save();
        $user = User::findOrFail($refund->user_id);
        $user->balance += $refund->refund_amount;
        $user->save();
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->admin_approval = 1;
            $refund->refund_status = 1;
        }
        if ($refund->save()) {
            if ($refund->orderDetail != null) {
                if(intval($refund->orderDetail->quantity) > 0) {
                    if($refund->orderDetail->variation != null) {
                        $productStock = $refund->orderDetail->product->stocks->where('variant', $refund->orderDetail->variation)->first();
                        if ($productStock != null) {
                            $productStock->qty += intval($refund->orderDetail->quantity);
                            $productStock->save();
                        }
                    }else {
                        $product = $refund->orderDetail->product;
                        if ($product != null) {
                            $product->current_stock += intval($refund->orderDetail->quantity);
                            $product->save();
                        }
                    }
                }
            }
            return 1;
        }
        else {
            return 0;
        }
    }

    public function reject_request(Request $request) {
        $refund = RefundRequest::findOrFail($request->el);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->admin_approval = 0;
            $refund->refund_status = -1;
        }
        if ($refund->save()) {
            return 1;
        }
        else {
            return 0;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refund_request_send_page($id)
    {
        $order_detail = OrderDetail::findOrFail($id);
        if ($order_detail->product != null && $order_detail->product->refundable == 1) {
            return view('refund_request.frontend.refund_request.create', compact('order_detail'));
        }
        else {
            return back();
        }
    }

    /**
     * Show the form for view the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //Shows the refund reason
    public function reason_view($id)
    {
        $refund = RefundRequest::findOrFail($id);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            if ($refund->orderDetail != null) {
                $refund->admin_seen = 1;
                $refund->save();
                return view('refund_request.reason', compact('refund'));
            }
        }
        else {
            return view('refund_request.frontend.refund_request.reason', compact('refund'));
        }
    }
}
