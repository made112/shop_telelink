<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserCollection;
use App\Models\Order;
use App\Models\OrderDetail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function info($id)
    {
        return new UserCollection(User::where('id', $id)->get());
    }

    public function updateName(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'user_id' => 'required',

        ]);
        if ($validators->fails()) {
            return response()->json([
                'message' => $validators->errors()->first(),
                'status' => false
            ], 404);
        }
        $user = User::findOrFail($request->user_id);
        if ($request->has('name')) {
            $user->update([
                'name' => $request->name
            ]);
        }
        if ($request->hasFile('photo')) {
            $user->avatar_original = $request->photo->store('uploads');
            $user->save();
        }
        return response()->json([
            'message' => 'Profile information has been updated successfully',
            'user' => $user,
            'status' => true
        ], 200);
    }

    public function updateShippingAddress(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'phone' => 'required|string',
            'city' => 'required|string',
            'phone' => 'nullable|string',
            'postal_code' => 'nullable|digits:5'
        ]);
        if ($validators->fails()) {
            return response()->json([
                'message' => $validators->errors()->first(),
                'status' => false
            ], 404);
        }
        $user = User::findOrFail($request->user_id);
        $user->update([
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'phone' => $request->phone,
            'postal_code' => $request->postal_code
        ]);
        return response()->json([
            'message' => 'Shipping information has been updated successfully',
            'status' => true
        ], 200);
    }

    public function getMyOrders(Request $request)
    {
        $user = User::findOrFail($request->customer);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'We can not find a user with that e-mail address'], 404);
        }
        $orders = Order::where('user_id', $user->id)->latest()->get();
        if ($orders && count($orders) > 0) {
            foreach ($orders as $key => $order) {
                $name_arr = explode(' ', json_decode($order->shipping_address)->name, '2');
                $fname = $name_arr[0];
                $lname = '';
                if (isset($name_arr[1])) {
                    $lname = $name_arr[1];
                }
                $orderDetails = OrderDetail::where('order_id', $order->id)->get();
                $delivery_status = 'pending';
                if (isset($orderDetails) && count($orderDetails) > 0) {
                    $delivery_status = $orderDetails->first()->delivery_status;
                    if ($delivery_status == 'pending') {
                        $delivery_status = 'pendding';
                    } elseif ($delivery_status == 'on_review') {
                        $delivery_status = 'on-hold';
                    } elseif ($delivery_status == 'on_delivery') {
                        $delivery_status = 'processing';
                    } elseif ($delivery_status == 'delivered') {
                        $delivery_status = 'completed';
                    } elseif ($delivery_status == 'cancelled') {
                        $delivery_status = 'cancelled';
                    } else {
                        $delivery_status = 'pendding';
                    }
                }
                $order->shipping_address = (object)[
                    'first_name' => $fname,
                    'last_name' => $lname,
                    'address_1' => json_decode($order->shipping_address)->address,
                    'city' => json_decode($order->shipping_address)->city,
                    'state' => '',
                    'country' => json_decode($order->shipping_address)->country,
                    'email' => json_decode($order->shipping_address)->email,
                    'phone' => json_decode($order->shipping_address)->phone,
                    'postcode' => json_decode($order->shipping_address)->postal_code,
                    'mapUrl' => null
                ];
                $order->delivery_status = $delivery_status;
                $order->billing = $order->shipping_address;
                $order->payment_method_title = ucwords(str_replace('_', ' ', $order->payment_type));
            }
        }

        return response()->json([
            'data' => $orders,
            'message' => true
        ], 200);
    }

    public function updateDocumentId(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'user_id' => 'required',
            'document_id' => 'required|string',
        ]);
        if ($validators->fails()) {
            return response()->json([
                'message' => $validators->errors()->first(),
                'status' => false
            ], 404);
        }
        $user = User::findOrFail($request->user_id);
//        $user->update([
//            'document_id' => $request->document_id
//        ]);
        $user->document_id = $request->document_id;
        $user->save();
        return response()->json([
            'message' => 'Document Firebase User  has been updated successfully',
            'user' => $user,
            'status' => true
        ], 200);
    }
}
