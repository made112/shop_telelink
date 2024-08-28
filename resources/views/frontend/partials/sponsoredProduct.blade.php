@if (\App\BusinessSetting::where('type', 'sponsored_product')->first()->value == 1)
    @php
    $sponsored_product = \App\SponsoredProduct::where('featured', 1)->where('status', 1)->first();
    @endphp
    @if($sponsored_product != null && strtotime(date('d-m-Y')) >= $sponsored_product->start_date && strtotime(date('d-m-Y')) <= $sponsored_product->end_date)
        @php
            $product_details = \App\Product::findOrFail($sponsored_product->product_id);
        @endphp
        @if($product_details)
        <section class="my-4 sponsored_product">
        <div class="container">
            <div class="bg-white shadow-sm">
                <div class="row no-gutters position-relative">
                    <div class="col-12 col-md-6" style="height:230px;background-image: url({{my_asset('frontend/images/flash-deals.png')}});background-position: center center;background-size: cover;">
                        <div class="clearfix px-4 py-2">
                            <h3 class="heading-5 strong-700 mb-0">
                                <span class="type">{{translate('Sponsored')}}</span>
                            </h3>
                            <div class="sponsored_content">
                                <p class="title">{{$sponsored_product->title}}</p>
                                <p class="description">{{$sponsored_product->sub_title}}</p>
                                <p class="subdescription">{{translate('Includes free shipping')}}</p>
                                <a href="{{ route('product', $product_details->slug) }}" target="_blank" class="shop_now">
                                    <span>{{translate('Shop Now')}}</span>
                                    <span class="la la-arrow-right"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="{{ route('product', $product_details->slug) }}" target="_blank">
                            <img
                                src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                data-src="{{ my_asset($sponsored_product->banner) }}"
                                onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload sponsored_img">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
        @endif
    @endif
@endif
