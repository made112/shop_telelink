@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('18', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="col-lg-12">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">#{{ $conversation->title }} (Between @if($conversation->sender != null) {{ $conversation->sender->name }} @endif and @if($conversation->receiver != null) {{ $conversation->receiver->name }} @endif)
                &sbquo; {{ translate('Product Name') }} : <a href="{{ route('product', $conversation->product->slug) }}" target="_blank">{{ $conversation->product->name }}</a>
            </h3>
        </div>

        <div class="panel-body">
            @foreach($conversation->messages as $message)
                <div class="form-group">
                    <a class="media-left" href="#"><img class="img-circle img-sm" alt="Profile Picture" @if($message->user != null) src="{{ uploaded_asset($message->user->avatar_original) }}" @endif onerror="this.onerror=null;this.src='{{ my_asset('img/avatar-place.png') }}';">
                    </a>
                    <div class="media-body">
                        <div class="comment-header">
                            <a href="#" class="media-heading box-inline text-main text-bold">
                                @if ($message->user != null)
                                    {{ $message->user->name }}
                                @endif
                            </a>
                            <p class="text-muted text-sm">{{$message->created_at}}</p>
                        </div>
                        <p>
                            {{ $message->message }}
                        </p>
                    </div>
                </div>
            @endforeach
            @if (Auth::user()->id == $conversation->receiver_id || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea "@if(app()->getLocale() == 'sa') dir="rtl" @endif class="form-control" rows="4" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="text-right">
                        <button type="submit" class="btn btn-info">{{translate('Send')}}</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection
@endif
