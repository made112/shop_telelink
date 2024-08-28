<?php

namespace App\Http\Resources;

use App\GeneralSetting;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ConversationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $generalsetting = GeneralSetting::first();
                return [
                    'id' => $data->id,
                    'receiver_id' => intval($data->receiver_id) ,
                    'sender_id' => intval($data->sender_id) ,
                    'receiver_type'=> $data->receiver->user_type,
                    'shop_id' => $data->receiver->user_type == 'admin' ? 0 : $data->receiver->shop->id,
                    'shop_name' => $data->receiver->user_type == 'admin' ? 'UNO' : $data->receiver->shop->name,
                    'shop_logo' => $data->receiver->user_type == 'admin' ? my_asset($generalsetting->logo)  : my_asset($data->receiver->shop->logo),
                    'title'=> $data->title,
                    'sender_viewed'=> intval($data->sender_viewed),
                    'receiver_viewed'=> intval($data->receiver_viewed),
                    'date'=> $data->updated_at,
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
