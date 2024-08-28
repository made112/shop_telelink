<?php

namespace App\Http\Controllers\Api;

use App\BusinessSetting;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'code' => 'required|numeric'
        ], [
            'user_id' => 'User ID must be required',
            'code.required' => 'Code Coupon must be required'
        ]);

        if($validators->fails()) {
            return response()->json([
                'message' => $validators->errors()->first(),
                'status' => 'error'
            ], 401);
        }
        $coupon = Coupon::where('code', $request->code)->first();
        $max_point = BusinessSetting::where('type', 'max_earn_point_user')->first()->value;

        if (ClubPoint::where('user_id', $request->user_id)->where('convert_status', 1)->sum('points') < $max_point) {
            if ($coupon != null && strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date && CouponUsage::where('user_id', $request->user_id)->where('coupon_id', $coupon->id)->first() == null) {
                if(in_array(Auth::user()->id, json_decode($coupon->details)[0]->users)) {
                    if ($coupon->usage > 0) {
                        if(CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null){
                            $couponDetails = json_decode($coupon->details);
                            if ($coupon->type == 'cart_base') {
                                $sum = Cart::where('uesr_id', $request->user_id)->sum('price');
                                if ($sum > $couponDetails->min_buy) {
                                    if ($coupon->discount_type == 'percent') {
                                        $couponDiscount = ($sum * $coupon->discount) / 100;
                                        if ($couponDiscount > $couponDetails->max_discount) {
                                            $couponDiscount = $couponDetails->max_discount;
                                        }
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $couponDiscount = $coupon->discount;
                                    }
                                    if ($this->isCouponAlreadyApplied($request->user_id, $coupon->id)) {
                                        return response()->json([
                                            'success' => false,
                                            'message' => 'The coupon is already applied. Please try another coupon'
                                        ]);
                                    } else {
                                        return response()->json([
                                            'success' => true,
                                            'discount' => (double)$couponDiscount
                                        ]);
                                    }
                                }
                            } elseif ($coupon->type == 'product_base') {
                                $couponDiscount = 0;
                                $cartItems = Cart::where('user_id', $request->user_id)->get();
                                foreach ($cartItems as $key => $cartItem) {
                                    foreach ($couponDetails as $key => $couponDetail) {
                                        if ($couponDetail->product_id == $cartItem->product_id) {
                                            if ($coupon->discount_type == 'percent') {
                                                $couponDiscount += $cartItem->price * $coupon->discount / 100;
                                            } elseif ($coupon->discount_type == 'amount') {
                                                $couponDiscount += $coupon->discount;
                                            }
                                        }
                                    }
                                }
                                if ($this->isCouponAlreadyApplied($request->user_id, $coupon->id)) {
                                    return response()->json([
                                        'success' => false,
                                        'message' => 'The coupon is already applied. Please try another coupon'
                                    ]);
                                } else {
                                    return response()->json([
                                        'success' => true,
                                        'discount' => (double)$couponDiscount,
                                        'message' => 'Coupon code applied successfully'
                                    ]);
                                }
                            }
                        } else{
                            return response()->json([
                                'status' => false,
                                'message' => 'You already used this coupon!'
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Coupon usage expired!'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'You don\'t have coupon!'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The coupon is invalid'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Coupon is disabled, because you have exceeded the maximum points limit ').$max_point"
            ]);
        }
    }

    protected function isCouponAlreadyApplied($userId, $couponId) {
        return CouponUsage::where(['user_id' => $userId, 'coupon_id' => $couponId])->count() > 0;
    }
}
