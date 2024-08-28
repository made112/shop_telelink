<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected function validator(array $data)
    {
        $validation_array = [
            "name"  => "required|string|max:100",
            'permissions'=>"required|array|min:1"
        ];
        $validation_messages = [
            "name.required" => "The name field is required.",
            "name.string" => "The name field must be string.",
            "name.max" => "The name field must be no more than 100 characters.",
            "permissions.required" => "The permissions field is required.",
            "permissions.array" => "The permissions field must be array.",
            "permissions.min" => "The permissions field must be at least one.",
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
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create');
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
        if($request->has('permissions')){
            $role = new Role;
            $role->name = $request->name;
            $role->permissions = json_encode($request->permissions);
            if($role->save()){
                flash(__('Role has been inserted successfully'))->success();
                return redirect()->route('roles.index');
            }
        }
        flash(__('Something went wrong'))->error();
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
        $role = Role::findOrFail(decrypt($id));
        return view('roles.edit', compact('role'));
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
        $validations = $this->validator($request->all());
        if($validations->fails()){
            $errors = '';
            foreach ($validations->errors()->all('') as $key=> $value) {
                $errors .= $value;
            }
            flash($errors)->error();
            return  back();
        }
        $role = Role::findOrFail($id);

        if($request->has('permissions')){
            $role->name = $request->name;
            $role->permissions = json_encode($request->permissions);
            if($role->save()){
                flash(__('Role has been updated successfully'))->success();
                return redirect()->route('roles.index');
            }
        }
        flash(__('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Role::destroy($id)){
            flash(__('Role has been deleted successfully'))->success();
            return redirect()->route('roles.index');
        }
        else{
            flash(__('Something went wrong'))->error();
            return back();
        }
    }
}
