<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GeneralSetting;
use Illuminate\Support\Facades\Validator;
use ImageOptimizer;
use App\Http\Controllers\BusinessSettingsController;

class GeneralSettingController extends Controller
{
    protected function validator(array $data, $type = 'add')
    {
        $validation_array = [
            'logo' => 'nullable|file|mimes:jpeg,bmp,png,jpg,svg|max:1024',
            'admin_logo' => 'nullable|file|max:1024|mimes:jpeg,bmp,png,jpg,svg',
            'favicon' => 'nullable|file|max:1024|mimes:jpeg,bmp,png,jpg,svg',
            'admin_login_background' => 'nullable|file|max:1024|mimes:jpeg,bmp,png,jpg,svg',
            'admin_login_sidebar' => 'nullable|file|max:1024|mimes:jpeg,bmp,png,jpg,svg',
        ];
        $validation_messages = [
            'logo.mimes' => 'logo extension must be [png, jpg, jpeg, bmp].',
            'admin_logo.mimes' => 'Admin Logo extension must be [png, jpg, jpeg, bmp].',
            'favicon.mimes' => 'favicon extension must be [png, jpg, jpeg, bmp].',
            'admin_login_background.mimes' => 'Admin Login Background extension must be [png, jpg, jpeg, bmp].',
            'admin_login_sidebar.mimes' => 'Admin Login Sidebar extension must be [png, jpg, jpeg, bmp].'
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
        $generalsetting = GeneralSetting::first();
        return view("general_settings.index", compact("generalsetting"));
    }

    public function logo()
    {

        $generalsetting = GeneralSetting::first();
        return view("general_settings.logo", compact("generalsetting"));
    }

    //updates the logo and favicons of the system
    public function storeLogo(Request $request)
    {
        $this->validator($request->all())->validate();

//        return $request ;
        $generalsetting = GeneralSetting::first();

        if($request->hasFile('logo')){
            $generalsetting->logo = $request->file('logo')->store('uploads/logo');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->logo);
        }
        if($request->hasFile('logo_footer')){
            $generalsetting->logo_footer = $request->file('logo_footer')->store('uploads/logo-footer');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->logo);
        }

        if($request->hasFile('admin_logo')){
            $generalsetting->admin_logo = $request->file('admin_logo')->store('uploads/admin_logo');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->admin_logo);
        }

        if($request->hasFile('favicon')){
            $generalsetting->favicon = $request->file('favicon')->store('uploads/favicon');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->favicon);
        }

        if($request->hasFile('admin_login_background')){
            $generalsetting->admin_login_background = $request->file('admin_login_background')->store('uploads/admin_login_background');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->admin_login_background);
        }

        if($request->hasFile('admin_login_sidebar')){
            $generalsetting->admin_login_sidebar = $request->file('admin_login_sidebar')->store('uploads/admin_login_sidebar');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->admin_login_sidebar);
        }

        if($generalsetting->save()){
            flash('Logo settings has been updated successfully')->success();
            return redirect()->route('generalsettings.logo');
        }
        else{
            flash('Something went wrong')->error();
            return back();
        }
    }

    public function color()
    {
        $generalsetting = GeneralSetting::first();
        return view("general_settings.color", compact("generalsetting"));
    }

    //updates system ui color
    public function storeColor(Request $request)
    {
        $generalsetting = GeneralSetting::first();
        $generalsetting->frontend_color = $request->frontend_color;

        if($generalsetting->save()){
            flash('Color settings has been updated successfully')->success();
            return redirect()->route('generalsettings.color');
        }
        else{
            flash('Something went wrong')->error();
            return back();
        }
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
        //
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
        $generalsetting = GeneralSetting::first();
        $generalsetting->site_name = $request->name;
        $generalsetting->address = $request->address;
        $generalsetting->phone = $request->phone;
        $generalsetting->email = $request->email;
        $generalsetting->description = $request->description;
        $generalsetting->descriptionAr = $request->descriptionAr;
        $generalsetting->facebook = $request->facebook;
        $generalsetting->instagram = $request->instagram;
        $generalsetting->twitter = $request->twitter;
        $generalsetting->youtube = $request->youtube;
        $generalsetting->google_plus = $request->google_plus;

        if($generalsetting->save()){
            $businessSettingsController = new BusinessSettingsController;
            $businessSettingsController->overWriteEnvFile('APP_NAME',$request->name);
            $businessSettingsController->overWriteEnvFile('APP_TIMEZONE',$request->timezone);

            flash('GeneralSetting has been updated successfully')->success();
            return redirect()->route('generalsettings.index');
        }
        else{
            flash('Something went wrong')->error();
            return back();
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
        //
    }
}
