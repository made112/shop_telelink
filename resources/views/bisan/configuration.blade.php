@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">{{translate('Bisan Settings')}}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('env_key_update.update') }}" method="POST">
                        @csrf
                        <div id="bisan">
                            <div class="form-group">
                                <input type="hidden" name="types[]" value="BISAN_URL">
                                <div class="col-lg-3">
                                    <label class="control-label">{{translate('BISAN URL')}}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="BISAN_URL" value="{{  env('BISAN_URL') }}" placeholder="{{ translate('BISAN URL') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="types[]" value="BISAN_USER">
                                <div class="col-lg-3">
                                    <label class="control-label">{{translate('BISAN USER')}}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="BISAN_USER" value="{{  env('BISAN_USER') }}" placeholder="{{ translate('BISAN USER') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="types[]" value="BISAN_PASSWORD">
                                <div class="col-lg-3">
                                    <label class="control-label">{{translate('BISAN PASSWORD')}}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="BISAN_PASSWORD" value="{{  env('BISAN_PASSWORD') }}" placeholder="{{ translate('BISAN PASSWORD') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="types[]" value="BISAN_API_ID">
                                <div class="col-lg-3">
                                    <label class="control-label">{{translate('BISAN API ID')}}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="BISAN_API_ID" value="{{  env('BISAN_API_ID') }}" placeholder="{{ translate('BISAN API ID') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="types[]" value="BISAN_API_SECRET">
                                <div class="col-lg-3">
                                    <label class="control-label">{{translate('BISAN API SECRET')}}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="BISAN_API_SECRET" value="{{  env('BISAN_API_SECRET') }}" placeholder="{{ translate('BISAN API SECRET') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="types[]" value="CODE_ACCOUNT">
                                <div class="col-lg-3">
                                    <label class="control-label">{{translate('CODE ACCOUNT')}}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="CODE_ACCOUNT" value="{{  env('CODE_ACCOUNT') }}" placeholder="{{ translate('CODE ACCOUNT') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="types[]" value="Default_Sales_Price">
                                <div class="col-lg-3">
                                    <label class="control-label">{{translate('Default Sales Price List')}}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="Default_Sales_Price" value="{{  env('Default_Sales_Price') }}" placeholder="{{ translate('Default Sales Price') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{translate('Save')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@endif
