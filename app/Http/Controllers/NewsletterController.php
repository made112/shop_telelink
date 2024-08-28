<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Subscriber;
use Illuminate\Support\Facades\Validator;
use Mail;
use App\Mail\EmailManager;

class NewsletterController extends Controller
{
    protected function validator(array $data)
    {
        $validation_array = [
            "user_emails"  => "required|array|min:1",
            'subscriber_emails' => 'required|array|min:1',
            "subject"  => "required|string|max:255",
            'content' => 'required|string|max:2000',
        ];
        $validation_messages = [

        ];

        return Validator::make($data, $validation_array, $validation_messages);
    }

    public function index(Request $request)
    {
//    	$users = User::all();
//        $subscribers = Subscriber::all();
//    	return view('newsletters.index', compact('users', 'subscribers'));
        abort(404);
    }

    public function send(Request $request)
    {
        $this->validator($request->all())->validate();
        if (env('MAIL_USERNAME') != null) {
            //sends newsletter to selected users
        	if ($request->has('user_emails')) {
                foreach ($request->user_emails as $key => $email) {
                    $array['view'] = 'emails.newsletter';
                    $array['subject'] = $request->subject;
                    $array['from'] = env('MAIL_USERNAME');
                    $array['content'] = $request->content;

                    try {
                        Mail::to($email)->queue(new EmailManager($array));
                    } catch (\Exception $e) {
                        flash($e->getMessage())->error();
                        return back();
                    }
            	}
            }

            //sends newsletter to subscribers
            if ($request->has('subscriber_emails')) {
                foreach ($request->subscriber_emails as $key => $email) {
                    $array['view'] = 'emails.newsletter';
                    $array['subject'] = $request->subject;
                    $array['from'] = env('MAIL_USERNAME');
                    $array['content'] = $request->content;

                    try {
                        Mail::to($email)->queue(new EmailManager($array));
                    } catch (\Exception $e) {
                        flash($e->getMessage())->error();
                        return back();
                    }
            	}
            }
        }
        else {
            flash(__('Please configure SMTP first'))->error();
            return back();
        }

    	flash(__('Newsletter has been send'))->success();
    	return redirect()->route('admin.dashboard');
    }
}
