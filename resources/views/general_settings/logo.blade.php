@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="col-lg-6 col-lg-offset-3">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{translate('Logo Settings')}}</h3>
            </div>

            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" action="{{ route('generalsettings.logo.store') }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="logo">{{translate('Frontend logo')}} <small>(max height 40px)</small></label>
                        <div class="col-sm-9">
                            <input type="file" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror">
                            @error('logo')
                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="logo">{{translate('Frontend logo footer')}} <small>(max height 40px)</small></label>
                        <div class="col-sm-9">
                            <input type="file" id="logo_footer" name="logo_footer" class="form-control @error('logo_footer') is-invalid @enderror">
                            @error('logo_footer')
                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="admin_logo">{{translate('Admin logo')}} <small>(60x60)</small></label>
                        <div class="col-sm-9">
                            <input type="file" id="admin_logo" name="admin_logo" class="form-control @error('admin_logo') is-invalid @enderror">
                            @error('admin_logo')
                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="favicon">{{translate('Favicon')}} <small>(32x32)</small></label>
                        <div class="col-sm-9">
                            <input type="file" id="favicon" name="favicon" class="form-control @error('favicon') is-invalid @enderror">
                            @error('favicon')
                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="admin_login_background">{{translate('Admin login background image')}} <small>(1920x1080)</small></label>
                        <div class="col-sm-9">
                            <input type="file" id="admin_login_background" name="admin_login_background" class="form-control @error('admin_login_background') is-invalid @enderror">
                            @error('admin_login_background')
                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                            @enderror
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="admin_login_sidebar">{{translate('Admin login sidebar image')}} <small>(600x500)</small></label>
                        <div class="col-sm-9">
                            <input type="file" id="admin_login_sidebar" name="admin_login_sidebar" class="form-control @error('admin_login_sidebar') is-invalid @enderror">
                            @error('admin_login_sidebar')
                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <button class="btn btn-purple" type="submit">{{translate('Save')}}</button>
                </div>
            </form>
            <!--===================================================-->
            <!--End Horizontal Form-->

        </div>
    </div>

@endsection
@endif
