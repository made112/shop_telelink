<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CartCollection;
use App\Models\Cart;
use App\Models\Color;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index($id)
    {
        if (isset($id) && $id != null){
            return new CartCollection(Cart::where('user_id', $id)->latest()->get());
        }else {
            return response()->json([
                'message' => translate('Invalid user id'),
                'success' => false,
                'status' => 200
            ]);
        }

    }

    public function add(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'variant' => 'nullable|string',
            'color' => 'nullable|string'
        ], [
            'id.required' => 'ID must be required',
            'user_id.required' => 'User ID must be required',
            'variant.string' => 'Variant must be a string',
            'color.string' => 'Color must be a string',
        ]);

        if($validators->fails()) {
            return response()->json([
                'message' => $validators->errors()->first(),
                'success' => false,
                'status' => 401
            ], 401);
        }

        $product = Product::findOrFail($request->id);

        $variant = $request->variant;
        $color = $request->color;
        $tax = 0;

        if ($variant == '' && $color == '')
            $price = $product->unit_price;
        else {
            //$variations = json_decode($product->variations);
            $product_stock = $product->stocks->where('variant', $variant)->first();
            $price = $product_stock->price;
        }

        //discount calculation based on flash deal and regular discount
        //calculation of taxes
        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1  && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                if($flash_deal_product->discount_type == 'percent'){
                    $price -= ($price*$flash_deal_product->discount)/100;
                }
                elseif($flash_deal_product->discount_type == 'amount'){
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if($product->discount_type == 'percent'){
                $price -= ($price*$product->discount)/100;
            }
            elseif($product->discount_type == 'amount'){
                $price -= $product->discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $tax = ($price * $product->tax) / 100;
        }
        elseif ($product->tax_type == 'amount') {
            $tax = $product->tax;
        }

        Cart::updateOrCreate([
            'user_id' => $request->user_id,
            'product_id' => $request->id,
            'variation' => $variant
        ], [
            'price' => $price,
            'tax' => $tax,
            'shipping_cost' => $product->shipping_type == 'free' ? 0 : $product->shipping_cost,
            'quantity' => DB::raw('quantity + 1')
        ]);

        return response()->json([
            'message' => translate('Product added to cart successfully'),
            'success' => true,
            'status' => 200
        ]);
    }

    public function changeQuantity(Request $request)
    {
        if ($request->has('id') && $request->id != ''){
            $cart = Cart::findOrFail($request->id);
            $cart->update([
                'quantity' => $request->quantity
            ]);
            return response()->json(['message' => translate('Cart updated'), 'success' => true, 'status' => 200], 200);
        }
        return response()->json(['message' => translate('Invalid cart id'), 'success' => false, 'status' => 401], 401);
    }

    public function destroy($id)
    {
        if (isset($id) && $id != ''){
            Cart::destroy($id);
            return response()->json(['message' => translate('Product is successfully removed from your cart'), 'success' => true, 'status' => 200], 200);
        }
        return response()->json(['message' => translate('Invalid cart id'), 'success' => false, 'status' => 401], 401);
    }
}
