<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Jawwalpay\RestApi\JawwalPay;

class JawwalPayController extends Controller
{
    public function checkout($order_id) {
        if(! isset($order_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Order id invalid'
            ]);
        }
        if (gettype($order_id) != 'string') {
            return response()->json([
                'status' => false,
                'message' => 'Order id invalid'
            ]);
        }

        $order = Order::findOrFail($order_id);
        if ($order->delivery_type == 'default'){
            $amount = $order->grand_total;
            $first_name = isset(json_decode($order->shipping_address)->name) ? json_decode($order->shipping_address)->name : 'User Test';
            $last_name = 'X';
            $phone = isset(json_decode($order->shipping_address)->phone) ? json_decode($order->shipping_address)->phone : '0597777777';
            $email = isset(json_decode($order->shipping_address)->email) ? json_decode($order->shipping_address)->email : 'test@test.ps';
            $address = isset(json_decode($order->shipping_address)->address) ? json_decode($order->shipping_address)->address : 'Ramallah';
            $city = isset(json_decode($order->shipping_address)->city) ? json_decode($order->shipping_address)->city : '14';

            $shipping_data = array(
                "fname" => $first_name,
                "lname" => $last_name,
                "phone" => $phone,
                "email" => $email,
                "address" => $address,
                "city" => $city
            );
            $jawwalpay = new JawwalPay();
            return $jawwalpay->checkout($order->id, $amount, $shipping_data);
        }else {
            return response()->json([
               'message' => 'This payment method does not support multiple vendors, please select another payment method',
                'status' => false
            ]);
        }
    }
}
