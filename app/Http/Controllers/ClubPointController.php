<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessSetting;
use App\ClubPointDetail;
use App\ClubPoint;
use App\Product;
use App\Wallet;
use App\Order;
use Auth;
use Illuminate\Support\Facades\Validator;

class ClubPointController extends Controller
{
    protected function validator(array $data, $type = 'user_point')
    {
        $validation_array = [
            "value"  => "required|numeric|min:1",
        ];
        $validation_messages = [];

        if ($type === 'products_point') {
            $validation_array = [
                'point' => 'required|numeric|min:1'
            ];
        }

        if ($type === 'set_products_point') {
            $validation_array = [
                'point' => 'required|numeric|min:1',
                'min_price' => 'required|numeric|min:0',
                'max_price' => 'required|numeric|min:1'
            ];
        }

        return Validator::make($data, $validation_array, $validation_messages);
    }
    public function configure_index()
    {
        return view('club_points.config');
    }

    public function index()
    {
        $club_points = ClubPoint::latest()->paginate(15);
        return view('club_points.index', compact('club_points'));
    }

    public function userpoint_index()
    {
        $club_points = ClubPoint::where('user_id', Auth::user()->id)->latest()->paginate(15);
        return view('club_points.frontend.index', compact('club_points'));
    }

    public function set_point()
    {
        $products = Product::latest()->paginate(15);
        return view('club_points.set_point', compact('products'));
    }

    public function set_products_point(Request $request)
    {
        $validations = $this->validator($request->all(), 'set_products_point');
        if($validations->fails()){
            $errors = '';
            foreach ($validations->errors()->all('') as $key=> $value) {
                $errors .= $value;
            }
            flash($errors)->error();
            return  back();
        }
        $products = Product::whereBetween('unit_price', [$request->min_price, $request->max_price])->get();
        foreach ($products as $product) {
            $product->earn_point = $request->point;
            $product->save();
        }
        flash(__('Point has been inserted successfully for ').count($products).__(' products'))->success();
        return redirect()->route('set_product_points');
    }

    public function set_point_edit($id)
    {
        $product = Product::findOrFail(decrypt($id));
        return view('club_points.product_point_edit', compact('product'));
    }

    public function update_product_point(Request $request, $id)
    {
        $validations = $this->validator($request->all(), 'products_point');
        if($validations->fails()){
            $errors = '';
            foreach ($validations->errors()->all('') as $key=> $value) {
                $errors .= $value;
            }
            flash($errors)->error();
            return  back();
        }
        $product = Product::findOrFail($id);
        $product->earn_point = $request->point;
        $product->save();
        flash(__('Point has been updated successfully'))->success();
        return redirect()->route('set_product_points');
    }

    public function convert_rate_store(Request $request)
    {
        $validations = $this->validator($request->all());
        if($validations->fails()){
            $errors = '';
            foreach ($validations->errors()->all('') as $key=> $value) {
                $errors .= $value;
            }
            flash($errors)->error();
            return  back();
        }
        $club_point_convert_rate = BusinessSetting::where('type', $request->type)->first();
        if ($club_point_convert_rate != null) {
            $club_point_convert_rate->value = $request->value;
        }
        else {
            $club_point_convert_rate = new BusinessSetting;
            $club_point_convert_rate->type = $request->type;
            $club_point_convert_rate->value = $request->value;
        }
        $club_point_convert_rate->save();
        flash(__('Point convert rate has been updated successfully'))->success();
        return redirect()->route('club_points.configs');
    }

    public function max_earn_point_user(Request $request)
    {
        $validations = $this->validator($request->all());
        if($validations->fails()){
            $errors = '';
            foreach ($validations->errors()->all('') as $key=> $value) {
                $errors .= $value;
            }
            flash($errors)->error();
            return  back();
        }
        $max_earn_point = BusinessSetting::where('type', $request->type)->first();
        if ($max_earn_point != null) {
            $max_earn_point->value = $request->value;
        }
        else {
            $max_earn_point = new BusinessSetting;
            $max_earn_point->type = $request->type;
            $max_earn_point->value = $request->value;
        }
        $max_earn_point->save();
        flash(__('Max Earning Point For User has been updated successfully'))->success();
        return redirect()->route('club_points.configs');
    }

    public function processClubPoints(Order $order)
    {
        $club_point = new ClubPoint;
        $club_point->user_id = Auth::user()->id;
        $club_point->points = 0;
        foreach ($order->orderDetails as $key => $orderDetail) {
            $total_pts = ($orderDetail->product->earn_point) * $orderDetail->quantity;
            $club_point->points += $total_pts;
        }
        $club_point->convert_status = 0;
        $club_point->save();
        foreach ($order->orderDetails as $key => $orderDetail) {
            $club_point_detail = new ClubPointDetail;
            $club_point_detail->club_point_id = $club_point->id;
            $club_point_detail->product_id = $orderDetail->product_id;
            $club_point_detail->point = $total_pts;
            $club_point_detail->save();
        }
    }

    public function club_point_detail($id)
    {
        $club_point_details = ClubPointDetail::where('club_point_id', decrypt($id))->paginate(12);
        return view('club_points.club_point_details', compact('club_point_details'));
    }

    public function convert_point_into_wallet(Request $request)
    {
        $club_point_convert_rate = BusinessSetting::where('type', 'club_point_convert_rate')->first()->value;
        $club_point = ClubPoint::findOrFail($request->el);
        $wallet = new Wallet;
        $wallet->user_id = Auth::user()->id;
        $wallet->amount = floatval($club_point->points / $club_point_convert_rate);
        $wallet->payment_method = 'Club Point Convert';
        $wallet->payment_details = 'Club Point Convert';
        $wallet->save();
        $user = Auth::user();
        $user->balance = $user->balance + floatval($club_point->points / $club_point_convert_rate);
        $user->save();
        $club_point->convert_status = 1;
        if ($club_point->save()) {
            return 1;
        }
        else {
            return 0;
        }
    }
}
