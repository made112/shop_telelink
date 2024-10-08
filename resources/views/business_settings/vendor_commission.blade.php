@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <!--Horizontal Form-->
                <form class="form-horizontal" action="{{ route('business_settings.vendor_commission.update') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    <div class="panel-body">
                        <div class="form-group">
                            <input type="hidden" name="type" value="{{ $business_settings->type }}">
                            <label class="col-lg-3 control-label">{{ translate('Seller Commission') }}</label>
                            <div class="col-lg-7">
                                <input type="number" min="0" step="0.01" value="{{ $business_settings->value }}" placeholder="{{translate('Seller Commission')}}" name="value" class="form-control @error('value') is-invalid @enderror">
                                @error('value')
                                <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-1">
                                <option class="form-control">%</option>
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

        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading bord-btm">
                    <h3 class="panel-title">{{translate('Note')}}</h3>
                </div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            1. {{ $business_settings->value }}% {{translate('of seller product price will be deducted from seller earnings') }}.
                        </li>
                        <li class="list-group-item">
                            1. {{translate('This commission only works when Category Based Commission is turned off from Business Settings') }}.
                        </li>
                        <li class="list-group-item">
                            1. {{translate('Commission doesn\'t work if seller package system add-on is activated') }}.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
@endif
