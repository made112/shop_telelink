@extends('frontend.layouts.app')

@section('content')
    <div class="py-2 bg-white">
        <div class="container">
            <div class="row gutters-10">
                @foreach (\App\Banner::where('position', 1)->where('published', 1)->limit(2)->get() as $key => $banner)
                    <div
                        class="col-lg-{{ 12/count(\App\Banner::where('position', 1)->where('published', 1)->limit(2)->get()) }}">
                        <div class="media-banner mb-3 mb-lg-0">
                            <a href="{{ $banner->url }}" target="_blank" class="banner-container">
                                <img src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}"
                                     data-src="{{ my_asset($banner->photo) }}" alt="{{ env('APP_NAME') }} promo"
                                     class="img-fluid lazyload">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <section class="home-banner-area pb-4">
        <div class="container">
            <div class="row no-gutters position-relative">
                <div class="col-lg-3 position-static order-2 order-lg-0">
                    <div class="category-sidebar">
                        <ul class="categories no-scrollbar">
                            @foreach (\App\Category::where('featured', 1)->take(11)->get() as $key => $category)
                                @php
                                    $brands = array();
                                @endphp
                                <li class="category-nav-element" data-id="{{ $category->id }}">
                                    <a href="{{ route('products.category', $category->slug) }}">
                                        <img class="cat-image lazyload"
                                             src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                             data-src="{{ my_asset($category->banner) }}" width="40"
                                             alt="{{ __($category->name) }}">
                                        <span class="cat-name">{{ __($category->name) }}</span>
                                        <i class="fa fa-chevron-right cat-chevron-icon d-none d-lg-block"></i>
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
                            <li class="category-nav-element">
                                <a href="{{ route('categories.all') }}">
                                    <img class="cat-image lazyload"
                                         src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                         data-src="{{ my_asset('frontend/images/icons/categories.png') }}" width="30"
                                         alt="{{ translate('All Category') }}">
                                    <span class="cat-name">{{ translate('All') }} {{ translate('Categories') }}</span>
                                    <i class="fa fa-chevron-right cat-chevron-icon d-none d-lg-block"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="row no-gutters position-relative">
                        @php
                            $num_todays_deal = count(filter_products(\App\Product::where('published', 1)->where('todays_deal', 1 ))->get());
                            $featured_categories = \App\Category::where('featured', 1)->get();
                        @endphp

                        <div
                            class="@if($num_todays_deal > 0) col-lg-9 @else col-lg-12 @endif order-1 order-lg-0 @if(count($featured_categories) == 0) home-slider-full @endif">
                            <div class="home-slide">
                                <div class="home-slide">
                                    <div class="slick-carousel" data-slick-arrows="true" data-slick-dots="true"
                                         data-slick-autoplay="true">
                                        @foreach (\App\Slider::where('published', 1)->get() as $key => $slider)
                                            <div class="" style="height:325px;">
                                                <a href="{{ $slider->link }}" target="_blank">
                                                    <img class="d-block w-100 h-100 lazyload"
                                                         src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}"
                                                         data-src="{{ my_asset($slider->photo) }}"
                                                         alt="{{ env('APP_NAME')}} promo">
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @if (count($featured_categories) > 0)

                                {{--                        <div class="trending-category  d-none d-lg-block">--}}
                                {{--                            <ul>--}}
                                {{--                                @foreach ($featured_categories->take(7) as $key => $category)--}}
                                {{--                                    <li @if ($key == 0) class="active" @endif>--}}
                                {{--                                        <div class="trend-category-single">--}}
                                {{--                                            <a href="{{ route('products.category', $category->slug) }}" class="d-block">--}}
                                {{--                                                <div class="name">{{ __($category->name) }}</div>--}}
                                {{--                                                <div class="img">--}}
                                {{--                                                    <img src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($category->banner) }}" alt="{{ __($category->name) }}" class="lazyload img-fit">--}}
                                {{--                                                </div>--}}
                                {{--                                            </a>--}}
                                {{--                                        </div>--}}
                                {{--                                    </li>--}}
                                {{--                                @endforeach--}}
                                {{--                            </ul>--}}
                                {{--                        </div>--}}
                            @endif
                        </div>

                        @if($num_todays_deal > 0)

                            <div class="col-lg-3 d-none d-lg-block">
                                <div class="flash-deal-box bg-white h-100">
                                    {{--                        <div class="title text-center p-2 gry-bg">--}}
                                    {{--                            <h3 class="heading-6 mb-0">--}}
                                    {{--                                {{ translate('Todays Deal') }}--}}
                                    {{--                                <span class="badge badge-danger">{{ translate('Hot') }}</span>--}}
                                    {{--                            </h3>--}}
                                    {{--                        </div>--}}
                                    <div class="flash-content c-scrollbar c-height">
                                        @foreach (filter_products(\App\Product::where('published', 1)->where('todays_deal', '1'))->get() as $key => $product)
                                            @if ($product != null)
                                                <a href="{{ route('product', $product->slug) }}"
                                                   class="d-block flash-deal-item">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col-12">
                                                            <div class="img">
                                                                <img
                                                                    class="lazyload img-fit"
                                                                    src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                                    data-src="{{ my_asset($product->thumbnail_img) }}"
                                                                    onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                                    alt="{{ __($product->name) }}">
                                                            </div>
                                                        </div>
                                                        {{--                                            <div class="col">--}}
                                                        {{--                                                <div class="price">--}}
                                                        {{--                                                    <span class="d-block">{{ home_discounted_base_price($product->id) }}</span>--}}
                                                        {{--                                                    @if(home_base_price($product->id) != home_discounted_base_price($product->id))--}}
                                                        {{--                                                        <del class="d-block">{{ home_base_price($product->id) }}</del>--}}
                                                        {{--                                                    @endif--}}
                                                        {{--                                                </div>--}}
                                                        {{--                                            </div>--}}
                                                    </div>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div id="section_featured">

                    </div>
                </div>

            </div>
        </div>

    </section>

    <div id="section_sponsored_product">

    </div>

    @php
        $flash_deal = \App\FlashDeal::where('status', 1)->where('featured', 1)->first();
    @endphp
    @if($flash_deal != null && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date)
        <section class="mt-4">
            <div class="container">
                <div class="px-2 py-4 p-md-4 bg-white shadow-sm">
                    <div class="section-title-1 clearfix ">
                        <h3 class="heading-5 strong-700 mb-0 float-left">
                            {{ translate('Flash Sale') }}
                        </h3>
                    </div>
                    <div class="caorusel-box arrow-round gutters-5">
                        <div class="row no-gutters position-relative">
                            <div class="col-12 col-lg-3 mb-3 mb-lg-0">
                                <div class="flash-deal-box custom-box"
                                     style="background-image: url({{asset('public/frontend/images/flash-deals.png')}});">
                                    <span class="mb-2">{{translate('Deals end in')}}</span>
                                    <div class="countdown countdown--style-1 countdown--style-1-v1 "
                                         data-countdown-date="{{ date('m/d/Y', $flash_deal->end_date) }}"
                                         data-countdown-label="show"></div>
                                    <ul class="custom-btn">
                                        <li><a href="{{ route('flash-deal-details', $flash_deal->slug) }}"
                                               class="active">{{ translate('View More') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-12 col-lg-9">
                                <div class="slick-carousel" data-slick-items="5" data-slick-xl-items="5"
                                     data-slick-lg-items="4" data-slick-md-items="3" data-slick-sm-items="2"
                                     data-slick-xs-items="2">
                                    @foreach ($flash_deal->flash_deal_products as $key => $flash_deal_product)
                                        @php
                                            $product = \App\Product::find($flash_deal_product->product_id);
                                        @endphp
                                        @if ($product != null && $product->published != 0)
                                            <div class="caorusel-card">
                                                <div class="product-card-2 card card-product shop-cards">
                                                    <div class="card-body p-0">
                                                        <div class="card-image">
                                                            <a href="{{ route('product', $product->slug) }}"
                                                               class="d-block">
                                                                <img
                                                                    class="img-fit lazyload mx-auto"
                                                                    src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                                    data-src="{{ my_asset($product->thumbnail_img) }}"
                                                                    onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                                    alt="{{ __($product->name) }}">
                                                            </a>
                                                        </div>

                                                        <div class="p-md-3 p-2">
                                                            <div class="price-box">
                                                                @if(home_base_price($product->id) != home_discounted_base_price($product->id))
                                                                    <del
                                                                        class="old-product-price strong-400">{{ home_base_price($product->id) }}</del>
                                                                @endif
                                                                <span
                                                                    class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                                            </div>
                                                            <div class="star-rating star-rating-sm mt-1">
                                                                {{ renderStarRating($product->rating) }}
                                                            </div>
                                                            <h2 class="product-title p-0">
                                                                <a href="{{ route('product', $product->slug) }}"
                                                                   class=" text-truncate">{{ __($product->name) }}</a>
                                                            </h2>
                                                            @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                                                <div
                                                                    class="club-point mt-2 bg-soft-base-1 border-light-base-1 border">
                                                                    {{ translate('Club Point') }}:
                                                                    <span
                                                                        class="strong-700 float-right">{{ $product->earn_point }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <div id="section_best_selling">

    </div>

    <section class="my-4 d-none d-lg-block">
        <div class="container">
            <div class="px-2 py-4 p-md-4 bg-white shadow-sm">
                <div class="section-title-1 clearfix ">
                    <h3 class="heading-5 strong-700 mb-0 float-left">
                        {{ translate('Our Services') }}
                    </h3>
                </div>
                <div class="row no-gutters position-relative d-flex flex-nowrap c-scrollbar c-width">
                    <div class="col-12 col-sm-6 col-lg-2 col-custom">
                        <div class="single-service-area d-flex align-items-center mb-3 wow fadeInUp"
                             data-wow-delay="300ms"
                             style="visibility: visible; animation-delay: 300ms; animation-name: fadeInUp;">
                            <div class="service-icon">
                                <i class="la la-sitemap"></i>
                            </div>
                            <div class="service-content">
                                <h4>{{translate('Great Value Products')}}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2 col-custom">
                        <div class="single-service-area d-flex align-items-center mb-3 wow fadeInUp"
                             data-wow-delay="300ms"
                             style="visibility: visible; animation-delay: 300ms; animation-name: fadeInUp;">
                            <div class="service-icon">
                                <i class="la la-truck"></i>
                            </div>
                            <div class="service-content">
                                <h4>{{translate('Worry-free Shopping')}}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2 col-custom">
                        <div class="single-service-area d-flex align-items-center mb-3 wow fadeInUp"
                             data-wow-delay="300ms"
                             style="visibility: visible; animation-delay: 300ms; animation-name: fadeInUp;">
                            <div class="service-icon">
                                <i class="la la-check"></i>
                            </div>
                            <div class="service-content">
                                <h4>{{translate('Customer Service')}}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2 col-custom">
                        <div class="single-service-area d-flex align-items-center mb-3 wow fadeInUp"
                             data-wow-delay="300ms"
                             style="visibility: visible; animation-delay: 300ms; animation-name: fadeInUp;">
                            <div class="service-icon">
                                <i class="la la-money"></i>
                            </div>
                            <div class="service-content">
                                <h4>{{translate('Different Payment Method')}}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2 col-custom">
                        <div class="single-service-area d-flex align-items-center mb-3 wow fadeInUp"
                             data-wow-delay="300ms"
                             style="visibility: visible; animation-delay: 300ms; animation-name: fadeInUp;">
                            <div class="service-icon">
                                <i class="la la-cubes"></i>
                            </div>
                            <div class="service-content">
                                <h4>{{translate('Track Your Package')}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="my-4">
        <div class="container">
            <div class="px-2 py-4 p-md-4 bg-white shadow-sm">
                <div class="section-title-1 clearfix ">
                    <h3 class="heading-5 strong-700 mb-0 float-left">
                        {{ translate('New Arrivals') }}
                    </h3>
                </div>
                @php
                    $new_arrivals = \App\Product::where('published', 1)->orderBy('created_at', 'desc')->limit(6)->get();
                @endphp
                <div class="caorusel-box arrow-round gutters-5">
                    <div class="row no-gutters position-relative">
                        <div class="col-12 col-lg-3 mb-3 mb-lg-0">
                            <div class="flash-deal-box custom-box"
                                 style="background-image: url({{asset('public/frontend/images/flash-deals.png')}});">
                                <div class="new_arrival_icon" style="">
                                    <span>
                                    {{translate('New Arrival')}}
                                    </span>
                                    <span class="new_collection">
                                        New Collection
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-9">
                            <div class="slick-carousel" data-slick-items="5" data-slick-xl-items="5"
                                 data-slick-lg-items="4" data-slick-md-items="3" data-slick-sm-items="2"
                                 data-slick-xs-items="2">
                                @foreach ($new_arrivals as $key => $new_arrival)
                                    @if ($new_arrival != null && $new_arrival->published != 0)
                                        <div class="caorusel-card">
                                            <div class="product-card-2 card card-product shop-cards">
                                                <div class="card-body p-0">
                                                    <div class="card-image">
                                                        <a href="{{ route('product', $new_arrival->slug) }}"
                                                           class="d-block">
                                                            @php
                                                                $img = '';
                                                                if($new_arrival->thumbnail_img) {
                                                                    $img = explode('/',$new_arrival->thumbnail_img)[2];
                                                                }
                                                            @endphp
                                                            <img
                                                                class="img-fit lazyload mx-auto"
                                                                src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                                data-src="{{ my_asset('thumbnails/'.$img) }}"
                                                                onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                                alt="{{ __($new_arrival->name) }}">
                                                        </a>
                                                    </div>

                                                    <div class="p-md-3 p-2">
                                                        <div class="price-box">
                                                            @if(home_base_price($new_arrival->id) != home_discounted_base_price($new_arrival->id) && $new_arrival->unit_price > 0)
                                                                <del
                                                                    class="old-product-price strong-400">{{ home_base_price($new_arrival->id) }}</del>
                                                            @endif
                                                            <span
                                                                class="product-price strong-600">{{ home_discounted_base_price($new_arrival->id) }}</span>
                                                        </div>
                                                        <div class="star-rating star-rating-sm mt-1">
                                                            {{ renderStarRating($new_arrival->rating) }}
                                                        </div>
                                                        <h2 class="product-title p-0">
                                                            <a href="{{ route('product', $new_arrival->slug) }}"
                                                               class=" text-truncate">{{ __($new_arrival->name) }}</a>
                                                        </h2>
                                                        @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                                            <div
                                                                class="club-point mt-2 bg-soft-base-1 border-light-base-1 border">
                                                                {{ translate('Club Point') }}:
                                                                <span
                                                                    class="strong-700 float-right">{{ $new_arrival->earn_point }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="section_home_categories">

    </div>

    @if(\App\BusinessSetting::where('type', 'classified_product')->first()->value == 1)
        @php
            $customer_products = \App\CustomerProduct::where('status', '1')->where('published', '1')->take(10)->get();
        @endphp
        @if (count($customer_products) > 0)
            <section class="mb-4">
                <div class="container">
                    <div class="px-2 py-4 p-md-4 bg-white shadow-sm">
                        <div class="section-title-1 clearfix">
                            <h3 class="heading-5 strong-700 mb-0 float-left">
                                <span class="mr-4">{{ translate('Classified Ads') }}</span>
                            </h3>
                            <ul class="inline-links float-right">
                                <li><a href="{{ route('customer.products') }}"
                                       class="active">{{ translate('View More') }}</a></li>
                            </ul>
                        </div>
                        <div class="caorusel-box arrow-round">
                            <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5"
                                 data-slick-lg-items="4" data-slick-md-items="3" data-slick-sm-items="2"
                                 data-slick-xs-items="2">
                                @foreach ($customer_products as $key => $customer_product)
                                    <div
                                        class="product-card-2 card card-product my-2 mx-1 mx-sm-2 shop-cards shop-tech">
                                        <div class="card-body p-0">
                                            <div class="card-image">
                                                <a href="{{ route('customer.product', $customer_product->slug) }}"
                                                   class="d-block">
                                                    <img
                                                        class="img-fit lazyload mx-auto"
                                                        src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                        data-src="{{ my_asset($customer_product->thumbnail_img) }}"
                                                        onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                        alt="{{ __($customer_product->name) }}">
                                                </a>
                                            </div>

                                            <div class="p-sm-3 p-2">
                                                <div class="price-box">
                                                    <span
                                                        class="product-price strong-600">{{ single_price($customer_product->unit_price) }}</span>
                                                </div>
                                                <h2 class="product-title p-0 text-truncate-1">
                                                    <a href="{{ route('customer.product', $customer_product->slug) }}">{{ __($customer_product->name) }}</a>
                                                </h2>
                                                <div>
                                                    @if($customer_product->conditon == 'new')
                                                        <span
                                                            class="product-label label-hot">{{translate('new')}}</span>
                                                    @elseif($customer_product->conditon == 'used')
                                                        <span
                                                            class="product-label label-hot">{{translate('Used')}}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endif

    <div class="mb-4">
        <div class="container">
            <div class="row gutters-10">
                @foreach (\App\Banner::where('position', 2)->where('published', 1)->get() as $key => $banner)
                    <div class="col-lg-{{ 12/count(\App\Banner::where('position', 2)->where('published', 1)->get()) }}">
                        <div class="media-banner mb-3 mb-lg-0">
                            <a href="{{ $banner->url }}" target="_blank" class="banner-container">
                                <img src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}"
                                     data-src="{{ my_asset($banner->photo) }}" alt="{{ env('APP_NAME') }} promo"
                                     class="img-fluid lazyload">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="section_best_sellers">

    </div>

    {{--    <section class="mb-3">--}}
    {{--        <div class="container">--}}
    {{--            <div class="row gutters-10">--}}
    {{--                <div class="col-lg-6">--}}
    {{--                    <div class="section-title-1 clearfix">--}}
    {{--                        <h3 class="heading-5 strong-700 mb-0 float-left">--}}
    {{--                            <span class="mr-4">{{translate('Top 10 Catogories')}}</span>--}}
    {{--                        </h3>--}}
    {{--                        <ul class="float-right inline-links">--}}
    {{--                            <li>--}}
    {{--                                <a href="{{ route('categories.all') }}" class="active">{{translate('View All Catogories')}}</a>--}}
    {{--                            </li>--}}
    {{--                        </ul>--}}
    {{--                    </div>--}}
    {{--                    <div class="row gutters-5">--}}
    {{--                        @foreach (\App\Category::where('top', 1)->get() as $category)--}}
    {{--                            <div class="mb-3 col-6">--}}
    {{--                                <a href="{{ route('products.category', $category->slug) }}" class="bg-white border d-block c-base-2 box-2 icon-anim pl-2">--}}
    {{--                                    <div class="row align-items-center no-gutters">--}}
    {{--                                        <div class="col-3 text-center">--}}
    {{--                                            <img src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($category->banner) }}" alt="{{ __($category->name) }}" class="img-fluid img lazyload">--}}
    {{--                                        </div>--}}
    {{--                                        <div class="info col-7">--}}
    {{--                                            <div class="name text-truncate pl-3 py-4">{{ __($category->name) }}</div>--}}
    {{--                                        </div>--}}
    {{--                                        <div class="col-2 text-center">--}}
    {{--                                            <i class="la la-angle-right c-base-1"></i>--}}
    {{--                                        </div>--}}
    {{--                                    </div>--}}
    {{--                                </a>--}}
    {{--                            </div>--}}
    {{--                        @endforeach--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--                <div class="col-lg-6">--}}
    {{--                    <div class="section-title-1 clearfix">--}}
    {{--                        <h3 class="heading-5 strong-700 mb-0 float-left">--}}
    {{--                            <span class="mr-4">{{translate('Top 10 Brands')}}</span>--}}
    {{--                        </h3>--}}
    {{--                        <ul class="float-right inline-links">--}}
    {{--                            <li>--}}
    {{--                                <a href="{{ route('brands.all') }}" class="active">{{translate('View All Brands')}}</a>--}}
    {{--                            </li>--}}
    {{--                        </ul>--}}
    {{--                    </div>--}}
    {{--                    <div class="row gutters-5">--}}
    {{--                        @foreach (\App\Brand::where('top', 1)->get() as $brand)--}}
    {{--                            <div class="mb-3 col-6">--}}
    {{--                                <a href="{{ route('products.brand', $brand->slug) }}" class="bg-white border d-block c-base-2 box-2 icon-anim pl-2">--}}
    {{--                                    <div class="row align-items-center no-gutters">--}}
    {{--                                        <div class="col-3 text-center">--}}
    {{--                                            <img src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($brand->logo) }}" alt="{{ __($brand->name) }}" class="img-fluid img lazyload">--}}
    {{--                                        </div>--}}
    {{--                                        <div class="info col-7">--}}
    {{--                                            <div class="name text-truncate pl-3 py-4">{{ __($brand->name) }}</div>--}}
    {{--                                        </div>--}}
    {{--                                        <div class="col-2 text-center">--}}
    {{--                                            <i class="la la-angle-right c-base-1"></i>--}}
    {{--                                        </div>--}}
    {{--                                    </div>--}}
    {{--                                </a>--}}
    {{--                            </div>--}}
    {{--                        @endforeach--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </section>--}}
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $.post('{{ route('home.section.featured') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                $('#section_featured').html(data);
                slickInit();
            });

            $.post('{{ route('home.section.best_selling') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                $('#section_best_selling').html(data);
                slickInit();
            });

            $.post('{{ route('home.section.home_categories') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                $('#section_home_categories').html(data);
                slickInit();
            });

            $.post('{{ route('home.section.best_sellers') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                $('#section_best_sellers').html(data);
                slickInit();
            });

            $.post('{{ route('home.section.sponsored_products') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                $('#section_sponsored_product').html(data);
            });
        });
    </script>
@endsection
