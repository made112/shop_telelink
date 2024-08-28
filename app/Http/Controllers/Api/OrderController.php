<?php

namespace App\Http\Controllers\Api;

use App\City;
use App\Http\Controllers\BisanController;
use App\Http\Controllers\OTPVerificationController;
use App\Models\BusinessSetting;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Shop;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class OrderController extends Controller
{
    public function processOrder(Request $request)
    {
        $req = json_decode($request->getContent())->order;
        $details = [];
        $details['start_time'] = Date::now();
        $status_paid = 'unpaid';
//        if (!isset($req->customer_id) || $req->customer_id == null) {
//            return response()->json([
//                'message' => 'User ID not found',
//                'status' => 'error'
//            ], 401);
//        }
        if (!isset($req->shipping)) {
            return response()->json([
                'message' => translate('Shipping Address must be required'),
                'status' => 'error'
            ], 401);
        }
        if (!isset($req->payment_method)) {
            return response()->json([
                'message' => translate('Payment Method must be required'),
                'status' => 'error'
            ], 401);
        }
        if (!isset($req->line_items)) {
            return response()->json([
                'message' => translate('Cart Items is empty'),
                'status' => 'error'
            ], 401);
        }
        $status = 'pending';
        if (!isset($req->total)) {
            return response()->json([
                'message' => translate('Total must be required'),
                'status' => 'error'
            ], 401);
        }
        if (isset($req->shipping->city)) {
            $search_for_city = $req->shipping->city;
            $city = City::where('name', 'like', '%' . $search_for_city . '%')->orWhere('nameAr', 'like', '%' . $search_for_city . '%')->first();
            if (!$city) {
                return response()->json([
                    'success' => false,
                    'message' => translate('select a valid city')
                ], 404);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => translate('select a valid city')
            ], 404);
        }
        $shippingAddress = (object)[
            "name" => isset($req->shipping->first_name) ? $req->shipping->first_name : '' . ' ' . isset($req->shipping->last_name) ? $req->shipping->last_name : '',
            "address" => isset($req->shipping->address_1) ? $req->shipping->address_1 : '',
            "email" => isset($req->shipping->email) ? $req->shipping->email : '',
            "phone" => isset($req->shipping->phone) ? $req->shipping->phone : '',
            "city" => isset($req->shipping->city) ? $req->shipping->city : '',
            "postal_code" => isset($req->shipping->postcode) ? $req->shipping->postcode : '',
            "country" => isset($req->shipping->country) ? $req->shipping->country : '',
            "checkout_type" => "logged"
        ];
        $coupon_discount = 0;
        if (isset($req->coupon_discount)) {
            $coupon_discount = $req->coupon_discount;
        }
        $user = null;
        if ($req->customer_id && $req->customer_id != 'null') {
            $user = User::findOrFail($req->customer_id);
        } else {
            if ($req->shipping->email) {
                $user = User::where('email', $req->shipping->email)->first();
            }
        }
        // create an order
        $order = Order::create([
            'user_id' => $user->id,
            'shipping_address' => json_encode($shippingAddress),
            'payment_type' => $req->payment_method->id,
            'payment_status' => $status_paid,
            'grand_total' => $req->total - $coupon_discount,
            'coupon_discount' => $coupon_discount,
            'code' => date('Ymd-his'),
            'date' => strtotime('now')
        ]);

        $cartItems = $req->line_items;
        $shipping_method = 'direct_delivery';
        if (isset($req->shipping_lines)) {
            if ($req->shipping_lines[0]->method_id == 'collective_delivery') {
                $shipping_method = 'collective_delivery';
            } elseif ($req->shipping_lines[0]->method_id == 'free_delivery') {
                $shipping_method = 'free_delivery';
            }
        }
        // save order details
        $shipping = 0;
        $admin_products = array();
        $seller_products = array();

        if (BusinessSetting::where('type', 'shipping_type')->first()->value == 'flat_rate') {
            $shipping = BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
        } elseif (BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping') {
            $cart = $req->line_items;
            $sellers = [];
            foreach ($cartItems as $key => $cartItem) {
                $product = Product::findOrFail($cartItem->product_id);
                if ($product) {
                    if (!key_exists($product->user_id, $sellers)) {
                        $sellers[$product->user_id] = [
                            'id' => $product->user_id,
                            'type' => $product->added_by,
                            'deal_with' => $product->added_by == 'seller' ? Shop::where('user_id', $product->user_id)->first()->deal_with : 'admin',
                            'items' => [
                                (array)$cartItem
                            ]
                        ];
                    } else {
                        array_push($sellers[$product->user_id]['items'], (array)$cartItem);
                    }
                }
            }

            if (count($sellers) > 0) {
                $free_shipping = intval(BusinessSetting::where('type', 'free_shipping')->first()->value);
                $city = City::find($city->id);
                foreach ($sellers as $index => $seller) {
                    if (($seller['type'] == 'admin') || ($seller['type'] == 'seller' && $seller['deal_with'] == 1)) {
                        $total_price = $this->getTotalOrder($seller['id'], $cart);
                        if (count($seller['items']) > 0) {
                            $count_items = count($seller['items']);
                            $price_with_city = intval(BusinessSetting::where('type', $city->type)->first()->value);
                            foreach ($seller['items'] as $index2 => $item) {
                                if ($total_price > $free_shipping) {
                                    if ($city->type != 'occupied_interior') {
                                        $sellers[$index]['items'][$index2]['shipping'] = 0;
                                    } else {
                                        $sellers[$index]['items'][$index2]['shipping'] = $price_with_city / $count_items;
                                    }
                                } else {
                                    if ($shipping_method == 'collective_delivery') {
                                        $sellers[$index]['items'][$index2]['shipping'] = ($price_with_city / $count_items) / 2;
                                    } else {
                                        $sellers[$index]['items'][$index2]['shipping'] = $price_with_city / $count_items;
                                    }
                                }
                            }
                        }
                    } else {
                        $seller_shop = Shop::where('user_id', $index)->first();
                        $total_price = $this->getTotalOrder($seller['id'], $cart);
                        if (count($seller['items']) > 0) {
                            $count_items = count($seller['items']);
                            foreach ($seller['items'] as $index3 => $item) {
                                if ($total_price > $seller_shop->shipping_free) {
                                    if ($city->type != 'occupied_interior') {
                                        $sellers[$index]['items'][$index3]['shipping'] = 0;
                                    } else {
                                        $sellers[$index]['items'][$index3]['shipping'] = $seller_shop->shipping_cost_oi / $count_items;
                                    }
                                } else {
                                    if ($city->type == 'occupied_interior') {
                                        $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_oi / $count_items);
                                    } elseif ($city->type == 'west_bank') {
                                        $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_wb / $count_items);
                                    } elseif ($city->type == 'jerusalem') {
                                        $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_j / $count_items);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $cartItems = array();
            foreach ($sellers as $seller) {
                if (count($seller['items']) > 0) {
                    foreach ($seller['items'] as $key => $item) {
                        array_push($cartItems, (object)[
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'shipping' => $item['shipping'],
                            'variation_id' => isset($item['variation_id']) ? $item['variation_id'] : null
                        ]);
                    }
                }
            }
        }

//        Get Total Price For Order
        $total = 0;
        $flag_not_available = false;
        foreach ($cartItems as $cartItem) {
            $product = Product::findOrFail($cartItem->product_id);
            if (isset($cartItem->variation_id)) {
                $cartItemVariation = $cartItem->variation_id;
                $product_stocks = $product->stocks->find($cartItem->variation_id);
                if ($product_stocks && $product_stocks->qty >= $cartItem->quantity) {
                    $product_stocks->qty -= $cartItem->quantity;
                    $product_stocks->save();
                } else {
                    $flag_not_available = true;
                }
            } else {
                if ($product->current_stock >= $cartItem->quantity) {
                    $product->update([
                        'current_stock' => DB::raw('current_stock - ' . $cartItem->quantity)
                    ]);
                } else {
                    $flag_not_available = true;
                }
            }
//            $order_detail_shipping_cost = 0;
//
//            if (BusinessSetting::where('type', 'shipping_type')->first()->value == 'flat_rate') {
//                if ($total >= 150) {
//                    $order_detail_shipping_cost = 0;
//                }else {
//                    $order_detail_shipping_cost = $shipping/count($cartItems);
//                }
//            }
//            elseif (BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping') {
//                $order_detail_shipping_cost = $shipping;
//            }
//            else{
//                $order_detail_shipping_cost = $product->shipping_cost;
//            }

            $price = $product->unit_price;

            if (isset($cartItem->variation_id)) {
                $price = $product_stocks->discount;
            }

            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }


            $details['before_create_order_details'] = Date::now();

            OrderDetail::create([
                'order_id' => $order->id,
                'seller_id' => $product->user_id,
                'product_id' => $product->id,
                'variation' => isset($product_stocks) && isset($product_stocks->variant) ? $product_stocks->variant : null,
                'price' => $price * $cartItem->quantity,
                'tax' => (isset($cartItem->tax) ? $cartItem->tax : 0) * $cartItem->quantity,
                'shipping_cost' => $product->category->id == 23 ? 0 : $cartItem->shipping,
                'quantity' => $cartItem->quantity,
                'payment_status' => $status_paid,
                'shipping_type' => 'home_delivery',
                'delivery_type' => $shipping_method,
                'delivery_status' => $status
            ]);

            $details['before_create_order_details'] = Date::now();

            $product->update([
                'num_of_sale' => DB::raw('num_of_sale + ' . $cartItem->quantity)
            ]);
        }

//        if ($flag_not_available) {
//            return response()->json([
//                'status' => false,
//                'message' => 'Required quantity exceed of available inventory.'
//            ], 404);
//        }

        // apply coupon usage
        if (isset($req->coupon_code)) {
            CouponUsage::create([
                'user_id' => $req->customer_id,
                'coupon_id' => Coupon::where('code', $req->coupon_code)->first()->id
            ]);
        }

        if (isset($req->delivery_type)) {
            $order->delivery_type = $req->delivery_type;
        }

        // calculate commission
        $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
        foreach ($order->orderDetails as $orderDetail) {
            if ($orderDetail->product->user->user_type == 'seller') {
                $seller = $orderDetail->product->user->seller;
                $price = $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
                $seller->admin_to_pay = ($req->payment_method == 'cash_on_delivery') ? $seller->admin_to_pay - ($price * $commission_percentage) / 100 : $seller->admin_to_pay + ($price * (100 - $commission_percentage)) / 100;
                $seller->save();
            }
        }
        // clear user's cart

        $user = null;
        if ($req->customer_id && $req->customer_id != 'null') {
            $user = User::findOrFail($req->customer_id);
        } else {
            if ($req->shipping->email) {
                $user = User::where('email', $req->shipping->email)->first();
            }
        }
        $user->carts()->delete();

        $line_items = array();
        foreach ($order->orderDetails as $orderDetail) {
            array_push($line_items, [
                'product_id' => $orderDetail->product_id,
                'name' => $orderDetail->product->name,
                'quantity' => $orderDetail->quantity,
                'total' => ($orderDetail->quantity * $orderDetail->price) . ''
            ]);
        }

        /*
         * Integration With Bisan System
         * Send Data To Method storeWithBisan to handle it,
         * send to Bisan system
         * */
        $details['before_bisan'] = Date::now();
        if (BusinessSetting::where('type', 'bisan_system')->first()->value == 1) {
            $flag = false;
            if ($order->orderDetails) {
                foreach ($order->orderDetails as $od) {
                    if ($od->product && $od->product->added_by == 'admin') {
                        $flag = true;
                    }
                }
            }
            if ($flag) {
                $bisan_order = new BisanController;
                $bisan_order->storeWithBisan($order);
            }
        }
        $details['after_bisan'] = Date::now();

//        Send SMS For User
        $payments = ['visa', 'jawwal_pay'];
        if (!in_array($order->payment_type, $payments)) {
            $details['before_sms'] = Date::now();
            if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_order')->first()->value) {
                try {
                    $otpController = new OTPVerificationController;
                    $otpController->send_order_code($order);
                } catch (\Exception $e) {

                }
            }
            $details['after_sms'] = Date::now();
        }

        $details['end_time'] = Date::now();

        $order->details = json_encode($details);
        return response()->json([
            'success' => true,
            'message' => translate('Your order has been placed successfully'),
            'data' => [
                "id" => $order->id,
                "customer_note" => "Customer Note",
                "code" => $order->code,
                "status " => $order->payment_status,
                "date_created" => $order->created_at,
                "date_modified" => $order->update_at,
                "total" => $order->grand_total,
                "total_tax" => 0,
                "payment_method_title" => $order->payment_type,
                "line_items" => $line_items
            ]
        ]);
    }

    public function store(Request $request)
    {
        return $this->processOrder($request);
    }

    public function getShippingDelivery(Request $request)
    {
        //calculate shipping is to get shipping costs of different types
        $req = json_decode($request->getContent())->order;
        $total_direct = 0;
        if (isset($req->shipping->city)) {
            $search_for_city = $req->shipping->city;
            $city = City::where('name', 'like', '%' . $search_for_city . '%')->orWhere('nameAr', 'like', '%' . $search_for_city . '%')->first();
            if (!$city) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => translate('select a valid city')
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('select a valid city')
            ]);
        }
        if (!isset($req->line_items)) {
            return response()->json([
                'success' => false,
                'message' => translate('please add items in cart'),
                'status' => 404,
                'data' => []
            ], 404);
        }
        if (isset($req->line_items) && $req->line_items != null && count($req->line_items) > 0) {
            $data['city'] = $city->id;
            $cart = $req->line_items;
            $sellers = [];
            $flag_not_gift_card = 0;
            foreach ($cart as $key => $cartItem) {
                $product = Product::find($cartItem->product_id);
                if ($product) {
                    if ($product->category->id != 23) {
                        $flag_not_gift_card = 1;
                        if (!key_exists($product->user_id, $sellers)) {
                            $sellers[$product->user_id] = [
                                'id' => $product->user_id,
                                'type' => $product->added_by,
                                'deal_with' => $product->added_by == 'seller' ? Shop::where('user_id', $product->user_id)->first()->deal_with : 'admin',
                                'items' => [
                                    (array)$cartItem
                                ]
                            ];
                        } else {
                            array_push($sellers[$product->user_id]['items'], (array)$cartItem);
                        }
                    }
                }
            }

            if (!$flag_not_gift_card) {
                $free_delivery = BusinessSetting::where('type', 'free_delivery')->where('value', 1)->first(['type']);
                if ($free_delivery) {
                    return response()->json([
                        'success' => true,
                        'data' => [[
                            'id' => 1,
                            'type' => $free_delivery->type,
                            'name' => 'Free Delivery',
                            'shipping' => 0
                        ]]
                    ]);
                }
            }
            if (count($sellers) > 0) {
                $free_shipping = intval(BusinessSetting::where('type', 'free_shipping')->first()->value);
                $city = City::find($data['city']);
                foreach ($sellers as $index => $seller) {
                    if (($seller['type'] == 'admin') || ($seller['type'] == 'seller' && $seller['deal_with'] == 1)) {
                        $total_price = $this->getTotalOrder($seller['id'], $cart);
                        if (count($seller['items']) > 0) {
                            $count_items = count($seller['items']);
                            $price_with_city = intval(BusinessSetting::where('type', $city->type)->first()->value);
                            foreach ($seller['items'] as $index2 => $item) {
                                if ($total_price > $free_shipping) {
                                    if ($city->type != 'occupied_interior') {
                                        $sellers[$index]['items'][$index2]['shipping'] = 0;
                                    } else {
                                        $sellers[$index]['items'][$index2]['shipping'] = $price_with_city / $count_items;
                                    }
                                } else {
                                    if (isset($seller['items'][$index2]['delivery_type']) && $seller['items'][$index2]['delivery_type'] == 'collective_delivery') {
                                        $sellers[$index]['items'][$index2]['shipping'] = ($price_with_city / $count_items) / 2;
                                    } else {
                                        $sellers[$index]['items'][$index2]['shipping'] = $price_with_city / $count_items;
                                    }
                                }
                            }
                        }
                    } else {
                        $seller_shop = Shop::where('user_id', $index)->first();
                        $total_price = $this->getTotalOrder($seller['id'], $cart);
                        if (count($seller['items']) > 0) {
                            $count_items = count($seller['items']);
                            foreach ($seller['items'] as $index3 => $item) {
                                if ($total_price > $seller_shop->shipping_free) {
                                    if ($city->type != 'occupied_interior') {
                                        $sellers[$index]['items'][$index3]['shipping'] = 0;
                                    } else {
                                        $sellers[$index]['items'][$index3]['shipping'] = $seller_shop->shipping_cost_oi / $count_items;
                                    }
                                } else {
                                    if ($city->type == 'occupied_interior') {
                                        $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_oi / $count_items);
                                    } elseif ($city->type == 'west_bank') {
                                        $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_wb / $count_items);
                                    } elseif ($city->type == 'jerusalem') {
                                        $sellers[$index]['items'][$index3]['shipping'] = ($seller_shop->shipping_cost_j / $count_items);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($sellers as $se) {
                if (count($se['items']) > 0) {
                    foreach ($se['items'] as $item) {
                        $total_direct += $item['shipping'];
                    }
                }
            }
        }
        $delivery_types = BusinessSetting::whereIn('type', ['collective_delivery', 'direct_delivery'])->where('value', 1)->orderBy('type', 'asc')->get(['type']);
        foreach ($delivery_types as $delivery_type) {
            if ($delivery_type->type == 'direct_delivery') {
                $delivery_type->id = 1;
                $delivery_type->name = 'Direct Delivery';
                $delivery_type->shipping = doubleval($total_direct);
            }
            if ($delivery_type->type == 'collective_delivery') {
                $delivery_type->id = 2;
                $delivery_type->name = 'Collective Delivery';
                $delivery_type->shipping = doubleval($total_direct / 2);
            }
        }
        return response()->json([
            'success' => true,
            'data' => $delivery_types
        ]);
    }

    private function getTotalOrder($id, $cart)
    {
        if ($cart && count($cart) > 0) {
            $subtotal = 0;
            foreach ($cart as $key => $cartItem) {
                $product = Product::find($cartItem->product_id);
                if ($product && $product->user_id == $id) {
                    $subtotal += $product->unit_price * $cartItem->quantity;
                }
            }
            return round($subtotal);
        }
        return 0;
    }
}
