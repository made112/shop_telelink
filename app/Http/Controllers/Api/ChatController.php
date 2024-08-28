<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Resources\ConversationCollection;
use App\Http\Resources\MessageCollection;
use App\Mail\ConversationMailManager;
use App\Message;
use App\Models\Product;
use App\User;
use Illuminate\Http\Request;
use Mail;

class ChatController extends Controller
{

    public function conversations($id)
    {
        $conversations = Conversation::where('sender_id', $id)->latest('id')->get();
        return new ConversationCollection($conversations);
    }

    public function messages($id)
    {
        $messages = Message::where('conversation_id', $id)->latest('id')->get();
        return new MessageCollection($messages);
    }

    public function insert_message(Request $request)
    {
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = $request->user_id;
        $message->message = $request->message;
        $message->save();
        $conversation = $message->conversation;
        if ($conversation->sender_id == $request->user_id) {
            $conversation->receiver_viewed = "1";
        } elseif ($conversation->receiver_id == $request->user_id) {
            $conversation->sender_viewed = "1";
        }
        $conversation->save();
        $messages = Message::where('id', $message->id)->paginate(1);
        return new MessageCollection($messages);
    }

    public function get_new_messages($conversation_id, $last_message_id)
    {
        $messages = Message::where('conversation_id', $conversation_id)->where('id', '>', $last_message_id)->latest('id')->get();
        return new MessageCollection($messages);
    }

    public function create_conversation(Request $request)
    {

        try {
            $seller_user = Product::findOrFail($request->product_id)->user;
            $user = User::find($request->user_id);
            $conversation = Conversation::where('sender_id', $user->id)->where('product_id', $request->product_id)->first();
            if (isset($conversation)) {
                $messages = Message::where('conversation_id', $conversation->id)->latest('id')->get();
                return new MessageCollection($messages);
            }
            $conversation = new Conversation;
            $product = Product::findOrFail($request->product_id);
            $conversation->sender_id = $user->id;
            $conversation->product_id = $product->id;
            $conversation->receiver_id = $product->user->id;
            $conversation->title = $product->name;

            if ($conversation->save()) {
                $message = new Message;
                $message->conversation_id = $conversation->id;
                $message->user_id = $user->id;
                $message->message = $product->name;

                if ($message->save()) {
                    $this->send_message_to_seller($conversation, $message, $seller_user, $user);
                }
            }

            $messages = Message::where('conversation_id', $conversation->id)->latest('id')->get();
            return new MessageCollection($messages);

//            $generalsetting = GeneralSetting::first();
//
//            return response()->json(['result' => true, 'conversation_id' => $conversation->id,
//                'shop_name' => $conversation->receiver->user_type == 'admin' ? 'In House Product' : $conversation->receiver->shop->name,
//                'shop_logo' => $conversation->receiver->user_type == 'admin' ? my_asset($generalsetting->logo) : my_asset($conversation->receiver->shop->logo),
//                'title' => $conversation->title,
//                'message' => translate("Conversation created"),
//                'data' => []]);
        } catch (\Exception $exception) {
            dd($exception);
        }

    }

    public function unread_conversation(Request $request)
    {
        try {
            $count = Conversation::where('sender_id', $request->user_id)
                ->where('sender_viewed', 0)
                ->orWhere('receiver_id', $request->user_id)
                ->where('receiver_viewed', 0)
                ->get()->count();
            return response()->json([
                'unread_count' => $count,
                'status' => true,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function mark_as_read(Request $request)
    {
        try {
            $conversation = Conversation::findOrFail($request->conversation_id);
            if ($conversation->sender_id == $request->user_id) {
                $conversation->sender_viewed = 1;
            } else {
                $conversation->receiver_viewed = 1;
            }
            if ($conversation->save()) {
                return response()->json([
                    'status' => true,
                ]);
            }
            return response()->json([
                'status' => false
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function send_message_to_seller($conversation, $message, $seller_user, $user)
    {
        $array['view'] = 'emails.conversation';
        $array['subject'] = 'Sender:- ' . $user->name;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = 'Hi! You recieved a message from ' . $user->name . '.';
        $array['sender'] = $user->name;

        if ($seller_user->type == 'admin') {
            $array['link'] = route('conversations.admin_show', encrypt($conversation->id));
        } else {
            $array['link'] = route('conversations.show', encrypt($conversation->id));
        }

        $array['details'] = $message->message;

        try {
            Mail::to($conversation->receiver->email)->queue(new ConversationMailManager($array));
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }

    }
}
