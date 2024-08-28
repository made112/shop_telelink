<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Staff;
use App\Role;
use App\User;
use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    protected function validator(array $data, $type = 'add', $staff = null)
    {
        $validation_array = [
            "name"  => "required|string|max:100",
            'password' => 'required|string|min:6',
            'email'=> "required|email|unique:users,email",
            'mobile'=>"required|string|max:20",
            'role_id'=>"required|numeric"
        ];

        $validation_messages = [];
        if ($type === 'update' and $staff !== null) {
            $validation_array['password'] = 'nullable|string|min:6';
            $validation_array['email'] = [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($staff->user->id),
            ];
        }
        return Validator::make($data, $validation_array, $validation_messages);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = Staff::all();
        return view('staffs.index', compact('staffs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        return view('staffs.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();
        if(User::where('email', $request->email)->first() == null){
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;
            $user->user_type = "staff";
            $user->password = Hash::make($request->password);
            if($user->save()){
                $staff = new Staff;
                $staff->user_id = $user->id;
                $staff->role_id = $request->role_id;
                if($staff->save()){
                    flash(__('Staff has been inserted successfully'))->success();
                    return redirect()->route('staffs.index');
                }
            }
        }

        flash(__('Email already used'))->error();
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
        $staff = Staff::findOrFail(decrypt($id));
        $roles = Role::all();
        return view('staffs.edit', compact('staff', 'roles'));
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
        $staff = Staff::findOrFail($id);
        $this->validator($request->all(), 'update', $staff)->validate();
        $user = $staff->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->mobile;
        if(strlen($request->password) > 0){
            $user->password = Hash::make($request->password);
        }
        if($user->save()){
            $staff->role_id = $request->role_id;
            if($staff->save()){
                flash(__('Staff has been updated successfully'))->success();
                return redirect()->route('staffs.index');
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
        User::destroy(Staff::findOrFail($id)->user->id);
        if(Staff::destroy($id)){
            flash(__('Staff has been deleted successfully'))->success();
            return redirect()->route('staffs.index');
        }

        flash(__('Something went wrong'))->error();
        return back();
    }
}
