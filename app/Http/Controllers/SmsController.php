<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Nexmo;
use Twilio\Rest\Client;
use App\OtpConfiguration;
use App\User;

class SmsController extends Controller
{
    protected function validator(array $data)
    {
        $validation_array = [
            "user_phones"  => "required|array|min:1",
            "subject"  => "required|string|max:255",
            'content' => 'required|string|max:2000',
        ];
        $validation_messages = [

        ];

        return Validator::make($data, $validation_array, $validation_messages);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$users = User::all();
        return view('otp_systems.sms.index',compact('users'));
    }

    //send message to multiple users
    public function send(Request $request)
    {
        $this->validator($request->all())->validate();
        foreach ($request->user_phones as $key => $phone) {
            sendSMS($phone, config('app.name'), $request->content);
        }

    	flash(__('SMS has been sent.'))->success();
    	return redirect()->route('admin.dashboard');
    }
}
