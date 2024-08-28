@extends('frontend.layouts.app')

@section('meta_title'){{ $shop->site_name }}@stop

@section('meta_description'){{ $shop->description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $shop->site_name }}">
    <meta itemprop="description" content="{{ $shop->description }}">
    <meta itemprop="image" content="{{ my_asset($shop->logo) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="website">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $shop->site_name }}">
    <meta name="twitter:description" content="{{ $shop->description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ my_asset($shop->logo) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $shop->site_name }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('shop.uno') }}" />
    <meta property="og:image" content="{{ my_asset($shop->logo) }}" />
    <meta property="og:description" content="{{ $shop->description }}" />
    <meta property="og:site_name" content="{{ $shop->site_name }}" />
@endsection

@section('content')
    <!-- <section>
        <img loading="lazy"  src="https://via.placeholder.com/2000x300.jpg" alt="" class="img-fluid">
    </section> -->

    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.4&appId=241110544128";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
    @php
        $total = 0;
        $rating = 0;
        $products = \App\Product::where('added_by', 'admin')->get();
        foreach ($products as $key => $seller_product) {
            $total += $seller_product->reviews->count();
            $rating += $seller_product->reviews->sum('rating');
        }

        $points = 0;
        foreach (\App\ClubPoint::where('user_id', $products[0]->id)->get('points') as $club) {
            $points += intval($club->points);
        }
    @endphp

    <section class="gry-bg pt-4 ">
        <div class="container">
            <div class="row align-items-baseline">
                <div class="col-md-6">
                    <div class="d-flex">
                        <img
                            height="70"
                            class="lazyload"
                            src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                            data-src="@if ($shop->logo !== null) {{ my_asset($shop->logo) }} @else {{ my_asset('frontend/images/placeholder.jpg') }} @endif"
                            alt="{{ $shop->site_name }}"
                        >
                        <div class="pl-4">
                            <h3 class="strong-700 heading-4 mb-0">{{ translate('UNO') }}
                                <span class="ml-2"><i class="fa fa-check-circle" style="color:green"></i></span>
                            </h3>
                            <div class="star-rating star-rating-sm mb-1">
                                @if ($total > 0)
                                    {{ renderStarRating($rating/$total) }}
                                @else
                                    {{ renderStarRating(0) }}
                                @endif
                            </div>
                            <div class="text-dark strong-500 heading-light">
                                {{ translate(convert_points_to_levels($points)) }}
                            </div>
                            <div class="location alpha-6" style="margin-bottom: 5px">{{ $shop->address }}</div>
                            <div class="fb-share-button" data-href="{{ route('shop.uno') }}" data-layout="button_count"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="text-md-right mt-4 mt-md-0 social-nav model-2">
                        @if ($shop->facebook != null)
                            <li>
                                <a href="{{ $shop->facebook }}" class="facebook social_a" target="_blank" data-toggle="tooltip" data-original-title="Facebook">
                                    <i class="fa fa-facebook"></i>
                                </a>
                            </li>
                        @endif
                        @if ($shop->twitter != null)
                            <li>
                                <a href="{{ $shop->twitter }}" class="twitter social_a" target="_blank" data-toggle="tooltip" data-original-title="Twitter">
                                    <i class="fa fa-twitter"></i>
                                </a>
                            </li>
                        @endif
                        @if ($shop->google != null)
                            <li>
                                <a href="{{ $shop->google }}" class="google-plus social_a" target="_blank" data-toggle="tooltip" data-original-title="Google">
                                    <i class="fa fa-google-plus"></i>
                                </a>
                            </li>
                        @endif
                        @if ($shop->youtube != null)
                            <li>
                                <a href="{{ $shop->youtube }}" class="youtube social_a" target="_blank" data-toggle="tooltip" data-original-title="Youtube">
                                    <i class="fa fa-youtube"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-white">
        <div class="container">
            <div class="row sticky-top mt-4">
                <div class="col">
                    <div class="seller-shop-menu">
                        <ul class="inline-links">
                            <li @if(!isset($type)) class="active" @endif><a href="{{ route('shop.uno') }}">{{ translate('Store Home')}}</a></li>
                            <li @if(isset($type) && $type == 'top_selling') class="active" @endif><a href="{{ route('shop.uno.type', ['type'=>'top_selling']) }}">{{ translate('Top Selling')}}</a></li>
                            <li @if(isset($type) && $type == 'all_products') class="active" @endif><a href="{{ route('shop.uno.type', ['type'=>'all_products']) }}">{{ translate('All Products')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (!isset($type))
        <section class="py-4">
            <div class="container">
                <div class="home-slide">
                    <div class="slick-carousel" data-slick-arrows="true" data-slick-dots="true">
                        @if ($shop->sliders != null)
                            @foreach (json_decode($shop->sliders) as $key => $slide)
                                <div class="">
                                    <img class="d-block w-100 lazyload" src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}" data-src="{{ my_asset($slide) }}" alt="{{ $key }} slide" style="max-height:300px;">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </section>
        <section class="sct-color-1 pt-5 pb-4">
            <div class="container">
                <div class="section-title section-title--style-1 text-center mb-4">
                    <h3 class="section-title-inner heading-3 strong-600">
                        {{ translate('Featured Products')}}
                    </h3>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="caorusel-box arrow-round gutters-15">
                            <div class="slick-carousel center-mode" data-slick-items="5" data-slick-lg-items="3"  data-slick-md-items="3" data-slick-sm-items="1" data-slick-xs-items="1">
                                @foreach ($products->where('published', 1)->where('featured', 1) as $key => $product)
                                    <div class="caorusel-card my-5">
                                        <div class="product-card-2 card card-product shop-cards shop-tech">
                                            <div class="card-body p-0">

                                                <div class="card-image">
                                                    <a href="{{ route('product', $product->slug) }}" class="d-block">
                                                        <img  class="mx-auto img-fit lazyload" src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($product->thumbnail_img) }}" alt="{{  __($product->name) }}">
                                                    </a>
                                                </div>

                                                <div class="p-3">
                                                    <div class="price-box">
                                                        <del class="old-product-price strong-400">{{ home_base_price($product->id) }}</del>
                                                        <span class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                                    </div>
                                                    <div class="star-rating star-rating-sm mt-1">
                                                        {{ renderStarRating($product->rating) }}
                                                    </div>
                                                    <h2 class="product-title p-0 text-truncate">
                                                        <a href="{{ route('product', $product->slug) }}">{{  __($product->name) }}</a>
                                                    </h2>
                                                    @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                                        <div class="club-point mt-2 bg-soft-base-1 border-light-base-1 border">
                                                            {{  translate('Club Point') }}:
                                                            <span class="strong-700 float-right">{{ $product->earn_point }}</span>
                                                        </div>
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
            </div>
        </section>
    @endif


    <section class="@if (!isset($type)) gry-bg @endif pt-5">
        <div class="container">
            <h4 class="heading-5 strong-600 border-bottom pb-3 mb-4">
                @if (!isset($type))
                    {{ translate('New Arrival Products')}}
                @elseif ($type == 'top_selling')
                    {{ translate('Top Selling')}}
                @elseif ($type == 'all_products')
                    {{ translate('All Products')}}
                @endif
            </h4>
            <div class="product-list row gutters-5 sm-no-gutters">
                @php
                    if (!isset($type)){
                        $products = \App\Product::where('added_by', 'admin')->where('published', 1)->orderBy('created_at', 'desc')->paginate(24);
                    }
                    elseif ($type == 'top_selling'){
                        $products = \App\Product::where('added_by', 'admin')->where('published', 1)->orderBy('num_of_sale', 'desc')->paginate(24);
                    }
                    elseif ($type == 'all_products'){
                        $products = \App\Product::where('added_by', 'admin')->where('published', 1)->paginate(24);
                    }
                @endphp
                @foreach ($products as $key => $product)
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6">
                        <div class="card product-box-1 mb-3">
                            <div class="card-image">
                                <a href="{{ route('product', $product->slug) }}" class="d-block text-center">
                                    <img class="img-fit lazyload" src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($product->thumbnail_img) }}" alt="{{  __($product->name) }}">
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <div class="px-3 py-2">
                                    @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                        <div class="club-point mb-2 bg-soft-base-1 border-light-base-1 border">
                                            {{  translate('Club Point') }}:
                                            <span class="strong-700 float-right">{{ $product->earn_point }}</span>
                                        </div>
                                    @endif
                                    <h2 class="title mb-0">
                                        <a class="text-truncate" href="{{ route('product', $product->slug) }}">{{  __($product->name) }}</a>
                                    </h2>
                                </div>
                                <div class="price-bar row no-gutters">
                                    <div class="price col-md-7">
                                        @if(home_price($product->id) != home_discounted_price($product->id))
                                            <del class="old-product-price strong-600">{{ home_base_price($product->id) }}</del>
                                            <span class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                        @else
                                            <span class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-5">
                                        <div class="star-rating star-rating-sm float-md-right">
                                            {{ renderStarRating($product->rating) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="cart-add d-flex">
                                    <button class="btn add-wishlist border-right" title="Add to Wishlist" onclick="addToWishList({{ $product->id }})">
                                        <i class="la la-heart-o"></i>
                                    </button>
                                    <button class="btn add-compare border-right" title="Add to Compare" onclick="addToCompare({{ $product->id }})">
                                        <i class="la la-refresh"></i>
                                    </button>
                                    <button type="button" class="btn btn-block btn-icon-left" onclick="showAddToCartModal({{ $product->id }})">
                                        <span class="d-none d-sm-inline-block">{{ translate('Add to cart')}}</span><i class="la la-shopping-cart ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col">
                    <div class="products-pagination my-5">
                        <nav aria-label="Center aligned pagination">
                            <ul class="pagination justify-content-center">
                                {{ $products->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
