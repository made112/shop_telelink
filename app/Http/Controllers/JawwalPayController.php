<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Jawwalpay\RestApi\JawwalPay;

class JawwalPayController extends Controller
{
    public function checkout_done(Request $request) {
        if (($request->has('success') && $request->success == "true") && ($request->has('txn_response_code') && $request->txn_response_code == "200")) {
            if ($request->session()->get('order_id') != null) {
                $order = Order::find($request->session()->get('order_id'));
                if ($order) {
                    $checkoutController = new CheckoutController;
                    return $checkoutController->checkout_done($order->id, null);
                }else {
                    flash('There is not enough balance in your wallet!')->warning();
                    return redirect()->route('home');
                }
            }else {
                flash('There is not enough balance in your wallet!')->warning();
                return redirect()->route('home');
            }
        }else {
            flash('Something went wrong, please try again or contact with support team')->error();
            return redirect()->route('home');
        }
//        return response()->json($request, 200);
    }

}
