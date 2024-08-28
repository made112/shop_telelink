<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Wallet;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClubPointController extends Controller
{
    public function userpoint_index()
    {
        $club_points = ClubPoint::where('user_id', Auth::user()->id)->latest()->paginate(15);
        return response()->json([
            'data' => $club_points,
            'success' => true,
            'status' => 200
        ]);
    }

    public function convert_point_into_wallet(Request $request)
    {
        if (($request->has('id') && $request->id != '') && ($request->has('user_id') && $request->user_id != '')) {
            $club_point_convert_rate = BusinessSetting::where('type', 'club_point_convert_rate')->first()->value;
            $club_point = ClubPoint::findOrFail($request->id);
            $wallet = new Wallet;
            $wallet->user_id = $request->id;
            $wallet->amount = floatval($club_point->points / $club_point_convert_rate);
            $wallet->payment_method = 'Club Point Convert';
            $wallet->payment_details = 'Club Point Convert';
            $wallet->save();
            $user = User::find($request->user_id);
            $user->balance = $user->balance + floatval($club_point->points / $club_point_convert_rate);
            $user->save();
            $club_point->convert_status = 1;
            if ($club_point->save()) {
                return response()->json([
                    'message' => 'Points converted into wallet successfully',
                    'success' => true,
                    'status' => 200
                ], 200);
            }
            else {
                return response()->json([
                    'message' => 'Club Point id does not found',
                    'success' => false,
                    'status' => 401
                ], 401);
            }
        }else {
            return response()->json([
                'message' => 'Club Point id does not found',
                'success' => false,
                'status' => 401
            ], 401);
        }
    }
}
