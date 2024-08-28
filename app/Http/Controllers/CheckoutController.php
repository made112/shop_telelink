<?php

namespace App\Http\Controllers;

use App\Address;
use App\BusinessSetting;
use App\Category;
use App\ClubPoint;
use App\Coupon;
use App\CouponUsage;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\PaytmController;
use App\Order;
use App\Shop;
use App\Utility\PayhereUtility;
use Auth;
use Illuminate\Http\Request;
use Jawwalpay\RestApi\JawwalPay;
use Session;

class CheckoutController extends Controller
{

    public function __construct()
    {
        //
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {
        if ($request->payment_option != null) {

            $orderController = new OrderController;


            if(! $orderController->store($request)) {
                flash(translate("Required quantity exceed of available inventory."))->error();
                return redirect()->route('cart');
            } else {
                $request->session()->put('payment_type', 'cart_payment');

                if($request->session()->get('order_id') != null){
                    if($request->payment_option == 'paypal'){
                        $paypal = new PaypalController;
                        return $paypal->getCheckout();
                    }
                    elseif($request->payment_option == 'visa'){
                        $visa = new VisaController();
                        return $visa->getCheckout();
                    }
                    elseif($request->payment_option == 'jawwal_pay'){
                        if ($request->session()->get('delivery_type') == 'default'){
                            $order = Order::findOrFail($request->session()->get('order_id'));
                            $order_id = $order->id;
                            $amount = $order->grand_total;
                            $first_name = json_decode($order->shipping_address)->name;
                            $last_name = 'X';
                            $phone = json_decode($order->shipping_address)->phone;
                            $email = json_decode($order->shipping_address)->email;
                            $address = json_decode($order->shipping_address)->address;
                            $city = json_decode($order->shipping_address)->city;

                            $shipping_data = array(
                                "fname" => $first_name,
                                "lname" => $last_name,
                                "phone" => $phone,
                                "email" => $email,
                                "address" => $address,
                                "city" => $city
                            );
                            $jawwalpay = new JawwalPay();
                            return $jawwalpay->checkout($order_id, $amount, $shipping_data);
                        }else {
                            flash(translate('This payment method does not support multiple vendors, please select another payment method.'))->warning();
                            return redirect()->route('checkout.payment_info');
                        }
                    }
                    elseif ($request->payment_option == 'stripe') {
                        $stripe = new StripePaymentController;
                        return $stripe->stripe();
                    }
                    elseif ($request->payment_option == 'sslcommerz') {
                        $sslcommerz = new PublicSslCommerzPaymentController;
                        return $sslcommerz->index($request);
                    }
                    elseif ($request->payment_option == 'instamojo') {
                        $instamojo = new InstamojoController;
                        return $instamojo->pay($request);
                    }
                    elseif ($request->payment_option == 'razorpay') {
                        $razorpay = new RazorpayController;
                        return $razorpay->payWithRazorpay($request);
                    }
                    elseif ($request->payment_option == 'voguepay') {
                        $voguePay = new VoguePayController;
                        return $voguePay->customer_showForm();
                    }
                    elseif ($request->payment_option == 'twocheckout') {
                        $twocheckout = new TwoCheckoutController;
                        return $twocheckout->index($request);
                    }
                    elseif ($request->payment_option == 'payhere') {
                        $order = Order::findOrFail($request->session()->get('order_id'));

                        $order_id = $order->id;
                        $amount = $order->grand_total;
                        $first_name = json_decode($order->shipping_address)->name;
                        $last_name = 'X';
                        $phone = json_decode($order->shipping_address)->phone;
                        $email = json_decode($order->shipping_address)->email;
                        $address = json_decode($order->shipping_address)->address;
                        $city = json_decode($order->shipping_address)->city;

                        return PayhereUtility::create_checkout_form($order_id, $amount, $first_name, $last_name, $phone, $email, $address, $city);
                    }
                    elseif ($request->payment_option == 'paytm') {
                        $paytm = new PaytmController;
                        return $paytm->index();
                    }
                    elseif (!isProductDigit() && $request->payment_option == 'cash_on_delivery') {
                        $request->session()->put('cart', collect([]));
                        // $request->session()->forget('order_id');
                        $request->session()->forget('delivery_info');
                        $request->session()->forget('coupon_id');
                        $request->session()->forget('coupon_discount');

                        flash(translate("Your order has been placed successfully"))->success();
                        return redirect()->route('order_confirmed');
                    }
                    elseif ($request->payment_option == 'wallet') {
                        $user = Auth::user();
                        $user->balance -= Order::findOrFail($request->session()->get('order_id'))->grand_total;
                        $user->save();
                        return $this->checkout_done($request->session()->get('order_id'), null);
                    }
                    else{
                        $order = Order::findOrFail($request->session()->get('order_id'));
                        $order->manual_payment = 1;
                        $order->save();

                        $request->session()->put('cart', collect([]));
                        // $request->session()->forget('order_id');
                        $request->session()->forget('delivery_info');
                        $request->session()->forget('coupon_id');
                        $request->session()->forget('coupon_discount');

                        flash(translate('Your order has been placed successfully. Please submit payment information from purchase history'))->success();
                        return redirect()->route('order_confirmed');
                    }
                }
            }

        }else {
            flash(translate('Select Payment Option.'))->warning();
            return back();
        }
    }

    //redirects to this method after a successfull checkout
    public function checkout_done($order_id, $payment)
    {
        $order = Order::findOrFail($order_id);
        $order->payment_status = 'paid';
        $order->payment_details = $payment;
        $order->save();

        if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
            $affiliateController = new AffiliateController;
            $affiliateController->processAffiliatePoints($order);
        }

        if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
            $clubpointController = new ClubPointController;
            $clubpointController->processClubPoints($order);
        }

        if(\App\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated){
            if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if($orderDetail->product->user->user_type == 'seller'){
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price*(100-$commission_percentage))/100 + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            }
            else{
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if($orderDetail->product->user->user_type == 'seller'){
                        $commission_percentage = $orderDetail->product->category->commision_rate;
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price*(100-$commission_percentage))/100  + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            }
        }
        else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = 'paid';
                $orderDetail->save();
                if($orderDetail->product->user->user_type == 'seller'){
                    $seller = $orderDetail->product->user->seller;
                    $seller->admin_to_pay = $seller->admin_to_pay + $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
                    $seller->save();
                }
            }
        }

        $order->commission_calculated = 1;
        $order->save();

        $payments = ['visa', 'jawwal_pay'];
        if (in_array($order->payment_type, $payments)) {
            if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_order')->first()->value){
                try {
                    $otpController = new OTPVerificationController;
                    $otpController->send_order_code($order);
                } catch (\Exception $e) {

                }
            }
        }

        Session::put('cart', collect([]));
        // Session::forget('order_id');
        Session::forget('payment_type');
        Session::forget('delivery_info');
        Session::forget('coupon_id');
        Session::forget('coupon_discount');

        flash(translate('Payment completed'))->success();
        return view('frontend.order_confirmed', compact('order'));
    }

    public function get_shipping_info(Request $request)
    {
        if (isProductDigit()) {
            $total = getTotalFromCart();
            return view('frontend.payment_select', compact('total'));
        }
        $request->session()->put('delivery_type', 'default');
        if(Session::has('cart') && count(Session::get('cart')) > 0){
            $categories = Category::all();
            return view('frontend.shipping_info', compact('categories'));
        }
        flash(translate('Your cart is empty'))->success();
        return back();
    }

    public function get_delivery_info(Request $request)
    {
        if (isProductDigit()) {
            $total = getTotalFromCart();
            return view('frontend.payment_select', compact('total'));
        }
        if(Session::has('cart') && count(Session::get('cart')) > 0){
            return view('frontend.delivery_info');
        }
        flash(translate('Your cart is empty'))->success();
        return back();
    }

    public function store_shipping_info(Request $request)
    {
        if (Auth::check()) {
            if($request->address_id == null){
                flash(translate("Please add shipping address"))->warning();
                return back();
            }
            $address = Address::findOrFail($request->address_id);
            $data['name'] = Auth::user()->name;
            $data['email'] = Auth::user()->email;
            $data['address'] = $address->address;
            $data['country'] = $address->country;
            $data['city'] = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->phone;
            $data['checkout_type'] = $request->checkout_type;
        }
        else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = $request->country;
            $data['city'] = $request->city;
            $data['postal_code'] = $request->postal_code;
            $data['phone'] = $request->phone;
            $data['checkout_type'] = $request->checkout_type;
        }

        $shipping_info = $data;
        $request->session()->put('shipping_info', $shipping_info);
        if(Session::has('cart') && count(Session::get('cart')) > 0) {
            $cart = $request->session()->get('cart', collect([]));
            $cart = $cart->map(function ($object, $key1) use ($request) {

                $sellers = [];
                if (Session::get('cart') && count(Session::get('cart')) > 0) {
                    foreach (Session::get('cart') as $key => $cartItem) {
                        $product = \App\Product::find($cartItem['id']);
                        if ($product){
                            if (! key_exists($product->user_id, $sellers)) {
                                $sellers[$product->user_id] = [
                                    'id' => $product->user_id,
                                    'type' => $product->added_by,
                                    'deal_with' => $product->added_by == 'seller' ? Shop::where('user_id', $product->user_id)->first()->deal_with : 'admin',
                                    'items' => [
                                        $cartItem
                                    ]
                                ];
                            }else {
                                array_push($sellers[$product->user_id]['items'], $cartItem);
                            }
                        }
                    }
                    if (count($sellers) > 0) {
                        $free_shipping = intval(\App\BusinessSetting::where('type', 'free_shipping')->first()->value);
                        $city = \App\City::find(request()->session()->get('shipping_info')['city']);
                        foreach ($sellers as $index =>$seller) {
                            if (($seller['type'] == 'admin') || ($seller['type'] == 'seller' && $seller['deal_with'] == 1)) {
                                $total_price = getTotalSellerPorductsInOrder($seller['id']);
                                if (count($seller['items']) > 0) {
                                    $count_items = count($seller['items']);
                                    $price_with_city = intval(BusinessSetting::where('type', $city->type)->first()->value);
                                    foreach ($seller['items'] as $index2 => $item) {
                                        if ($total_price > $free_shipping) {
                                            if ($city->type != 'occupied_interior') {
                                                $sellers[$index]['items'][$index2]['shipping'] = 0;
                                            }else {
                                                $sellers[$index]['items'][$index2]['shipping'] = $price_with_city / $count_items;
                                            }
                                        }else {
                                            if (isset($seller['items'][$index2]['delivery_type']) && $seller['items'][$index2]['delivery_type'] == 'collective_delivery') {
                                                $sellers[$index]['items'][$index2]['shipping'] = ($price_with_city / $count_items) / 2;
                                            } else{
                                                $sellers[$index]['items'][$index2]['shipping'] = $price_with_city / $count_items;
                                            }
                                        }
                                    }
                                }
                            }else {
                                $seller_shop = Shop::where('user_id', $index)->first();
                                $total_price = getTotalSellerPorductsInOrder($seller['id']);
                                if (count($seller['items']) > 0) {
                                    $count_items = count($seller['items']);
                                    foreach ($seller['items'] as $index3 => $item) {
                                        if($total_price > $seller_shop->shipping_free) {
                                            if ($city->type != 'occupied_interior') {
                                                $sellers[$index]['items'][$index3]['shipping'] = 0;
                                            }else {
                                                $sellers[$index]['items'][$index3]['shipping'] = $seller_shop->shipping_cost_oi / $count_items;
                                            }
                                        } else {
                                            if (isset($seller['items'][$index3]['delivery_type']) && $seller['items'][$index3]['delivery_type'] == 'collective_delivery') {
                                                if ($city->type == 'occupied_interior'){
                                                    $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_oi / $count_items) / 2;
                                                }elseif ($city->type == 'west_bank') {
                                                    $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_wb / $count_items) / 2;
                                                }elseif ($city->type == 'jerusalem') {
                                                    $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_j / $count_items) / 2;
                                                }
                                            }else {
                                                if ($city->type == 'occupied_interior'){
                                                    $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_oi / $count_items);
                                                }elseif ($city->type == 'west_bank') {
                                                    $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_wb / $count_items);
                                                }elseif ($city->type == 'jerusalem') {
                                                    $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_j / $count_items);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $cart_item = collect();
                foreach ($sellers as $se) {
                    if (count($se['items']) > 0) {
                        foreach ($se['items'] as $item) {
                            $cart_item->add($item);
                        }
                    }
                }
                return $cart_item[$key1];
            });
            $request->session()->put('cart', $cart);
        }
        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        foreach (Session::get('cart') as $key => $cartItem){
            $subtotal += $cartItem['price']*$cartItem['quantity'];
            $tax += $cartItem['tax']*$cartItem['quantity'];
            $shipping += $cartItem['shipping']*$cartItem['quantity'];
        }

        $total = $subtotal + $tax + $shipping;

        if(Session::has('coupon_discount')){
                $total -= Session::get('coupon_discount');
        }

//        return view('frontend.delivery_info');
         return view('frontend.payment_select', compact('total'));
    }

    public function store_delivery_info(Request $request)
    {
        if(Session::has('cart') && count(Session::get('cart')) > 0){
            $cart = $request->session()->get('cart', collect([]));
            $cart = $cart->map(function ($object, $key) use ($request) {
                $product = \App\Product::find($object['id']);
                if($product->added_by == 'admin'){
                    if($request['shipping_type_admin'] == 'home_delivery'){
                        $object['shipping_type'] = 'home_delivery';
                    }
                    else{
                        $object['shipping_type'] = 'pickup_point';
                        $object['pickup_point'] = $request->pickup_point_id_admin;
                    }
                    if ($request->has('shipping_type_delivery_admin')) {
                        $object['delivery_type'] = $request->get('shipping_type_delivery_admin');
                    }
                }
                else{
                    $seller_shop = Shop::where('user_id', $product->user_id)->first();
                    if($request['shipping_type_'.$product->user_id] == 'home_delivery'){
                        $object['shipping_type'] = 'home_delivery';
                    }
                    else{
                        $object['shipping_type'] = 'pickup_point';
                        $object['pickup_point'] = $request['pickup_point_id_'.\App\Product::find($object['id'])->user_id];
                    }

                    if ($seller_shop->deal_with == 1) {
                        if ($request->has('shipping_type_delivery_admin')) {
                            $object['delivery_type'] = $request->get('shipping_type_delivery_admin');
                        }
                    }

                    if ($seller_shop->deal_with == 0 && $seller_shop->collective_delivery == 0) {
                        $object['delivery_type'] = 'direct_delivery';
                    }
                    if ($request->has('shipping_type_delivery_'.$product->user_id)) {
                        $object['delivery_type'] = $request->get('shipping_type_delivery_'.$product->user_id);
                    }
                }
                return $object;
            });
            $request->session()->put('cart', $cart);
//            $cart = $cart->map(function ($object, $key) use ($request) {
//                $object['shipping'] = getShippingCost($key);
//                return $object;
//            });

//            $request->session()->put('cart', $cart);

            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            foreach (Session::get('cart') as $key => $cartItem){
                $subtotal += $cartItem['price']*$cartItem['quantity'];
                $tax += $cartItem['tax']*$cartItem['quantity'];
                $shipping += $cartItem['shipping']*$cartItem['quantity'];
            }

            $total = $subtotal + $tax + $shipping;

            if(Session::has('coupon_discount')){
                    $total -= Session::get('coupon_discount');
            }

            return  redirect()->route('checkout.shipping_info');
//            return view('frontend.shipping_info');
        }
        else {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
    }

    public function get_payment_info(Request $request)
    {
        $total = getTotalFromCart();
        return view('frontend.payment_select', compact('total'));
    }

    public function apply_coupon_code(Request $request){
        //dd($request->all());
        $coupon = Coupon::where('code', $request->code)->first();
        $max_point = BusinessSetting::where('type', 'max_earn_point_user')->first()->value;
        if (ClubPoint::where('user_id', Auth::user()->id)->where('convert_status', 1)->sum('points') < $max_point) {
            if($coupon != null){
                if(strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date){
                    if(in_array(Auth::user()->id, json_decode($coupon->details)[0]->users)){
                        if($coupon->usage > 0){
                            if(CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null){
                                $coupon_details = json_decode($coupon->details);

                                if ($coupon->type == 'cart_base')
                                {
                                    $subtotal = 0;
                                    $tax = 0;
                                    $shipping = 0;
                                    foreach (Session::get('cart') as $key => $cartItem)
                                    {
                                        $subtotal += $cartItem['price']*$cartItem['quantity'];
                                        $tax += $cartItem['tax']*$cartItem['quantity'];
                                        $shipping += $cartItem['shipping']*$cartItem['quantity'];
                                    }
                                    $sum = $subtotal+$tax+$shipping;

                                    if ($sum > $coupon_details->min_buy) {
                                        if ($coupon->discount_type == 'percent') {
                                            $coupon_discount =  ($sum * $coupon->discount)/100;
                                            if ($coupon_discount > $coupon_details->max_discount) {
                                                $coupon_discount = $coupon_details->max_discount;
                                            }
                                        }
                                        elseif ($coupon->discount_type == 'amount') {
                                            $coupon_discount = $coupon->discount;
                                        }
                                        $request->session()->put('coupon_id', $coupon->id);
                                        $request->session()->put('coupon_discount', $coupon_discount);
                                        flash(translate('Coupon has been applied'))->success();
                                    }
                                }
                                elseif ($coupon->type == 'product_base')
                                {
                                    $coupon_discount = 0;
                                    foreach (Session::get('cart') as $key => $cartItem){
                                        foreach ($coupon_details as $key => $coupon_detail) {
                                            if($coupon_detail->product_id == $cartItem['id']){
                                                if ($coupon->discount_type == 'percent') {
                                                    $coupon_discount += $cartItem['price']*$coupon->discount/100;
                                                }
                                                elseif ($coupon->discount_type == 'amount') {
                                                    $coupon_discount += $coupon->discount;
                                                }
                                            }
                                        }
                                    }
                                    $coupon->usage = $coupon->usage -1;
                                    $coupon->save();
                                    $request->session()->put('coupon_id', $coupon->id);
                                    $request->session()->put('coupon_discount', $coupon_discount);
                                    flash(translate('Coupon has been applied'))->success();
                                }
                            }
                            else{
                                flash(translate('You already used this coupon!'))->warning();
                            }
                        } else {
                            flash(translate("Coupon usage expired!"))->warning();
                        }
                    } else {
                        flash(translate("You don't have coupon!"))->warning();
                    }
                }
                else{
                    flash(translate('Coupon expired!'))->warning();
                }
            }
            else {
                flash(translate('Invalid coupon!'))->warning();
            }
        } else {
            flash(translate('Coupon is disabled, because you have exceeded the maximum points limit ').$max_point)->warning();
        }
        return back();
    }

    public function remove_coupon_code(Request $request){
        $request->session()->forget('coupon_id');
        $request->session()->forget('coupon_discount');
        return back();
    }

    public function order_confirmed(){
        $order = Order::findOrFail(Session::get('order_id'));
        return view('frontend.order_confirmed', compact('order'));
    }
}
