<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Policy;
use Auth;

class PolicyController extends Controller
{
    public function index($type)
    {
        if (Auth::check() && Auth::user()->user_type == 'staff') {
            if (! in_array(9, json_decode(Auth::user()->staff->role->permissions))) {
                abort(401);
            }
        }
        $policy = Policy::where('name', $type)->first();
        return view('policies.index', compact('policy'));
    }

    //updates the policy pages
    public function store(Request $request){
        if (Auth::check() && Auth::user()->user_type == 'staff') {
            if (! in_array(9, json_decode(Auth::user()->staff->role->permissions))) {
                abort(401);
            }
        }
        $policy = Policy::where('name', $request->name)->first();
        $policy->name = $request->name;
        $policy->content = $request->content;
        $policy->save();

        flash($request->name.' updated successfully');
        return back();
    }
}
