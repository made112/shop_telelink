<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Models\Order;
use App\Product;
use App\Seller;
use App\SubCategory;
use App\Unit;
use Illuminate\Foundation\Auth\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Cache;
use Auth;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

class BisanController extends Controller
{
    private $token = null;
    private $items = null;
    private $itemCategory = null;
    private $classifiction = null;
    private $prices = null;
    private $brand = null;
    private $units = null;
    private $currencies = null;
    private $current_currency = '01';
    private $client;


    public function __construct()
    {
        $currency = \App\Currency::findOrFail(\App\BusinessSetting::where('type', 'system_default_currency')->first()->value);
        if ($currency->bsn_code) {
            $this->current_currency = $currency->bsn_code;
        }
        $this->client = new Client();
        if(Cache::has('bsn_token')) {
            $this->token = Cache::get('bsn_token');
        }else {
           $this->loginWithBisan();
        }
    }

    public function getToken() {
        return $this->token;
    }

    public function refreshToken() {
        $this->loginWithBisan();
        return $this->token;
    }

    public function storeWithBisan($order) {
        /**
         **  Check If Product Added By Admin
         **/
        $customer = Auth::user();
        if ($customer->bsn_code === null) {
            $this->createCustomer();
        }
        foreach ($order->orderDetails as $key => $item) {
            if ($item->product->added_by === 'admin') {
                if($item->product->bsn_code === null) {
                    $this->createProduct($item->product);
                }
            }
        }
        $this->createOrder($order->id);

    }

    private function loginWithBisan() {
        $user = config('bisan.user');
        $password = config('bisan.password');
        $url = config('bisan.BISAN_URL');
        $apiID = config('bisan.BSN-apiID');
        $secretID = config('bisan.BSN-apiSecret');
        $body = [
             'user' => $user,
             'password' => $password
        ];
        try {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url. '/login',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_CIPHER_LIST=> 'DEFAULT@SECLEVEL=1',
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_HTTPHEADER => array(
                    'BSN-apiSecret: '.$secretID,
                    'BSN-apiID: '.$apiID
                ),
            ));
            $response = curl_exec($ch);
            $err = curl_error($ch);
            if ($err) {
                dd($err);
            }
            curl_close($ch);
            if (json_decode($response) != null) {
                $this->token = json_decode($response)->token;
                $expireAt = Carbon::now()->addHour(1);
                Cache::put('bsn_token', $this->token, $expireAt);
            } else {
                $this->loginWithBisan();
            }
        } catch (\Exception $err) {
            dd($err);
            flash("Something Error")->error();
           die($err);
        }
    }

    public function fetchProducts() {
        $url = config('bisan.BISAN_URL');
        $fields = "code,name,nameAR,nameEN,isAssetItem,enabled,headerFld,treeParent,attachment,refundPercent,type,vendor,unitType,defaultUnit,smallestUnit,itemFamily,defaultPrice,smallestUnit,specifications,specificationsAR,specificationsEN,commission,classification,itemCategory,brand,reOrder,isTaxable,itemTaxType,itemCostDetail.date,itemCostDetail.costType,itemCostDetail.currency,itemCostDetail.costPrice,itemCostDetail.unit,bins.warehouse,bins.binNum,bins.unit,bins.alarmQnt,bins.minQnt,bins.maxQnt,group,reportUnit,unitContent,reOrder,assetFamily,assetTagPrefix,maxDiscPerc,defaultPrice,unitList.unit,unitList.partNumber,unitList.packWeight,unitList.packVolume,depreciationRateDetail.date,depreciationRateDetail.rate,discontinued,partNumber,itemSTDCostDetail.date,itemSTDCostDetail.unit,itemSTDCostDetail.currency,itemSTDCostDetail.costPrice,serial,batch";
        try {
            $this->loginWithBisan();
            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url.'/item?fields='.$fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                'BSN-token: '.$this->token
            ));
            $res = curl_exec($ch);
            curl_close($ch);
            $this->items = json_decode($res, true);
            return response()->json($this->items['rows']);
        } catch (\Exception $err) {
            if($err->getCode() === 401) {
                $this->refreshToken();
                $this->fetchProducts();
            }elseif ($err->getCode() === 500) {
                flash("Server Error")->error();
//                return back();
            }
            else {
                flash("Something Error")->error();
//                return back();
            }
        }
    }

    public function fetchPrice($code = null) {
        $url = config('bisan.BISAN_URL');
        $fields = "priceList,item,unit,currency,rawPrice,taxedPrice,maxDiscPerc,maxMarkupPerc";
        $search = '';
        if ($code !== null){
            $search = '&search=item:'.$code;
        }
        $this->loginWithBisan();
        try {
            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url.'/itemPrice?fields='.$fields.$search);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                'BSN-token: '. $this->token
            ));
            $res = curl_exec($ch);
            curl_close($ch);
            $this->prices = json_decode($res, true);
            return response()->json($this->prices['rows']);
        } catch (\Exception $err) {
            if($err->getCode() === 401) {
                $this->refreshToken();
                $this->fetchPrice($code);
                return ;
            }
            elseif ($err->getCode() === 500) {
                flash("Server Error")->error();
//                return back();
            }
            else {
                flash("Something Error")->error();
//                return back();
            }
        }
    }

    public function fetchItemCategories() {
        $url = config('bisan.BISAN_URL');
        try {
            $this->loginWithBisan();
            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url.'/itemCategory?fields=code,nameAR,nameEN,name,headerFld,treeParent,attachment');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                'BSN-token: '. $this->token
            ));
            $res = curl_exec($ch);
            curl_close($ch);
            $this->itemCategory = json_decode($res, true);
            return response()->json($this->itemCategory['rows']);
        } catch (\Exception $err) {
            if($err->getCode() === 401) {
                $this->refreshToken();
                $this->createCustomer();
            }elseif ($err->getCode() === 500) {
                flash("Server Error")->error();
//                return back();
            }
            else {
                flash('Something Error')->error();
//                return back();
            }
        }
    }

    public function fetchClassification() {
        $url = config('bisan.BISAN_URL');
        try {
            $this->loginWithBisan();
            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url.'/itemClassification?fields=code,nameAR,nameEN,name,headerFld,treeParent,attachment');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                'BSN-token: '. $this->token
            ));
            $res = curl_exec($ch);
            curl_close($ch);
            $this->classifiction = json_decode($res, true);
            return response()->json($this->classifiction['rows']);
        } catch (\Exception $err) {
            if($err->getCode() === 401) {
                $this->refreshToken();
                $this->createCustomer();
            }elseif ($err->getCode() === 500) {
                flash("Server Error")->error();
//                return back();
            }
            else {
                flash('Something Error')->error();
//                return back();
            }
        }
    }

    public function fetchBrand() {
        $url = config('bisan.BISAN_URL');
        try {
            $this->loginWithBisan();
            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url.'/itemBrand?fields=code,nameAR,nameEN,name,headerFld,treeParent,attachment');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                'BSN-token: '. $this->token
            ));
            $res = curl_exec($ch);
            curl_close($ch);
            $this->brand = json_decode($res, true);
            return response()->json($this->brand['rows']);
        } catch (\Exception $err) {
            if($err->getCode() === 401) {
                $this->refreshToken();
                $this->createCustomer();
            }elseif ($err->getCode() === 500) {
                flash("Server Error")->error();
//                return back();
            }
            else {
                flash('Something Error')->error();
//                return back();
            }
        }
    }

    public function fetchCurrency() {
        $url = config('bisan.BISAN_URL');
        try {
            $this->loginWithBisan();
            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url.'/currency?fields=code,name,nameAR,nameEN,stdCode,symbolAR,symbolEN,symbol,multiply');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                'BSN-token: '. $this->token
            ));
            $res = curl_exec($ch);
            curl_close($ch);
            $this->currencies = json_decode($res, true);
            return response()->json($this->currencies['rows']);
        } catch (\Exception $err) {
            if($err->getCode() === 401) {
                $this->refreshToken();
                $this->createCustomer();
            }elseif ($err->getCode() === 500) {
                flash("Server Error")->error();
//                return back();
            }
            else {
                flash('Something Error')->error();
//                return back();
            }
        }
    }

    public function fetchUnit() {
        $url = config('bisan.BISAN_URL');
        try {
            $this->loginWithBisan();
            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url.'/unit?fields=name,code,nameAR,enabled,unitType,symbolAR,symbolEN,symbol,factor');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                'BSN-token: '. $this->token
            ));
            $res = curl_exec($ch);
            curl_close($ch);
            $this->units = json_decode($res, true);
            return response()->json($this->units['rows']);
        } catch (\Exception $err) {
            die("aaa");
            if($err->getCode() === 401) {
                $this->refreshToken();
                $this->createCustomer();
            }elseif ($err->getCode() === 500) {
                flash("Server Error")->error();
//                return back();
            }
            else {
                flash('Something Error')->error();
//                return back();
            }
        }
    }

    public function getAllQuantities() {
        $url = config('bisan.BISAN_URL');
        try {
            $this->loginWithBisan();
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url.'/REPORT/stockBalance?fields=item,endBalance&search=lg_status:SAVED+POSTED,warehouse_From:1,warehouse_To:1',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'BSN-token: '.$this->getToken()
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            return response()->json(json_decode($response)->rows);
        }catch (\Exception $err) {

        }
    }

    public function createCustomer() {
        if (Auth::check()) {
            try {
                $customer = Auth::user();
                if ($customer->bsn_code === null) {
                    $body = array();
                    $body['TRANSACTION_ID'] = strval(Carbon::now()->timestamp. rand(1,8));
                    $body['record'] = array();
                    $body['record']['name'] = $customer->name;
                    $body['record']['nameAR'] = $customer->name;
                    $body['record']['nameEN'] = $customer->name;
                    $body['record']['enabled'] = 'نعم';
                    if (strlen($customer->phone) >= 7) {
                        $body['record']['phone'] = $customer->phone ? $customer->phone : "";
                    }
                    $body['record']['email'] = $customer->email ? $customer->email : "";

                    $body['record']['streetAddress'] = $customer->address ? $customer->address : "";
                    $body['record']['gender'] = "";
                    $body['record']['birthDate'] = "";
                    $body['record']['isCustomer'] = 'نعم';
                    $body['record']['cusPriceList'] = config('bisan.Default_Sales_Price');
                    $body['record']['cusAccount'] = config('bisan.CODE_ACCOUNT');
                    $url = config('bisan.BISAN_URL');

                    $this->loginWithBisan();
                    $ch = curl_init(); // Initialize cURL
                    curl_setopt($ch, CURLOPT_URL, $url.'/contact');
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                        'BSN-token: '. $this->token
                    ));
                    $res = curl_exec($ch);
                    curl_close($ch);

                    if($res != null) {
                        $code = json_decode($res)->rows->code;
                        $user = User::find($customer->id);
                        $user->bsn_code = $code;
                        $user->save();
                        return $code;
                    }
                }
            }
            catch (\Exception $err) {
                if($err->getCode() === 401) {
                    $this->refreshToken();
                    $this->createCustomer();
                }elseif ($err->getCode() === 500) {
                    flash("Server Error")->error();
//                    return back();
                }
                else {
                    flash("Something Error")->error();
//                    return back();
                }
            }
        }
    }

    public function createProduct($resource) {
        try {
            if ($resource->bsn_code === null) {
                $unit = Unit::where('code', $resource->unit)->first();
                $default_unit = 'PCS';
                if($unit !== null){
                    $default_unit = $unit->code;
                }
                $body = array();
                $body['TRANSACTION_ID'] = strval(Carbon::now()->timestamp. rand(1,10));
                $body['record'] = array();
                $body['record']['name'] = Str::limit($resource->getTranslation('name', 'en'), 20);
                $body['record']['nameAR'] = Str::limit($resource->getTranslation('name', 'en'),20);
                $body['record']['nameEN'] = Str::limit($resource->getTranslation('name', 'en'), 20);
                $body['record']['enabled'] = "نعم";
                $body['record']['type'] = "صنف مخزون";
                $body['record']['unitType'] = "كمية";
                $body['record']['smallestUnit'] = $default_unit;
                $body['record']["unitList"] = [
                    (object) [
                        "unit" => $default_unit,
                        "partNumber" => Str::limit('I-Buy-'.strval(Carbon::now()->timestamp. rand(1,10)), 20)
                    ]
                ];
                $body['record']['itemCostDetail'] = array(
                    (object) [
                        "date" => strval(Carbon::now()->format('d/m/Y')),
                        "costType" => 'ثابت',
                        "costPrice" => strval($resource->unit_price),
                        "unit" => $default_unit
                    ]
                );

                $quantity = 0;
                if ($resource->stocks != null) {
                    foreach ($resource->stocks as $key => $stock) {
                        $quantity += intval($stock->qty);
                    }
                }else {
                    $quantity = strval($resource->current_stock);
                }

                $body['record']['bins'] = array(
                    (object) [
                        "warehouse" => "0001",
                        "unit" => $default_unit,
                        "minQnt" => "1",
                        "maxQnt" => $quantity
                    ]
                );
                $body['record']['commission'] = strval($resource->tax);
                if($resource->subcategory->code !== null) {
                    $body['record']['itemCategory'] = $resource->subcategory->code;
                }
                $body['record']['classification'] = "LST";
//                $body['   record']['brand'] = "WM";
//                $body['record']['itemSTDCostDetail'] = array(
//                    (object) [
//                        "costPrice" => strval($resource->unit_price),
//                        "currency" => strval($this->current_currency)
//                    ]
//                );
                $url = config('bisan.BISAN_URL');
                $this->loginWithBisan();
                $ch = curl_init(); // Initialize cURL
                curl_setopt($ch, CURLOPT_URL, $url.'/item');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                    'BSN-token: '. $this->token
                ));
                $res = curl_exec($ch);
                curl_close($ch);
                if($res != null) {
                    $code = json_decode($res)->rows->code;
                    $product = Product::find($resource->id);
                    $product->bsn_code = $code;
                    $product->save();
                    return $code;
                }
            }

        }catch (\Exception $err) {

            if($err->getCode() === 401) {
                $this->refreshToken();
                $this->createProduct($resource);
            }
            elseif ($err->getCode() === 400) {
                flash("Something Error on create product")->error();
//                return back();
            }
            elseif ($err->getCode() === 500) {
                flash("Server Error")->error();
//                return back();
            } else {
                flash("Something Error on create product")->error();
//                return back();
            }
        }
    }

    public function createOrder($resource) {
        if (Auth::check()) {
            try {
                $customer = Auth::user();
                if ($customer->bsn_code === null) {
                   $customer->bsn_code = $this->createCustomer();
                }
                $body = array();
                $body['TRANSACTION_ID'] = strval(Carbon::now()->timestamp. intval(Str::uuid()->toString()));
                $body['record'] = array();
                $body['record']['contact'] = strval($customer->bsn_code);

                $order = Order::find($resource);
                $flag = false;
                $body['record']["orderDetail"] = array();
                foreach ($order->orderDetails as $orderDetail) {
                    if ($orderDetail->product->added_by === 'admin') {
                        if ($orderDetail->product->bsn_code === null) {
                            $orderDetail->product->bsn_code = $this->createProduct($orderDetail->product);
                        }
                        $this->createPrice($orderDetail->product->bsn_code);
                        $flag = true;
                        $discount = "0";
                        if ($orderDetail->product->discount != null && $orderDetail->product->discount_type != null) {
                            if ($orderDetail->product->discount_type == 'percent') {
                                $discount = intval($orderDetail->product->discount);
                            }elseif ($orderDetail->product->discount_type == 'amount') {
                                $discount = (intval($orderDetail->product->unit_price) - (100 - intval($orderDetail->product->discount)))/ 100;
                            }else {
                                $discount= "0";
                            }
                        }
                        array_push($body['record']["orderDetail"], (object) [
                            "item" => strval($orderDetail->product->bsn_code),
                            "quantity" => strval($orderDetail->quantity),
                            "discountPercent"=> $discount
                        ]);
                    }
                }
                if ($flag) {
                    $url = config('bisan.BISAN_URL');
                    $this->loginWithBisan();
                    $ch = curl_init(); // Initialize cURL
                    curl_setopt($ch, CURLOPT_URL, $url.'/salesOrder');
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                        'BSN-token: '. $this->token
                    ));
                    $res = curl_exec($ch);
                    curl_close($ch);
                    if($res) {
                        $code = json_decode($res)->rows->code;
                        return $code;
                    }
                }

            } catch (\Exception $err) {
                if($err->getCode() === 401) {
                    $this->refreshToken();
                    $this->createOrder($resource);
                }
                elseif ($err->getCode() === 400) {
                    flash("Something Error on create order")->error();
//                    return back();
                }
                elseif ($err->getCode() === 500) {
                    flash("Server Error")->error();
//                    return back();
                } else {
                    flash("Something Error on create order")->error();
//                    return back();
                }
            }
        }
    }

    public function createPrice($code) {
        $this->loginWithBisan();
        try {
            $resource = Product::where('bsn_code', $code)->first();
            if ($resource !== null) {
                $unit = Unit::where('code', $resource->unit)->first();
                $default_unit = 'PCS';
                if($unit !== null){
                    $default_unit = $unit->code;
                }
                $url = config('bisan.BISAN_URL');

                $headers = [
                    'BSN-token' => $this->getToken()
                ];
                $type_prices = array();
                $price_item = $this->fetchPrice($code);
                if($price_item !== null) {
                    foreach ($price_item->getData() as $key => $p) {
                        array_push($type_prices, $p->priceList);
                    }
                }
                if (!in_array( 'P', $type_prices) and $resource->purchase_price > 0) {
                    $body = array();
                    $body['TRANSACTION_ID'] = strval(Carbon::now()->timestamp + intval(Str::uuid()->toString()));
                    $body['record'] = (object) [
                        "priceList" => "P",
                        "item" => $resource->bsn_code,
                        "unit" => $default_unit,
                        "rawPrice" => strval($resource->purchase_price),
                    ];
                    $this->loginWithBisan();
                    $ch = curl_init(); // Initialize cURL
                    curl_setopt($ch, CURLOPT_URL, $url.'/itemPrice');
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                        'BSN-token: '. $this->token
                    ));
                    curl_exec($ch);
                    curl_close($ch);
                }
                if (!in_array('S', $type_prices) and $resource->unit_price > 0) {
                    $body2 = array();
                    $body2['TRANSACTION_ID'] = strval(Carbon::now()->timestamp . rand(1,20));
                    $body2['record'] = (object) [
                        "priceList" => "S",
                        "item" => $resource->bsn_code,
                        "unit" => $default_unit,
                        "rawPrice" => strval($resource->unit_price),
                    ];
                    $this->loginWithBisan();
                    $ch = curl_init(); // Initialize cURL
                    curl_setopt($ch, CURLOPT_URL, $url.'/itemPrice');
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body2));
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_HTTPHEADER,    array(
                        'BSN-token: '. $this->token
                    ));
                    curl_exec($ch);
                    curl_close($ch);
                }
            }

        }catch (\Exception $err) {
            if($err->getCode() === 401) {
                $this->refreshToken();
                $this->createPrice($code);
            }
            elseif ($err->getCode() === 400) {
                flash("Something Error on create price1")->error();
//                return back();
            }
            elseif ($err->getCode() === 500) {
                flash("Server Error")->error();
//                return back();
            } else {
                flash("Something Error on create price")->error();
//                return back();
            }
        }
    }

    public function export_products() {
        $products = Product::with('orderDetails')->where('added_by', 'seller')->orderBy('created_at', 'desc')->get();
        return view('bisan.export_products', compact('products'));
    }

    public function export_orders() {
        $sellers = Seller::all();
        return view('bisan.export_orders', compact('sellers'));
    }

}
