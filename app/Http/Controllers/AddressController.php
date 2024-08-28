<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Address;
use Auth;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'address' => 'required|string|max:255',
            'city' => 'required|numeric|max:20',
            'phone' => 'required|string|min:6|max:20',
            'country_code' => 'required|string|max:3|min:3',
            'postal_code' => 'nullable',
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $validations = $this->validator($request->all());
            if($validations->fails()){
                $errors = '';
                foreach ($validations->errors()->all('') as $key=> $value) {
                    $errors .= $value;
                }
                flash($errors)->error();
                return  back();
            }

            $address = new Address;
            $address->user_id = Auth::user()->id;
            $address->address = $request->address;
            $address->country = $request->country;
            $address->city = $request->city;
            $address->postal_code = $request->postal_code;
            $address->phone = '+'.$request->country_code.$request->phone;
            $address->save();
            flash(__('Address is added successfully'))->success();

            return back();
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
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        if(!$address->set_default){
            $address->delete();
            flash(__('Address deleted successfully'))->success();
            return back();
        }
        flash(__('Default address can not be deleted'))->warning();
        return back();
    }

    public function set_default($id){
        foreach (Auth::user()->addresses as $key => $address) {
            $address->set_default = 0;
            $address->save();
        }
        $address = Address::findOrFail($id);
        $address->set_default = 1;
        $address->save();

        return back();
    }
}
