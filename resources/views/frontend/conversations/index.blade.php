@extends('frontend.layouts.app')

@section('content')
    @if(Auth::user()->user_type == 'seller')
        @include('frontend.inc.alert_review_shop')
    @endif
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @if(Auth::user()->user_type == 'seller')
                        @include('frontend.inc.seller_side_nav')
                    @elseif(Auth::user()->user_type == 'customer')
                        @include('frontend.inc.customer_side_nav')
                    @endif
                </div>

                <div class="col-lg-9">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0 d-inline-block">
                                        {{ translate('Conversations') }}
                                    </h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{ translate('Home') }}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{ translate('Dashboard') }}</a></li>
                                            <li><a href="{{ route('conversations.index') }}">{{ translate('Conversations') }}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card no-border mt-4 p-3">
                            <div class="py-4">
                                @if(count($conversations) > 0)
                                @foreach ($conversations as $key => $conversation)
                                    <div class="block block-comment border-bottom">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="block-image">
                                                    @if (Auth::user()->id == $conversation->sender_id)
                                                        <img @if ($conversation->receiver->avatar_original == null) src="{{ my_asset('frontend/images/user.png') }}" @else src="{{ my_asset($conversation->receiver->avatar_original) }}" @endif class="rounded-circle">
                                                    @else
                                                        <img @if ($conversation->sender->avatar_original == null) src="{{ my_asset('frontend/images/user.png') }}" @else src="{{ my_asset($conversation->sender->avatar_original) }}" @endif class="rounded-circle">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <p>
                                                    @if (Auth::user()->id == $conversation->sender_id)
                                                        <a href="javascript:;">{{ $conversation->receiver->name }}</a>
                                                    @else
                                                        <a href="javascript:;">{{ $conversation->sender->name }}</a>
                                                    @endif
                                                    <br>
                                                    <span class="comment-date">
                                                        {{ date('h:i:m d-m-Y', strtotime($conversation->messages->last()->created_at)) }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-9">
                                                <div class="block-body">
                                                    <div class="block-body-inner pb-3">
                                                        <div class="row no-gutters">
                                                            <div class="col">
                                                                <h4 class="heading heading-6">
                                                                    <a href="{{ route('conversations.show', encrypt($conversation->id)) }}">
                                                                        {{ $conversation->title }}
                                                                    </a>
                                                                    @if ((Auth::user()->id == $conversation->sender_id && $conversation->sender_viewed == 0) || (Auth::user()->id == $conversation->receiver_id && $conversation->receiver_viewed == 0))
                                                                        <span class="badge badge-pill badge-danger">{{ translate('New') }}</span>
                                                                    @endif
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <p class="comment-text mt-0">
                                                            {{ $conversation->messages->last()->message }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @else
                                <div class="text-center">
                                    <i class="la la-meh-o d-block heading-1 alpha-5" style="font-size: 80px!important;"></i>
                                    <span class="h4" style="font-size: 20px; color: #818a91">{{__('There is no conversations')}}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="pagination-wrapper py-4">
                            <ul class="pagination justify-content-end">
                                {{ $conversations->links() }}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
