@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="col-sm-12">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{__('Send Newsletter')}}</h3>
        </div>
        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('sms.send') }}" method="POST" enctype="multipart/form-data">
        	@csrf
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">{{__('Mobile')}} ({{__('Users')}})</label>
                    <div class="col-sm-10">
                        <select class="form-control @error('user_phones') is-invalid @enderror demo-select2-multiple-selects" name="user_phones[]" multiple>
                            @foreach($users as $user)
                                @if ($user->phone != null)
                                    <option value="{{$user->phone}}">{{$user->phone}}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('user_phones')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="subject">{{__('SMS subject')}}</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" id="subject">
                        @error('subject')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">{{__('SMS content')}}</label>
                    <div class="col-sm-10">
                        <textarea class="editor @error('content') is-invalid @enderror" name="content"></textarea>
                        @error('content')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{__('Send')}}</button>
            </div>
        </form>
        <!--===================================================-->
        <!--End Horizontal Form-->

    </div>
</div>

@endsection
@endif
