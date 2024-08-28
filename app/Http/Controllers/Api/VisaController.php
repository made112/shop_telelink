<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\OTPVerificationController;
use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Http\Request;

class VisaController extends Controller
{
    public function getCheckout($order_id)
    {
        if (!isset($order_id)) {
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

        try {
            $version = config('visa.version');

            $acquirerID = config('visa.acquirerID');

            $merchantID = config('visa.merchantID');

            $responseURL = config('visa.responseURL_M');

            $captureFlag = config('visa.captureFlag');

            $password = config('visa.password');

            $signatureMethod = config('visa.signatureMethod');

            $orderID = $order_id;
            $order = Order::find(intval($orderID));
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ]);
            }

            $purchaseAmt = number_format((float)convert_to_usd($order->grand_total), 2, '.', '');
            $purchaseAmt = str_pad($purchaseAmt, 13, "0", STR_PAD_LEFT);

            $formattedPurchaseAmt = substr($purchaseAmt, 0, 10) . substr($purchaseAmt, 11);

            $currency = 840;

            $currencyExp = 2;

            $toEncrypt = $password . $merchantID . $acquirerID . $orderID . $formattedPurchaseAmt . $currency;

            $sha1Signature = sha1($toEncrypt);

            $base64Sha1Signature = base64_encode(pack("H*", $sha1Signature));
            return view('frontend.payment.visa', compact(['version', 'merchantID', 'acquirerID', 'responseURL', 'formattedPurchaseAmt', 'currency', 'currencyExp', 'orderID', 'captureFlag', 'base64Sha1Signature', 'signatureMethod']));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getResult(Request $request)
    {
        //Parameters returned
        try {
//            if ($request->ReasonCodeDesc == 'Declined' && $request->ResponseCode == 2) {
//                return response()->json([
//                    'status' => false,
//                    'message' => 'Order Declined'
//                ]);
//            }
            $fields = ['MerID', 'AcqID', 'OrderID', 'ResponseCode', 'ReasonCode', 'ReasonDescr', 'Ref', 'PaddedCardNo', 'Signature', 'verifySignature', 'ResponseCode', 'ReasonCode'];
            $MerID = $request->MerID;
            $AcqID = $request->AcqID;
            $OrderID = $request->OrderID;
            $ResponseCode = intval($request->ResponseCode);
            $ReasonCode = intval($request->ReasonCode);
            $ReasonDescr = $request->ReasonCodeDesc;
            $Ref = $request->ReferenceNo;
            $PaddedCardNo = $request->PaddedCardNo;
            $Signature = $request->Signature;
            //Authorization code is only returned in case of successful transaction,
            // indicated with a value of 1
            //for both response code and reason code
            if ($ResponseCode == 1 && $ReasonCode == 1) {
                $AuthNo = $request->AuthCode;
                array_push($fields, 'AuthNo');
            }
            //The parameters used for creating the signature as stored on the merchant server

            $acquirerID = config('visa.acquirerID');
            $merchantID = config('visa.merchantID');
            $password = config('visa.password');

//            $order = Order::findOrFail($OrderID);
//            if (!$order) {
//                return response()->json([
//                    'status' => false,
//                    'message' => 'There is no order, please try agian!'
//                ]);
//            }
            //Form the plaintext string that used to product the hash it sent by
            // concatenating
            // Password, Merchant ID, Acquirer ID and Order ID
            //This will give: 1234abcd | 0011223344 | 402971 | TestOrder12345 (spaces and |
            // introduced here for clarity)
            $toEncrypt = $password . $merchantID . $acquirerID . $OrderID;
            //Produce the hash using SHA1
            //This will give fed389f2e634fa6b62bdfbfafd05be761176cee9
            $sha1Signature = sha1($toEncrypt);
            //Encode the signature using Base64
            //This will give /tOJ8uY0+mtivfv6/QW+dhF2zuk=
            $expectedBase64Sha1Signature = base64_encode(pack("H*", $sha1Signature));
            // signature verification is performed simply by comparing the signature we
            // produced with the one sent
            $verifySignature = ($expectedBase64Sha1Signature == $Signature);
            return $this->checkout_done($OrderID, null);
            if ($verifySignature) {
                try {
                    return $this->checkout_done($OrderID, null);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => $e->getMessage()
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Signature verification does not match'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    public function checkout_done($order_id, $payment)
    {
        try {
            $order = Order::findOrFail($order_id);
            $order->payment_status = 'paid';
            $order->payment_details = $payment;
            $order->payment_type = 'visa';
            $order->save();
            if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                    $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                    if (isset($order->orderDetails)) {
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                                $seller->save();
                            }
                        }
                    }
                } else {
                    if (isset($order->orderDetails)) {
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $commission_percentage = $orderDetail->product->category->commision_rate;
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                                $seller->save();
                            }
                        }
                    }
                }
            }
            $order->commission_calculated = 1;
            $order->save();

            $payments = ['visa', 'jawwal_pay'];
            if (in_array($order->payment_type, $payments)) {
                if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_order')->first()->value) {
                    try {
                        $otpController = new OTPVerificationController;
                        $otpController->send_order_code($order);
                    } catch (\Exception $e) {

                    }
                }
            }

//            return view('webview.order_success', compact('order'));
            return response()->json([
                'order' => $order->id,
                'status' => 'success',
                'statusCode' => 200]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
