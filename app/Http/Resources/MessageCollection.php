<?php

namespace App\Http\Resources;

use App\GeneralSetting;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MessageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $generalsetting = GeneralSetting::first();
        return [
            'data' => $this->collection->map(function ($data) use ($generalsetting) {
                return [
                    'id' => $data->id,
                    'user_id' => intval($data->user_id),
                    'conversation_id' => intval($data->conversation_id),
                    'message' => $data->message,
                    'shop_logo' => $data->conversation->receiver->user_type == 'admin' ? my_asset($generalsetting->logo)  : my_asset($data->conversation->receiver->shop->logo),
                    'date' => Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('F d,Y'),
                    'time' => Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('h:i a'),
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
