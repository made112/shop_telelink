<?php

namespace App\Http\Controllers;

use App\BusinessSetting;
use App\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cities = City::paginate(15);
        return view('cities.index', compact('cities'));
    }

    public function shipping_type(Request $request) {
        if ($request->has('jerusalem')) {
            $jerusalem = BusinessSetting::where('type', 'jerusalem')->first();
            $jerusalem->value = $request->jerusalem;
            $jerusalem->save();
        }
        if ($request->has('west_bank')) {
            $west_bank = BusinessSetting::where('type', 'west_bank')->first();
            $west_bank->value = $request->west_bank;
            $west_bank->save();
        }
        if ($request->has('occupied_interior')) {
            $occupied_interior = BusinessSetting::where('type', 'occupied_interior')->first();
            $occupied_interior->value = $request->occupied_interior;
            $occupied_interior->save();
        }
        flash('Cities Shipping type updated successfully')->success();
        return back();
    }

    public function free_shipping(Request $request) {
        if ($request->has('free_shipping')) {
            $f_shipping = BusinessSetting::where('type', 'free_shipping')->first();
            $f_shipping->value = $request->free_shipping;
            if ($f_shipping->save()) {
                flash('Free Shipping updated successfully')->success();
                return back();
            }
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('partials.city_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $city = new City;
        $city->name = $request->name;
        $city->nameAr = $request->nameAr;
        $city->type = $request->type;
        $city->status = '1';
        if($city->save()){
            flash('City updated successfully')->success();
            return redirect()->route('cities.index');
        }
        else {
            flash('Something went wrong')->error();
            return redirect()->route('cities.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $city = City::findOrFail($id);
        return view('partials.city_edit', compact('city'));
    }
    public function editCity(Request $request)
    {
        $city = City::findOrFail($request->id);
        return view('partials.city_edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $city = City::findOrFail($request->id);
        $city->name = $request->name;
        $city->nameAr = $request->nameAr;
        $city->type = $request->type;
        $city->status = $city->status;
        if($city->save()){
            flash('City updated successfully')->success();
            return redirect()->route('cities.index');
        }
        else {
            flash('Something went wrong')->error();
            return redirect()->route('cities.index');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        if(City::destroy($id)){
            flash(__('City has been deleted successfully'))->success();
            return redirect()->route('cities.index');
        }
        else{
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function updateStatus(Request $request){
        $city = City::findOrFail($request->id);
        $city->status = $request->status;
        if($city->save()){
            return 1;
        }
        return 0;
    }
}
