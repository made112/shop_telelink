<?php

namespace App\Http\Controllers;

use App\BusinessSetting;
use App\Order;
use Illuminate\Http\Request;
use Nexmo\Account\Config;
use Session;
class VisaController extends Controller
{
    public function getCheckout() {
        if(Session::has('payment_type')) {
            if (Session::get('payment_type') == 'cart_payment') {
                $version = config('visa.version');

                $acquirerID = config('visa.acquirerID');

                $merchantID = config('visa.merchantID');

                $responseURL = config('visa.responseURL');

                $captureFlag = config('visa.captureFlag');

                $password = config('visa.password');

                $signatureMethod = config('visa.signatureMethod');

                $orderID = Session::get('order_id');

                $order = Order::findOrFail($orderID);

                $purchaseAmt = number_format((float)convert_to_usd($order->grand_total), 2, '.', '');;
                $purchaseAmt = str_pad($purchaseAmt, 13, "0", STR_PAD_LEFT);

                $formattedPurchaseAmt = substr($purchaseAmt,0,10).substr($purchaseAmt,11);

                $currency = 840;

                $currencyExp = 2;

                $toEncrypt = $password.$merchantID.$acquirerID.$orderID.$formattedPurchaseAmt.$currency;

                $sha1Signature = sha1($toEncrypt);

                $base64Sha1Signature = base64_encode(pack("H*",$sha1Signature));

                return view('frontend.payment.visa', compact(['version', 'merchantID', 'acquirerID', 'responseURL', 'formattedPurchaseAmt', 'currency', 'currencyExp', 'orderID', 'captureFlag', 'base64Sha1Signature', 'signatureMethod']));
            }
        }
        abort(404);
    }

    public function getResult(Request $request) {
        //Parameters returned
            try {
                if ($request["ResponseCode"] != 1 || $request["ReasonCode"] != 1) {
                    flash($request["ReasonCodeDesc"])->error();
                    return redirect()->route('home');
                }
//                if(Session::has('payment_type')) {
//                    if (Session::get('payment_type') == 'cart_payment') {
                        $fields = ['MerID', 'AcqID', 'OrderID', 'ResponseCode', 'ReasonCode', 'ReasonDescr', 'Ref', 'PaddedCardNo', 'Signature', 'verifySignature', 'ResponseCode', 'ReasonCode'];
                        $MerID = $request["MerID"];
                        $AcqID = $request["AcqID"];
                        $OrderID = $request["OrderID"];
                        $ResponseCode = intval($request["ResponseCode"]);
                        $ReasonCode = intval($request["ReasonCode"]);
                        $ReasonDescr = $request["ReasonCodeDesc"];
                        $Ref = $request["ReferenceNo"];
                        $PaddedCardNo = $request["PaddedCardNo"];
                        $Signature = $request["Signature"];
                        //Authorization code is only returned in case of successful transaction,
                        // indicated with a value of 1
                        //for both response code and reason code
                        if ($ResponseCode == 1 && $ReasonCode == 1) {
                            $AuthNo = $request["AuthCode"];
                            array_push($fields, 'AuthNo');
                        }
                        //The parameters used for creating the signature as stored on the merchant server

                        $acquirerID = config('visa.acquirerID');
                        $merchantID = config('visa.merchantID');
                        $password = config('visa.password');

                        $order = Order::findOrFail($OrderID);
                        if ($order == null) {
                            flash('There is no order, please try agian!')->error();
                            return redirect()->route('home');
                        }

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
                        if ($verifySignature) {
                            try {
                                $checkoutController = new CheckoutController;
                                return $checkoutController->checkout_done($OrderID, null);
                            } catch (\Exception $e) {
                                flash($e->getMessage())->error();
//                            dd($e->getMessage());
                                return redirect()->route('home');
                            }
                        }else {
                            flash('Signature verification does not match')->error();
                            return redirect()->route('home');
                        }
//                    }
//                }
            } catch (\Exception $e) {
                flash($e->getMessage())->error();
//                            dd($e->getMessage());
                return redirect()->route('cart');
            }
        }

    }
