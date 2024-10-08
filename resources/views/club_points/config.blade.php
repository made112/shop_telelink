@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('22', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
@php
    $club_point_convert_rate = \App\BusinessSetting::where('type', 'club_point_convert_rate')->first();
    $max_earn_point_user = \App\BusinessSetting::where('type', 'max_earn_point_user')->first();
@endphp
    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{__('Convert Point To Wallet')}}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('point_convert_rate_store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="club_point_convert_rate">
                        <div class="form-group">
                            <div class="col-lg-4">
                                <label class="control-label">{{__('Set Point For ')}} {{ single_price(1) }}</label>
                            </div>
                            <div class="col-lg-5">
                                <input type="number" min="0" step="0.01" class="form-control" name="value" @if ($club_point_convert_rate != null) value="{{ $club_point_convert_rate->value }}" @endif placeholder="100" required>
                            </div>
                            <div class="col-lg-3">
                                <label class="control-label">{{__('Points')}}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{__('Save')}}</button>
                            </div>
                        </div>
                    </form>
                    <p class="h5 mt-2">{{ __('Note: You need to activate wallet option first before using club point addon.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{__('Maximum Earning Point For User')}}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('max_earn_point_user') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="max_earn_point_user">
                        <div class="form-group">
                            <div class="col-lg-4">
                                <label class="control-label">{{__('Set Point')}}</label>
                            </div>
                            <div class="col-lg-5">
                                <input type="number" min="0" class="form-control" name="value" @if ($max_earn_point_user != null) value="{{ $max_earn_point_user->value }}" @endif placeholder="{{translate('Point')}}" required>
                            </div>
                            <div class="col-lg-3">
                                <label class="control-label">{{__('Points')}}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{__('Save')}}</button>
                            </div>
                        </div>
                    </form>
                    <p class="h5 mt-2">{{ __('Note: Not be activated any process as a deduction or coupon from fees when access to this point, leaves after returning to admin determines the process manually under the off-site agreement.') }}</p>
                </div>
            </div>
        </div>
    </div>

@endsection
@endif
