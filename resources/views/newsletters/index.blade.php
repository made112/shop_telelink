@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="col-sm-12">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Send Newsletter')}}</h3>
        </div>
        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('newsletters.send') }}" method="POST" enctype="multipart/form-data">
        	@csrf
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">{{translate('Emails')}} ({{translate('Users')}})</label>
                    <div class="col-sm-10">
                        <select class="form-control @error('user_emails') is-invalid @enderror selectpicker" name="user_emails[]" multiple data-selected-text-format="count" data-actions-box="true">
                            @foreach($users as $user)
                                <option value="{{$user->email}}">{{$user->email}}</option>
                            @endforeach
                        </select>
                        @error('user_emails')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">{{translate('Emails')}} ({{translate('Subscribers')}})</label>
                    <div class="col-sm-10">
                        <select class="form-control @error('subscriber_emails') is-invalid @enderror selectpicker" name="subscriber_emails[]" multiple data-selected-text-format="count" data-actions-box="true">
                            @foreach($subscribers as $subscriber)
                                <option value="{{$subscriber->email}}">{{$subscriber->email}}</option>
                            @endforeach
                        </select>
                        @error('subscriber_emails')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="subject">{{translate('Newsletter subject')}}</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" id="subject" required>
                        @error('subject')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">{{translate('Newsletter content')}}</label>
                    <div class="col-sm-10">
                        <textarea class="editor @error('content') form-control is-invalid @enderror" name="content" ></textarea>
                        @error('content')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{translate('Send')}}</button>
            </div>
        </form>
        <!--===================================================-->
        <!--End Horizontal Form-->

    </div>
</div>

@endsection
@endif
