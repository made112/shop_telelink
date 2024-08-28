@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('17', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title text-center">{{__('Reason For Refund Request')}}</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{__('Reason')}}</label>
                    <div class="col-lg-8">
                        <p class="bord-all pad-all">{{ $refund->reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@endif
