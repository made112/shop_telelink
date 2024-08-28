<?php

namespace Jawwalpay\RestApi;

use App\Order;
use App\Shop;

class JawwalPay {

    public function __construct()
    {
    }

    private function getData($username, $password) {
        $body = array(
            "username"=> $username,
            "password"=> $password,
            "hostname"=> config('jawwal-pay.hostname')
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://checkoutapi.jawwalpay.ps/api/auth/tokens',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    private function registerOrder($vendor, $order, $amount, $shipping_data) {
        $items = array();
        if ($order && count($order->orderDetails) > 0){
            foreach ($order->orderDetails as $key => $orderDetail) {
                array_push($items, [
                    "name" => $orderDetail->product->name,
                    "amount_cents" => ($orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost) * 100,
                    "description" => isset($orderDetail->product->description) && $orderDetail->product->description != null ? $orderDetail->product->description : 'Dummy Data Description',
                    "quantity" => $orderDetail->quantity
                ]);
            }
        }
        $body = array(
            "delivery_needed"=> "false",
            "merchant_id" => $vendor->profile->id,
            "amount_cents" => $amount * 100,
            "currency" => "ILS",
            "items" => $items,
            "shipping_data" => [
                "first_name" => $shipping_data['fname'],
                "phone_number" => $shipping_data['phone'],
                "last_name" => $shipping_data['lname'],
                "email" => isset($shipping_data['email']) && $shipping_data['email'] != null ? $shipping_data['email'] : 'dummy_test@test.com',
                "city" => getNameCity($shipping_data['city'])
            ]
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://checkoutapi.jawwalpay.ps/api/ecommerce/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$vendor->token
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    private function payment_key($vendor, $order_id,$shipping_data, $amount, $integration_id) {
        $body = array(
            "amount_cents" => $amount * 100,
            "expiration" => 3600,
            "order_id" => $order_id, // id obtained in step 2
            "billing_data" => [
                "apartment" => "000",
                "email" => isset($shipping_data['email']) && $shipping_data['email'] != null ? $shipping_data['email'] : 'dummy_test@test.com',
                "floor" => "00",
                "first_name" => $shipping_data['fname'],
                "street" => "Dummy Data",
                "building" => "0000",
                "phone_number" => $shipping_data['phone'],
                "shipping_method" => "Dummy Data",
                "postal_code" => "00000",
                "city" => getNameCity($shipping_data['city']),
                "country" => "PS",
                "last_name" => $shipping_data['lname'],
                "state" => "Dummy Data"
            ],
            "currency" => "ILS",
            "integration_id" => $integration_id, // integration_id will be provided upon signing up,
            "auth_token" => $vendor->token, //Token obtained from step1 ,
            "lock_order_when_paid" => "true"
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://checkoutapi.jawwalpay.ps/api/acceptance/payment_keys',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function checkout($order_id, $amount, $shipping_data) {
        try {
            $order = Order::findOrFail($order_id);
            if ($order->orderDetails->first()->product->added_by == 'admin'){
                $vendor = $this->getData(config('jawwal-pay.username'), config('jawwal-pay.password'));
                $iframe = config('jawwal-pay.iframe');
                $integration_id = config('jawwal-pay.integrationID');
            }else {
                $jawwal_payment = Shop::where('user_id', $order->orderDetails->first()->seller_id)->first()->jawwal_payment;
                if ($jawwal_payment && $jawwal_payment != null ) {
                    $det_account = json_decode($jawwal_payment);
                    $vendor = $this->getData($det_account->pay_username, $det_account->pay_password);
                    $iframe = $det_account->pay_iframe;
                    $integration_id = $det_account->pay_integration_id;
                } else {
                    flash('This seller dont have jawwal pay payment');
                    return redirect()->route('checkout.payment_info');;
                }
            }

            $key_order = $this->registerOrder($vendor, $order, $amount, $shipping_data);
            return redirect()->to('https://checkoutapi.jawwalpay.ps/api/acceptance/iframes/'.$iframe.'?payment_token='. $this->payment_key($vendor, $key_order->id,$shipping_data, $amount, $integration_id)->token);

        }catch (\Exception $e) {
            dd($e);
        }
    }

}
