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
                                <div class="col-md-6 col-12">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{ translate('Wishlist')}}
                                    </h2>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{ translate('Home')}}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{ translate('Dashboard')}}</a></li>
                                            <li class="active"><a href="{{ route('wishlists.index') }}">{{ translate('Wishlist')}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wishlist items -->

                        <div class="row shop-default-wrapper shop-cards-wrapper shop-tech-wrapper mt-4">
                            @if(count($wishlists) > 0)
                            @foreach ($wishlists as $key => $wishlist)
                                @if ($wishlist->product != null)
                                    <div class="col-xl-4 col-6" id="wishlist_{{ $wishlist->id }}">
                                        <div class="card card-product mb-3 product-card-2">
                                            <div class="card-body p-3">
                                                <div class="card-image">
                                                    <a href="{{ route('product', $wishlist->product->slug) }}" class="d-flex">
                                                        <img
                                                            class="img-fit mx-auto lazyload"
                                                            src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                            data-src="{{my_asset($wishlist->product->thumbnail_img)}}"
                                                            onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                            alt="{{$wishlist->product->name}}">
                                                    </a>
                                                </div>

                                                <h2 class="heading heading-6 strong-600 mt-2 text-truncate-2">
                                                    <a href="{{ route('product', $wishlist->product->slug) }}">{{ $wishlist->product->name }}</a>
                                                </h2>
                                                <div class="star-rating star-rating-sm mb-1">
                                                    {{ renderStarRating($wishlist->product->rating) }}
                                                </div>
                                                <div class="mt-2">
                                                    <div class="price-box">
                                                        @if(home_base_price($wishlist->product->id) != home_discounted_base_price($wishlist->product->id))
                                                            <del class="old-product-price strong-400">{{ home_base_price($wishlist->product->id) }}</del>
                                                        @endif
                                                        <span class="product-price strong-600">{{ home_discounted_base_price($wishlist->product->id) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer p-3">
                                                <div class="product-buttons">
                                                    <div class="row align-items-center">
                                                        <div class="col-2">
                                                            <a href="#" class="link link--style-3" data-toggle="tooltip" data-placement="top" title="Remove from wishlist" onclick="removeFromWishlist({{ $wishlist->id }})">
                                                                <i class="la la-close"></i>
                                                            </a>
                                                        </div>
                                                        <div class="col-10">
                                                            <button type="button" class="btn btn-block btn-base-1 btn-circle btn-icon-left" onclick="showAddToCartModal({{ $wishlist->product->id }})">
                                                                <i class="la la-shopping-cart mr-2"></i>{{ translate('Add to cart')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            @else
                                <div class="col-12 card no-border text-center">
                                    <i class="la la-meh-o d-block heading-1 alpha-5" style="font-size: 80px!important;"></i>
                                    <span class="h4" style="font-size: 20px; color: #818a91">{{__('There is no wishlists')}}</span>
                                </div>
                            @endif
                        </div>

                        <div class="pagination-wrapper py-4">
                            <ul class="pagination justify-content-end">
                                {{ $wishlists->links() }}
                            </ul>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="addToCart" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
                <button type="button" class="close absolute-close-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div id="addToCart-modal-body">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        function removeFromWishlist(id){
            $.post('{{ route('wishlists.remove') }}',{_token:'{{ csrf_token() }}', id:id}, function(data){
                $('#wishlist').html(data);
                $('#wishlist_'+id).hide();
                showFrontendAlert('success', 'Item has been renoved from wishlist');
            })
        }
    </script>
@endsection
