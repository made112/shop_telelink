<div class="header bg-white">
    <!-- Top Bar -->
{{--    <div class="top-navbar">--}}
{{--        <div class="container">--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-7 col">--}}
{{--                    <ul class="inline-links d-lg-inline-block d-flex justify-content-between">--}}
{{--                        <li class="dropdown" id="currency-change">--}}
{{--                            @php--}}
{{--                                if(Session::has('currency_code')){--}}
{{--                                    $currency_code = Session::get('currency_code');--}}
{{--                                }--}}
{{--                                else{--}}
{{--                                    $currency_code = \App\Currency::findOrFail(\App\BusinessSetting::where('type', 'system_default_currency')->first()->value)->code;--}}
{{--                                }--}}
{{--                            @endphp--}}
{{--                            <a href="" class="dropdown-toggle top-bar-item" data-toggle="dropdown">--}}
{{--                                {{ \App\Currency::where('code', $currency_code)->first()->name }} {{ (\App\Currency::where('code', $currency_code)->first()->symbol) }}--}}
{{--                            </a>--}}
{{--                            <ul class="dropdown-menu">--}}
{{--                                @foreach (\App\Currency::where('status', 1)->get() as $key => $currency)--}}
{{--                                    <li class="dropdown-item @if($currency_code == $currency->code) active @endif">--}}
{{--                                        <a href="" data-currency="{{ $currency->code }}">{{ $currency->name }} ({{ $currency->symbol }})</a>--}}
{{--                                    </li>--}}
{{--                                @endforeach--}}
{{--                            </ul>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}

{{--                <div class="col-5 text-right d-none d-lg-block">--}}
{{--                    <ul class="inline-links">--}}
{{--                        <li>--}}
{{--                            <a href="{{ route('orders.track') }}" class="top-bar-item">{{ translate('Track Order')}}</a>--}}
{{--                        </li>--}}
{{--                        @if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated)--}}
{{--                            <li>--}}
{{--                                <a href="{{ route('affiliate.apply') }}" class="top-bar-item">{{ translate('Be an affiliate partner')}}</a>--}}
{{--                            </li>--}}
{{--                        @endif--}}
{{--                        @auth--}}
{{--                        <li>--}}
{{--                            <a href="{{ route('dashboard') }}" class="top-bar-item">{{ translate('My Panel')}}</a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a href="{{ route('logout') }}" class="top-bar-item">{{ translate('Logout')}}</a>--}}
{{--                        </li>--}}
{{--                        @else--}}
{{--                        <li>--}}
{{--                            <a href="{{ route('user.login') }}" class="top-bar-item">{{ translate('Login')}}</a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a href="{{ route('user.registration') }}" class="top-bar-item">{{ translate('Registration')}}</a>--}}
{{--                        </li>--}}
{{--                        @endauth--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <!-- END Top Bar -->

    <!-- mobile menu -->
    <div class="mobile-side-menu d-lg-none">
        <div class="side-menu-overlay opacity-0" onclick="sideMenuClose()"></div>
        <div class="side-menu-wrap opacity-0">
            <div class="side-menu closed">
                <div class="side-menu-header ">
                    <div class="side-menu-close" onclick="sideMenuClose()">
                        <i class="la la-close"></i>
                    </div>

                    @auth
                        <div class="widget-profile-box px-3 py-4 d-flex align-items-center">
                            @if (Auth::user()->avatar_original != null)
                                <div class="image " style="background-image:url('{{ my_asset(Auth::user()->avatar_original) }}')"></div>
                            @else
                                <div class="image " style="background-image:url('{{ my_asset('frontend/images/user.png') }}')"></div>
                            @endif

                            <div class="name">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="side-login px-3 pb-3">
                            <a href="{{ route('logout') }}">{{translate('Sign Out')}}</a>
                        </div>
                    @else
                        <div class="widget-profile-box px-3 py-4 d-flex align-items-center">
                                <div class="image " style="background-image:url('{{ my_asset('frontend/images/icons/user-placeholder.jpg') }}')"></div>
                        </div>
                        <div class="side-login px-3 pb-3">
                            <a href="{{ route('user.login') }}">{{translate('Sign In')}}</a>
                            <a href="{{ route('user.registration') }}">{{translate('Registration')}}</a>
                        </div>
                    @endauth
                </div>
                <div class="side-menu-list px-3">
                    <ul class="side-user-menu">
                        <li>
                            <a href="{{ route('home') }}">
                                <i class="la la-home"></i>
                                <span>{{translate('Home')}}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('dashboard') }}">
                                <i class="la la-dashboard"></i>
                                <span>{{translate('Dashboard')}}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('purchase_history.index') }}">
                                <i class="la la-file-text"></i>
                                <span>{{translate('Purchase History')}}</span>
                            </a>
                        </li>
                        @auth
                            @php
                                $conversation = \App\Conversation::where('sender_id', Auth::user()->id)->where('sender_viewed', '1')->get();
                            @endphp
                            @if (\App\BusinessSetting::where('type', 'conversation_system')->first()->value == 1)
                                <li>
                                    <a href="{{ route('conversations.index') }}" class="{{ areActiveRoutesHome(['conversations.index', 'conversations.show'])}}">
                                        <i class="la la-comment"></i>
                                        <span class="category-name">
                                            {{translate('Conversations')}}
                                            @if (count($conversation) > 0)
                                                <span class="ml-2" style="color:green"><strong>({{ count($conversation) }})</strong></span>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                            @endif
                        @endauth
                        <li>
                            <a href="{{ route('compare') }}">
                                <i class="la la-refresh"></i>
                                <span>{{translate('Compare')}}</span>
                                @if(Session::has('compare'))
                                    <span class="badge" id="compare_items_sidenav">{{ count(Session::get('compare'))}}</span>
                                @else
                                    <span class="badge" id="compare_items_sidenav">0</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cart') }}">
                                <i class="la la-shopping-cart"></i>
                                <span>{{translate('Cart')}}</span>
                                @if(Session::has('cart'))
                                    <span class="badge" id="cart_items_sidenav">{{ count(Session::get('cart'))}}</span>
                                @else
                                    <span class="badge" id="cart_items_sidenav">0</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('wishlists.index') }}">
                                <i class="la la-heart-o"></i>
                                <span>{{translate('Wishlist')}}</span>
                            </a>
                        </li>

                        @if(\App\BusinessSetting::where('type', 'classified_product')->first()->value == 1)
                        <li>
                            <a href="{{ route('customer_products.index') }}">
                                <i class="la la-diamond"></i>
                                <span>{{translate('Classified Products')}}</span>
                            </a>
                        </li>
                        @endif

                        @if (\App\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)
                            <li>
                                <a href="{{ route('wallet.index') }}">
                                    <i class="la la-dollar"></i>
                                    <span>{{translate('My Wallet')}}</span>
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('profile') }}">
                                <i class="la la-user"></i>
                                <span>{{translate('Manage Profile')}}</span>
                            </a>
                        </li>

                        @php
                        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                        $club_point_addon = \App\Addon::where('unique_identifier', 'club_point')->first();
                        @endphp
                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                            <li>
                                <a href="{{ route('customer_refund_request') }}" class="{{ areActiveRoutesHome(['customer_refund_request'])}}">
                                    <i class="la la-file-text"></i>
                                    <span class="category-name">
                                        {{translate('Sent Refund Request')}}
                                    </span>
                                </a>
                            </li>
                        @endif

                        @if ($club_point_addon != null && $club_point_addon->activated == 1)
                            <li>
                                <a href="{{ route('earnng_point_for_user') }}" class="{{ areActiveRoutesHome(['earnng_point_for_user'])}}">
                                    <i class="la la-dollar"></i>
                                    <span class="category-name">
                                        {{translate('Earning Points')}}
                                    </span>
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('support_ticket.index') }}" class="{{ areActiveRoutesHome(['support_ticket.index', 'support_ticket.show'])}}">
                                <i class="la la-support"></i>
                                <span class="category-name">
                                    {{translate('Support Ticket')}}
                                </span>
                            </a>
                        </li>

                    </ul>
                    @if (Auth::check() && Auth::user()->user_type == 'seller')
                        <div class="sidebar-widget-title py-0">
                            <span>{{translate('Shop Options')}}</span>
                        </div>
                        <ul class="side-seller-menu">
                            <li>
                                <a href="{{ route('seller.products') }}">
                                    <i class="la la-diamond"></i>
                                    <span>{{translate('Products')}}</span>
                                </a>
                            </li>

                            @if (\App\Addon::where('unique_identifier', 'pos_system')->first() != null && \App\Addon::where('unique_identifier', 'pos_system')->first()->activated)
                                <li>
                                    <a href="{{route('poin-of-sales.seller_index')}}">
                                        <i class="la la-fax"></i>
                                        <span>
                                            {{translate('POS Manager')}}
                                        </span>
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('orders.index') }}">
                                    <i class="la la-file-text"></i>
                                    <span>{{translate('Orders')}}</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('shops.index') }}">
                                    <i class="la la-cog"></i>
                                    <span>{{translate('Shop Setting')}}</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('withdraw_requests.index') }}">
                                    <i class="la la-money"></i>
                                    <span>
                                        {{translate('Money Withdraw')}}
                                    </span>
                                </a>
                            </li>

                            @php
                                $conversation = \App\Conversation::where('receiver_id', Auth::user()->id)->where('receiver_viewed', '1')->get();
                            @endphp
                            @if (\App\BusinessSetting::where('type', 'conversation_system')->first()->value == 1)
                                <li>
                                    <a href="{{ route('conversations.index') }}" class="{{ areActiveRoutesHome(['conversations.index', 'conversations.show'])}}">
                                        <i class="la la-comment"></i>
                                        <span class="category-name">
                                            {{translate('Conversations')}}
                                            @if (count($conversation) > 0)
                                                <span class="ml-2" style="color:green"><strong>({{ count($conversation) }})</strong></span>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('payments.index') }}">
                                    <i class="la la-cc-mastercard"></i>
                                    <span>{{translate('Payment History')}}</span>
                                </a>
                            </li>
                        </ul>
                        <div class="sidebar-widget-title py-0">
                            <span>{{translate('Earnings')}}</span>
                        </div>
                        <div class="widget-balance py-3">
                            <div class="text-center">
                                <div class="heading-4 strong-700 mb-4">
                                    @php
                                        $orderDetails = \App\OrderDetail::where('seller_id', Auth::user()->id)->where('created_at', '>=', date('-30d'))->get();
                                        $total = 0;
                                        foreach ($orderDetails as $key => $orderDetail) {
                                            if($orderDetail->order != null && $orderDetail->order != null && $orderDetail->order->payment_status == 'paid'){
                                                $total += $orderDetail->price;
                                            }
                                        }
                                    @endphp
                                    <small class="d-block text-sm alpha-5 mb-2">{{translate('Your earnings (current month)')}}</small>
                                    <span class="p-2 bg-base-1 rounded">{{ single_price($total) }}</span>
                                </div>
                                <table class="text-left mb-0 table w-75 m-auto">
                                    <tbody>
                                        <tr>
                                            @php
                                                $orderDetails = \App\OrderDetail::where('seller_id', Auth::user()->id)->get();
                                                $total = 0;
                                                foreach ($orderDetails as $key => $orderDetail) {
                                                    if($orderDetail->order != null && $orderDetail->order->payment_status == 'paid'){
                                                        $total += $orderDetail->price;
                                                    }
                                                }
                                            @endphp
                                            <td class="p-1 text-sm">
                                                {{translate('Total earnings')}}:
                                            </td>
                                            <td class="p-1">
                                                {{ single_price($total) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            @php
                                                $orderDetails = \App\OrderDetail::where('seller_id', Auth::user()->id)->where('created_at', '>=', date('-60d'))->where('created_at', '<=', date('-30d'))->get();
                                                $total = 0;
                                                foreach ($orderDetails as $key => $orderDetail) {
                                                    if($orderDetail->order != null && $orderDetail->order->payment_status == 'paid'){
                                                        $total += $orderDetail->price;
                                                    }
                                                }
                                            @endphp
                                            <td class="p-1 text-sm">
                                                {{translate('Last Month earnings')}}:
                                            </td>
                                            <td class="p-1">
                                                {{ single_price($total) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    <!-- <div class="sidebar-widget-title py-0">
                        <span>Categories</span>
                    </div>
                    <ul class="side-seller-menu">
                        @foreach (\App\Category::all() as $key => $category)
                            <li>
                            <a href="{{ route('products.category', $category->slug) }}" class="text-truncate">
                                <img class="cat-image lazyload" src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($category->icon) }}" width="13" alt="{{ __($category->name) }}">
                                <span>{{ __($category->name) }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul> -->
                </div>
            </div>
        </div>
    </div>
    <!-- end mobile menu -->

    <div class="position-relative logo-bar-area gry-bg">
        <div class="">
            <div class="container">
                <div class="row no-gutters align-items-center">
                    <div class="col-lg-3 col-8">
                        <div class="d-flex">
                            <div class="d-flex justify-content-center align-items-center d-lg-none mobile-menu-icon-box">
                                <!-- Navbar toggler  -->
                                <a href="" onclick="sideMenuOpen(this)">
                                    <div class="hamburger-icon">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </a>
                            </div>

                            <!-- Brand/Logo -->
                            <a class="navbar-brand w-100" href="{{ route('home') }}">
                                @php
                                    $generalsetting = \App\GeneralSetting::first();
                                @endphp
                                @if($generalsetting->logo != null)
                                    <img src="{{ my_asset($generalsetting->logo) }}" alt="{{ env('APP_NAME') }}">
                                @else
                                    <img src="{{ my_asset('frontend/images/logo/logo.png') }}" alt="{{ env('APP_NAME') }}">
                                @endif
                            </a>

                            @if(Route::currentRouteName() != 'home' && Route::currentRouteName() != 'categories.all')
                                <div class="d-none d-xl-block category-menu-icon-box" style="align-self: center">
                                    <div class="dropdown-toggle navbar-light category-menu-icon" id="category-menu-icon">
                                        <span class="navbar-toggler-icon"></span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-9 col-4 position-static">
                        <div class="d-flex w-100 justify-content-center align-items-center">
                            <div class="search-box flex-grow-1 px-4">
                                <form action="{{ route('search') }}" method="GET">
                                    <div class="d-flex position-relative">
                                        <div class="d-lg-none search-box-back">
                                            <button class="" type="button"><i class="la la-long-arrow-left"></i></button>
                                        </div>
                                        <div class="w-100">
                                            <input type="text" aria-label="Search" id="search" name="q" class="w-100" placeholder="{{translate('I am shopping for...')}}" autocomplete="off">
                                        </div>
                                        <div class="form-group category-select d-none d-xl-block">
                                            <select class="form-control selectpicker" name="category">
                                                <option value="">{{translate('All Categories')}}</option>
                                                @foreach (\App\Category::where('featured', 1)->get() as $key => $category)
                                                <option value="{{ $category->slug }}"
                                                    @isset($category_id)
                                                        @if ($category_id == $category->id)
                                                            selected
                                                        @endif
                                                    @endisset
                                                    >{{ __($category->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button class="d-none d-lg-block" type="submit">
                                            <i class="la la-search la-flip-horizontal"></i>
                                        </button>
                                        <div class="typed-search-box d-none">
                                            <div class="search-preloader">
                                                <div class="loader"><div></div><div></div><div></div></div>
                                            </div>
                                            <div class="search-nothing d-none">

                                            </div>
                                            <div id="search-content">

                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>

                            <div class="logo-bar-icons d-inline-block ml-auto d-flex justify-content-center align-items-center">
                                <div class="d-inline-block d-lg-none">
                                    <div class="nav-search-box">
                                        <a href="#" class="nav-box-link">
                                            <i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i>
                                        </a>
                                    </div>
                                </div>
                                @if(Auth::check() and Auth::user()->user_type == 'admin')
                                    <div class="d-none d-lg-inline-block">
                                        <div class="dropdown nav-compare-box" id="compare">
                                            <div class="d-flex justify-content-center align-items-center flex-column">
                                                <i class="fa fa-user-o d-inline-block nav-box-icon"></i>
                                                <span class="nav-box-text d-none d-xl-inline-block">
                                                    <a href="{{ route('admin.dashboard') }}" class="top-bar-item">{{ translate('My Admin')}}</a>
                                                </span>
                                            </div>
                                            <ul class="dropdown-menu dropdown-menu-right px-0" role="menu">
                                                <li>
                                                    <div class="dropdown-cart custom-dropdown px-0">
                                                        <div class="dc-header px-2 py-0 pt-1">
                                                            <h3 class="heading heading-6 strong-700 clearfix">{{translate('Hi')}} {{ Auth::user()->name }}
                                                                <a href="{{ route('logout') }}" class="strong-300 heading heading-count float-right">{{ translate('Logout')}}</a>
                                                            </h3>
                                                        </div>
                                                        <div class="dd-content">
                                                            <div class="subcontent mt-2">
                                                                <h6 class="heading-6 strong-700 p-2">My Admin</h6>
                                                                <ul class="px-2">
                                                                    <li><a href="{{ url('/admin/profile') }}">{{translate('Profile')}}</a></li>
                                                                    <li><a href="{{ route('orders.index.admin') }}">{{translate('My Orders')}}</a></li>
                                                                    <li><a href="{{ route('conversations.admin_index') }}">{{translate('My Conversations')}}</a></li>
                                                                    <li><a href="{{ route('orders.track') }}">{{translate('Track Order')}}</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    {{--                                Start Messaging--}}
                                    <div class="d-none d-lg-inline-block">
                                        <div class="nav-wishlist-box dropdown">
                                            <a href="{{ route('conversations.admin_index') }}" id="messages-dropdown" class="d-flex justify-content-center align-items-center flex-md-column">
                                                <i class="la la-envelope-o d-inline-block nav-box-icon"></i>
                                                <span class="nav-box-text d-none d-xl-inline-block">{{translate('Messages')}}
                                            </span>
                                                @if(Auth::check())
                                                    <span class="nav-box-number">{{ $unread_messages }}</span>
                                                @else
                                                    <span class="nav-box-number">0</span>
                                                @endif
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right px-0">
                                                <li>
                                                    @auth
                                                        @if(count($conversations_nav) > 0)
                                                            <div class="dropdown-cart px-0 pb-2">
                                                                <div class="dc-header">
                                                                    <h3 class="heading heading-6 strong-700">{{translate('Messaging')}}</h3>
                                                                </div>
                                                                <div class="dropdown-cart-items c-scrollbar"></div>
                                                                @foreach($conversations_nav->take(4) as $key=>$conv)
                                                                    <div class="dc-item conv-item">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="dc-image">
                                                                                <a href="#">
                                                                                    @if (Auth::user()->id == $conv->sender_id)
                                                                                        <img @if ($conv->receiver->avatar_original == null) src="{{ my_asset('frontend/images/user.png') }}" @else src="{{ my_asset($conv->receiver->avatar_original) }}" @endif class="w-100 rounded-circle">
                                                                                    @else
                                                                                        <img @if ($conv->sender->avatar_original == null) src="{{ my_asset('frontend/images/user.png') }}" @else src="{{ my_asset($conv->sender->avatar_original) }}" @endif class="w-100 rounded-circle">
                                                                                    @endif
                                                                                </a>
                                                                            </div>
                                                                            <div class="dd-content pl-2 position-relative w-100">
                                                                            <span class="dc-product-name text-capitalize strong-600 mb-1">
                                                                            @if (Auth::user()->id == $conv->sender_id)
                                                                                    <a href="javascript:;">{{ $conv->receiver->name }}</a>
                                                                                @else
                                                                                    <a href="javascript:;">{{ $conv->sender->name }}</a>
                                                                                @endif
                                                                            </span>
                                                                                <span class="comment-date">
                                                                                @php
                                                                                    if(App::getLocale() == 'en'){
                                                                                        \Carbon\CarbonInterval::setLocale('en');
                                                                                    }else {
                                                                                        \Carbon\CarbonInterval::setLocale('ar');
                                                                                    }
                                                                                @endphp
                                                                                    {{$conv->messages->last()->updated_at->diffForHumans()}}
                                                                            </span>
                                                                                <span>
                                                                                <h4 class="heading heading-6 mt-2">
                                                                                    <a class="title-conv" href="{{ route('conversations.admin_show', encrypt($conv->id)) }}">
                                                                                        {{ $conv->title }}
                                                                                    </a>
                                                                                    @if ((Auth::user()->id == $conv->sender_id && $conv->sender_viewed == 0) || (Auth::user()->id == $conv->receiver_id && $conv->receiver_viewed == 0))
                                                                                        <span class="badge badge-pill badge-danger">{{ translate('New') }}</span>
                                                                                    @endif
                                                                                </h4>
                                                                            </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <div class="pt-3 text-center">
                                                                    <a href="{{route('conversations.admin_index')}}">
                                                                        <span>{{translate('View all messages')}}</span>
                                                                        <i class="fa fa-chevron-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="dropdown-cart custom-dropdown no-auth-dropdown px-0">
                                                                <div class="dd-content text-center border-bottom">
                                                                    <img height="70" src="{{my_asset('frontend/images/no-message.png')}}">
                                                                    <p class="title">
                                                                        {{translate('No new messages')}}
                                                                    </p>
                                                                </div>
                                                                <div class="pt-3 text-center">
                                                                    <a href="{{route('conversations.index')}}">
                                                                        <span>{{translate('View all messages')}}</span>
                                                                        <i class="fa fa-chevron-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    @else
                                                        <div class="dropdown-cart custom-dropdown no-auth-dropdown px-0">
                                                            <div class="dc-header">
                                                                <h3 class="title heading heading-6 strong-700">{{translate('Do not miss messages')}}</h3>
                                                            </div>
                                                            <div class="dd-content">
                                                                <span class="content d-block">{{ translate('Please log in to view all messages.') }}</span>
                                                                <a class="content-btn d-block" href="{{route('user.login')}}">Log in</a>
                                                            </div>
                                                        </div>
                                                    @endauth
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    {{--                                End Messaging--}}
                                @else
                                    @auth
                                        {{--                                    My iBuy--}}
                                        <div class="d-none d-lg-inline-block">
                                            <div class="dropdown nav-compare-box" id="compare">
                                                <div class="d-flex justify-content-center align-items-center flex-column">
                                                    <i class="fa fa-user-o d-inline-block nav-box-icon"></i>
                                                    <span class="nav-box-text d-none d-xl-inline-block">
                                                        <a href="{{ route('dashboard') }}" class="top-bar-item">{{ translate('My iBuy')}}</a>
                                                    </span>
                                                </div>
                                                <ul class="dropdown-menu dropdown-menu-right px-0" role="menu">
                                                    <li>
                                                        <div class="dropdown-cart custom-dropdown px-0">
                                                            <div class="dc-header px-2 py-0 pt-1">
                                                                <h3 class="heading heading-6 strong-700 clearfix">{{translate('Hi')}} {{ Auth::user()->name }}
                                                                    <a href="{{ route('logout') }}" class="strong-300 heading heading-count float-right">{{ translate('Logout')}}</a>
                                                                </h3>
                                                            </div>
                                                            <div class="dd-content">
                                                                <div class="subcontent mt-2">
                                                                    <h6 class="heading-6 strong-700 p-2">My iBuy</h6>
                                                                    <ul class="px-2">
                                                                        <li><a href="{{ route('purchase_history.index') }}">{{translate('My Orders')}}</a></li>
                                                                        <li><a href="{{ route('wishlists.index') }}">{{translate('My Favorites')}}</a></li>
                                                                        <li><a href="{{ route('profile') }}">{{translate('My Account')}}</a></li>
                                                                        <li><a href="{{ route('orders.track') }}">{{translate('Track Order')}}</a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        {{--                                    End My iBuy--}}
                                    @else
                                        {{--                                    Sign in & Register--}}
                                        <div class="d-none d-lg-inline-block">
                                            <div class="dropdown nav-compare-box" id="compare">
                                                <div>
                                                    <i class="fa fa-user-o d-inline-block nav-box-icon"></i>
                                                    <span class="nav-box-text d-none d-xl-inline-block">
                                                    <a href="{{route('user.login')}}">{{translate('Sign In')}}</a>
                                                    <br>
                                                    <a href="{{route('user.registration')}}">{{translate('Join Free')}}</a>
                                                </span>
                                                </div>
                                                <ul class="dropdown-menu dropdown-menu-right px-0" role="menu">
                                                    <li>
                                                        <div class="dropdown-cart custom-dropdown px-0">
                                                            <div class="dc-header px-2 py-0 pt-1">
                                                                <h3 class="heading heading-6 strong-700">{{translate('Get Start Now')}}</h3>
                                                            </div>
                                                            <div class="dd-content pt-2">
                                                                <div class="content-login text-center pb-2 px-2 border-bottom">
                                                                    <a class="d-block custom-login" href="{{route('user.login')}}">{{translate('Sign In')}}</a>
                                                                    <span class="d-block m-0 py-1 text-capitalize">{{translate('or')}}</span>
                                                                    <a class="d-block" href="{{route('user.registration')}}">{{translate('Join Free')}}</a>
                                                                </div>
                                                                @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                                                <div class="text-center">
                                                                    <span class="d-block m-0 py-2">{{translate('Continue with:')}}</span>
                                                                    <div class="login-with">
                                                                        @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                                                            <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="facebook">
                                                                                <i class="fa fa-facebook"></i>
                                                                            </a>
                                                                        @endif
                                                                        @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                                                            <a href="{{ route('social.login', ['provider' => 'google']) }}" class="google">
                                                                                <i class="fa fa-google"></i>
                                                                            </a>
                                                                        @endif
                                                                        @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                                                            <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="twitter">
                                                                                <i class="fa fa-twitter"></i>
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                <div class="subcontent mt-2">
                                                                    <h6 class="heading-6 strong-700 p-2">My iBuy</h6>
                                                                    <ul class="px-2">
                                                                        <li><a href="{{ route('purchase_history.index') }}">{{translate('My Orders')}}</a></li>
                                                                        <li><a href="{{ route('wishlists.index') }}">{{translate('My Favorites')}}</a></li>
                                                                        <li><a href="{{ route('profile') }}">{{translate('My Account')}}</a></li>
                                                                        <li><a href="{{ route('orders.track') }}">{{translate('Track Order')}}</a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        {{--                                   End Sign in & Register--}}

                                    @endauth
                                    {{--                                Start Messaging--}}
                                    <div class="d-none d-lg-inline-block">
                                        <div class="nav-wishlist-box dropdown">
                                            <a href="{{ route('conversations.index') }}" id="messages-dropdown" class="d-flex justify-content-center align-items-center flex-md-column">
                                                <i class="la la-envelope-o d-inline-block nav-box-icon"></i>
                                                <span class="nav-box-text d-none d-xl-inline-block">{{translate('Messages')}}
                                            </span>
                                                @if(Auth::check())
                                                    <span class="nav-box-number">{{ $unread_messages }}</span>
                                                @else
                                                    <span class="nav-box-number">0</span>
                                                @endif
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right px-0">
                                                <li>
                                                    @auth
                                                        @if(count($conversations_nav) > 0)
                                                            <div class="dropdown-cart px-0 pb-2">
                                                                <div class="dc-header">
                                                                    <h3 class="heading heading-6 strong-700">{{translate('Messaging')}}</h3>
                                                                </div>
                                                                <div class="dropdown-cart-items c-scrollbar"></div>
                                                                @foreach($conversations_nav->take(4) as $key=>$conv)
                                                                    <div class="dc-item conv-item">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="dc-image">
                                                                                <a href="#">
                                                                                    @if (Auth::user()->id == $conv->sender_id)
                                                                                        <img @if ($conv->receiver->avatar_original == null) src="{{ my_asset('frontend/images/user.png') }}" @else src="{{ my_asset($conv->receiver->avatar_original) }}" @endif class="w-100 rounded-circle">
                                                                                    @else
                                                                                        <img @if ($conv->sender->avatar_original == null) src="{{ my_asset('frontend/images/user.png') }}" @else src="{{ my_asset($conv->sender->avatar_original) }}" @endif class="w-100 rounded-circle">
                                                                                    @endif
                                                                                </a>
                                                                            </div>
                                                                            <div class="dd-content pl-2 position-relative w-100">
                                                                            <span class="dc-product-name text-capitalize strong-600 mb-1">
                                                                            @if (Auth::user()->id == $conv->sender_id)
                                                                                    <a href="javascript:;">{{ $conv->receiver->name }}</a>
                                                                                @else
                                                                                    <a href="javascript:;">{{ $conv->sender->name }}</a>
                                                                                @endif
                                                                            </span>
                                                                                <span class="comment-date">
                                                                                @php
                                                                                    if(App::getLocale() == 'en'){
                                                                                        \Carbon\CarbonInterval::setLocale('en');
                                                                                    }else {
                                                                                        \Carbon\CarbonInterval::setLocale('ar');
                                                                                    }
                                                                                @endphp
                                                                                    {{$conv->messages->last()->updated_at->diffForHumans()}}
                                                                            </span>
                                                                                <span>
                                                                                <h4 class="heading heading-6 mt-2">
                                                                                    <a class="title-conv" href="{{ route('conversations.show', encrypt($conv->id)) }}">
                                                                                        {{ $conv->title }}
                                                                                    </a>
                                                                                    @if ((Auth::user()->id == $conv->sender_id && $conv->sender_viewed == 0) || (Auth::user()->id == $conv->receiver_id && $conv->receiver_viewed == 0))
                                                                                        <span class="badge badge-pill badge-danger">{{ translate('New') }}</span>
                                                                                    @endif
                                                                                </h4>
                                                                            </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <div class="pt-3 text-center">
                                                                    <a href="{{route('conversations.index')}}">
                                                                        <span>{{translate('View all messages')}}</span>
                                                                        <i class="fa fa-chevron-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="dropdown-cart custom-dropdown no-auth-dropdown px-0">
                                                                <div class="dd-content text-center border-bottom">
                                                                    <img height="70" src="{{my_asset('frontend/images/no-message.png')}}">
                                                                    <p class="title">
                                                                        {{translate('No new messages')}}
                                                                    </p>
                                                                </div>
                                                                <div class="pt-3 text-center">
                                                                    <a href="{{route('conversations.index')}}">
                                                                        <span>{{translate('View all messages')}}</span>
                                                                        <i class="fa fa-chevron-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    @else
                                                        <div class="dropdown-cart custom-dropdown no-auth-dropdown px-0">
                                                            <div class="dc-header">
                                                                <h3 class="title heading heading-6 strong-700">{{translate('Do not miss messages')}}</h3>
                                                            </div>
                                                            <div class="dd-content">
                                                                <span class="content d-block">{{translate('Please log in to view all messages.')}}</span>
                                                                <a class="content-btn d-block" href="{{route('user.login')}}">{{ translate('Log in') }}</a>
                                                            </div>
                                                        </div>
                                                    @endauth
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    {{--                                End Messaging--}}
                                    {{--                                Start Orders--}}
                                    <div class="d-none d-lg-inline-block">
                                        <div class="nav-wishlist-box dropdown" id="orders">
                                            <a href="{{route('purchase_history.index')}}" id="orders-dropdown" class="d-flex justify-content-center align-items-center flex-md-column">
                                                <i class="la la-list-alt d-inline-block nav-box-icon"></i>
                                                <span class="nav-box-text d-none d-xl-inline-block">{{translate('Orders')}}
                                            </span>
                                                @if(Auth::check())
                                                    <span class="nav-box-number">{{ $count_orders  }}</span>
                                                @else
                                                    <span class="nav-box-number">0</span>
                                                @endif
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right px-0">
                                                <li>
                                                    @auth
                                                        <div class="dropdown-cart custom-dropdown no-auth-dropdown px-0">
                                                            @if($count_orders > 0)
                                                                <div class="dd-content text-center border-bottom">
                                                                    <a class="media" href="{{ route('purchase_history.index') }}" style="position:relative">
                                                                        <span class="badge badge-header badge-info" style="right:auto;left:3px;"></span>
                                                                        <div class="media-body">
                                                                            <p class="mar-no text-nowrap text-main text-semibold">{{ $count_orders }} {{translate('New order(s)')}}</p>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div class="dd-content text-center border-bottom">
                                                                    <img height="70" src="{{my_asset('frontend/images/no-message.png')}}">
                                                                    <p class="title">
                                                                        {{translate('No new orders')}}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                            <div class="pt-3 text-center">
                                                                <a href="{{route('purchase_history.index')}}">
                                                                    <span>{{translate('View all orders')}}</span>
                                                                    <i class="fa fa-chevron-right"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="dropdown-cart custom-dropdown no-auth-dropdown px-0">
                                                            <div class="dc-header">
                                                                <h3 class="title heading heading-6 strong-700">{{translate('Do not miss orders')}}</h3>
                                                            </div>
                                                            <div class="dd-content">
                                                                <span class="content d-block">{{ translate('Please log in to view all orders.') }}</span>
                                                                <a class="content-btn d-block" href="{{route('user.login')}}">{{ translate('Log in') }}</a>
                                                            </div>
                                                        </div>
                                                    @endauth
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    {{--                                End Orders--}}
                                    {{--                                Start Shipping Cart--}}
                                    <div class="d-inline-block">
                                        <div class="nav-cart-box dropdown" id="cart_items">
                                            <a href="{{route('cart')}}" class="nav-box-link d-flex justify-content-center align-items-center flex-md-column p-0">
                                                <i class="la la-shopping-cart d-inline-block nav-box-icon"></i>
                                                <span class="nav-box-text d-none d-xl-inline-block">{{translate('Cart')}}</span>
                                                @if(Session::has('cart'))
                                                    <span class="nav-box-number">{{ count(Session::get('cart'))}}</span>
                                                @else
                                                    <span class="nav-box-number">0</span>
                                                @endif
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right px-0">
                                                <li>
                                                    @if(Session::has('cart'))
                                                        @if(count($cart = Session::get('cart')) > 0)
                                                            <div class="dropdown-cart px-0">
                                                                <div class="dc-header">
                                                                    <h3 class="heading heading-6 strong-700">{{translate('Cart Items')}}</h3>
                                                                </div>

                                                                <div class="dropdown-cart-items c-scrollbar">
                                                                    @php
                                                                        $total = 0;
                                                                    @endphp
                                                                    @foreach($cart as $key => $cartItem)
                                                                        @php
                                                                            $product = \App\Product::find($cartItem['id']);
                                                                            $total = $total + $cartItem['price']*$cartItem['quantity'];
                                                                        @endphp
                                                                        <div class="dc-item">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="dc-image">
                                                                                    <a href="{{ route('product', $product->slug) }}">
                                                                                        <img
                                                                                            src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                                                            data-src="{{ my_asset($product->thumbnail_img) }}" class="img-fluid lazyload"
                                                                                            alt="{{ __($product->name) }}">
                                                                                    </a>
                                                                                </div>
                                                                                <div class="dc-content">
                                                                            <span class="d-block dc-product-name text-capitalize strong-600 mb-1">
                                                                                <a href="{{ route('product', $product->slug) }}">
                                                                                    {{ __($product->name) }}
                                                                                </a>
                                                                            </span>

                                                                                    <span class="dc-quantity">x{{ $cartItem['quantity'] }}</span>
                                                                                    <span class="dc-price">{{ single_price($cartItem['price']*$cartItem['quantity']) }}</span>
                                                                                </div>
                                                                                <div class="dc-actions">
                                                                                    <button onclick="removeFromCart({{ $key }})">
                                                                                        <i class="la la-close"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="dc-item py-3">
                                                                    <span class="subtotal-text">{{translate('Subtotal')}}</span>
                                                                    <span class="subtotal-amount">{{ single_price($total) }}</span>
                                                                </div>
                                                                <div class="py-2 text-center dc-btn">
                                                                    <ul class="inline-links inline-links--style-3">
                                                                        <li class="px-1">
                                                                            <a href="{{ route('cart') }}" class="link link--style-1 text-capitalize btn btn-base-1 px-3 py-1">
                                                                                <i class="la la-shopping-cart"></i> {{translate('View cart')}}
                                                                            </a>
                                                                        </li>
                                                                        @if (Auth::check())
                                                                            <li class="px-1">
                                                                                <a href="{{ route('checkout.delivery_info') }}" class="link link--style-1 text-capitalize btn btn-base-1 px-3 py-1 light-text">
                                                                                    <i class="la la-mail-forward"></i> {{translate('Checkout')}}
                                                                                </a>
                                                                            </li>
                                                                        @endif
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="dropdown-cart custom-dropdown no-auth-dropdown px-0">
                                                                <div class="dd-content text-center border-bottom">
                                                                    <img height="70" src="{{my_asset('frontend/images/no-message.png')}}">
                                                                    <p class="title">
                                                                        {{translate('Cart is empty')}}
                                                                    </p>
                                                                </div>
                                                                <div class="pt-3 text-center">
                                                                    <a href="{{route('cart')}}">
                                                                        <span>{{translate('View Cart')}}</span>
                                                                        <i class="fa fa-chevron-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="dropdown-cart custom-dropdown no-auth-dropdown px-0">
                                                            <div class="dd-content text-center border-bottom">
                                                                <img height="70" src="{{my_asset('frontend/images/no-message.png')}}">
                                                                <p class="title">
                                                                    {{translate('Cart is empty')}}
                                                                </p>
                                                            </div>
                                                            <div class="pt-3 text-center">
                                                                <a href="{{route('cart')}}">
                                                                    <span>{{translate('View Cart')}}</span>
                                                                    <i class="fa fa-chevron-right"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    {{--                                End Shipping Cart--}}
                                @endif
                                {{--                                Start Languages--}}
                                <div class="d-lg-inline-block">
                                    <div class="nav-wishlist-box dropdown" id="lang-change">
                                        @php
                                            if(Session::has('locale')){
                                                $locale = Session::get('locale', Config::get('app.locale'));
                                            }
                                            else{
                                                $locale = 'en';
                                            }
                                        @endphp
                                        <a href="#" id="orders-dropdown" class="d-flex justify-content-center align-items-center flex-md-column">
                                            <i class="la la-globe d-inline-block nav-box-icon"></i>
                                            <span class="nav-box-text d-none d-xl-inline-block">{{translate($locale)}}
                                            </span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right px-0">
                                            @foreach (\App\Language::all() as $key => $language)
                                                <li class="dropdown-item p-0 @if($locale == $language->code) active @endif">
                                                    <a href="#" data-flag="{{ $language->code }}"><img src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset('frontend/images/icons/flags/'.$language->code.'.png') }}" class="flag lazyload" alt="{{ $language->name }}" height="11"><span class="language">{{ translate($language->name) }}</span></a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                {{--                                End Languages--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hover-category-menu" id="hover-category-menu">
            <div class="container">
                <div class="row no-gutters position-relative">
                    <div class="col-lg-3 position-static">
                        <div class="category-sidebar" id="category-sidebar">
                            <div class="all-category">
                                <span>{{translate('CATEGORIES')}}</span>
                                <a href="{{ route('categories.all') }}" class="d-inline-block">{{ translate('See All') }} ></a>
                            </div>
                            <ul class="categories">
                                @foreach (\App\Category::where('featured', 1)->take(11)->get() as $key => $category)
                                    @php
                                        $brands = array();
                                    @endphp
                                    <li class="category-nav-element" data-id="{{ $category->id }}">
                                        <a href="{{ route('products.category', $category->slug) }}">
                                            <img class="cat-image lazyload" src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($category->icon) }}" width="30" alt="{{ __($category->name) }}">
                                            <span class="cat-name">{{ __($category->name) }}</span>
                                        </a>
                                        @if(count($category->subcategories)>0)
                                            <div class="sub-cat-menu c-scrollbar">
                                                <div class="c-preloader">
                                                    <i class="fa fa-spin fa-spinner"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Navbar -->

    <!-- <div class="main-nav-area d-none d-lg-block">
        <nav class="navbar navbar-expand-lg navbar--bold navbar--style-2 navbar-light bg-default">
            <div class="container">
                <div class="collapse navbar-collapse align-items-center justify-content-center" id="navbar_main">
                    <ul class="navbar-nav">
                        @foreach (\App\Search::orderBy('count', 'desc')->get()->take(5) as $key => $search)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('suggestion.search', $search->query) }}">{{ $search->query }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>
    </div> -->
</div>
