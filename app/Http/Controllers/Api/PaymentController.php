<?php

namespace App\Http\Controllers\Api;

use App\BusinessSetting;
use App\ManualPaymentMethod;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function getPayments() {
        try {
            $data = array();
            $payments = BusinessSetting::whereIn('type', ['cash_payment', 'visa_payment', 'paypal_payment', 'jawwal_pay_payment'])->orderBy('type', 'asc')->get(['type', 'value']);
            foreach ($payments as $pay) {
                if ($pay->value == 1){
                    $pay->value = true;
                } else {
                    $pay->value = false;
                }
                if ($pay->type == 'cash_payment'){
                    $pay->desc = 'Cash On Delivery';
                }
                if ($pay->type == 'paypal_payment'){
                    $pay->desc = 'Paypal Payment';
                }
                if ($pay->type == 'visa_payment'){
                    $pay->desc = 'Visa Payment';
                }
                if ($pay->type == 'jawwal_pay_payment'){
                    $pay->desc = 'Jawwal Pay';
                }
                array_push($data, $pay);
            }
            $offline_payments = ManualPaymentMethod::select(['heading as name', 'type'])->get();
            foreach ($offline_payments as $offline_payment) {
                $offline_payment->value = true;
                if ($offline_payment->name == 'maalchat') {
                    $offline_payment->desc = 'Maalchat Payment';
                    $offline_payment->type = 'maalchat';
                }
                array_push($data, $offline_payment);
            }
            return response()->json([
                'data' => $data,
                'message' => 'success',
                'status' => 200
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'message' => $e
            ]);
        }
    }
    public function cashOnDelivery(Request $request)
    {
        $order = new OrderController;
        return $order->processOrder($request);
    }
}
