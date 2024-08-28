<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seller;
use App\User;
use App\Shop;
use App\Product;
use App\Order;
use App\OrderDetail;
use App\Http\Controllers\OTPVerificationController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SellerController extends Controller
{
    protected function validator(array $data, $type = 'add', $seller = null)
    {
        $validation_array = [
            "name" => "required|string|max:100",
            'password' => 'required|string|min:6',
            'email' => "nullable|email|unique:users,email",
            'phone' => "required|regex:/^\d{9,10}$/"
        ];
         $validation_messages = [
            "name.required" => "Name field must be required",
            "password.required" => "Password field must be required",
            "email.required" => "Email field must be required",
            "name.string" => "Name field must be string",
            "name.max" => "Name field must a 100 character ",
            "email.unique" => "This email is already exist",
            "email.email" => "This email is usable"
        ];
         if ($type === 'update') {
             $validation_array['password'] = 'nullable|string|min:6';
             $validation_array['phone'] = 'nullable|regex:/^\d{9,10}$|string|min:9|max:9/';
             $validation_array['email'] = [
                 'nullable',
                 'email',
                 Rule::unique('users', 'email')->ignore($seller->user->id),
             ];
         }
        return Validator::make($data, $validation_array, $validation_messages);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $approved = null;
        $sellers = Seller::orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'seller')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')->orWhere('email', 'like', '%' . $sort_search . '%');
            })->pluck('id')->toArray();
            $sellers = $sellers->where(function ($seller) use ($user_ids) {
                $seller->whereIn('user_id', $user_ids);
            });
        }
        if ($request->approved_status != null) {
            $approved = $request->approved_status;
            $sellers = $sellers->where('verification_status', $approved);
        }
        $sellers = $sellers->paginate(8);
        return view('sellers.index', compact('sellers', 'sort_search', 'approved'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sellers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();
        if ($request->has('email') && $request->email != null) {
            if (User::where('email', $request->email)->first() != null) {
                flash(__('Email already exists!'))->error();
                return back();
            }
        }

        if (User::where('phone', '+'.$request->country_code.$request->phone)->first() != null) {
            flash('Phone already exists.');
            return back();
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = '+'.$request->country_code.$request->phone;
        $user->user_type = "seller";
        $user->verification_code = rand(100000, 999999);

        $user->password = Hash::make($request->password);
        $user->show_password = $request->password;
        if ($user->save()) {
            $otpController = new OTPVerificationController;
            $otpController->send_code($user);

            $seller = new Seller;
            $seller->user_id = $user->id;
            if ($seller->save()) {
                $shop = new Shop;
                $shop->user_id = $user->id;
                $shop->slug = 'demo-shop-' . $user->id;
                $shop->save();
                flash(__('Seller has been inserted successfully'))->success();
                return redirect()->route('sellers.index');
            }
        }

        flash(__('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $seller = Seller::findOrFail(decrypt($id));
        return view('sellers.edit', compact('seller'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $seller = Seller::findOrFail($id);
        $this->validator($request->all(),'update', $seller)->validate();
        $user = $seller->user;
        $user->name = $request->name;
//        if ($request->has('email') && $request->email != null) {
//            if (User::where('email', $request->email)->first() != null) {
//                flash(__('Email already exists!'))->error();
//                return back();
//            }
//        }


        $change_phone_flag = false;
        if ($request->has('phone') && $request->phone != null && $user->phone != '+'.$request->country_code.$request->phone) {
            if (User::where('phone', '+'.$request->country_code.$request->phone)->where('id', '!=', $seller->user->id)->first() != null) {
                flash('Phone already exists.');
                return back();
            } else {
                $user->phone = '+'.$request->country_code.$request->phone;
                $user->verification_code = rand(100000, 999999);
                $user->email_verified_at = null;
                $change_phone_flag = true;
            }
        }
        $user->email = $request->email;
        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
            $user->show_password = $request->password;
        }
        if ($user->save()) {
            if ($change_phone_flag) {
                $otpController = new OTPVerificationController;
                $otpController->send_code($user);
            }
            if ($seller->save()) {
                flash(__('Seller has been updated successfully'))->success();
                return redirect()->route('sellers.index');
            }
        }

        flash(__('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $seller = Seller::findOrFail($id);
        Shop::where('user_id', $seller->user->id)->delete();
        Product::where('user_id', $seller->user->id)->delete();
        Order::where('user_id', $seller->user->id)->delete();
        OrderDetail::where('seller_id', $seller->user->id)->delete();
        User::destroy($seller->user->id);
        if (Seller::destroy($id) == 0) {
            flash(__('Seller has been deleted successfully'))->success();
            return redirect()->route('sellers.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function show_verification_request($id)
    {
        $seller = Seller::findOrFail($id);
        return view('sellers.verification', compact('seller'));
    }

    public function approve_seller($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->verification_status = 1;
        if ($seller->save()) {
            flash(__('Seller has been approved successfully'))->success();
            return redirect()->route('sellers.index');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    public function reject_seller($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->verification_status = 0;
        $seller->verification_info = null;
        if ($seller->save()) {
            flash(__('Seller verification request has been rejected successfully'))->success();
            return redirect()->route('sellers.index');
        }
        flash(__('Something went wrong'))->error();
        return back();
    }


    public function payment_modal(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        return view('sellers.payment_modal', compact('seller'));
    }

    public function profile_modal(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        return view('sellers.profile_modal', compact('seller'));
    }

    public function updateApproved(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        $seller->verification_status = $request->status;
        if ($seller->save()) {
            return 1;
        }
        return 0;
    }

    public function login($id)
    {
        $seller = Seller::findOrFail(decrypt($id));

        $user = $seller->user;

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }
}
