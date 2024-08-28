<section class="">
    <div class="container p-0">
        <div class="py-4 p-md-4 bg-white shadow-sm">
            <div class="section-title-1 clearfix">
                <h3 class="heading-5 strong-700 mb-0 float-left">
                    <span class="mr-4">{{ translate('Featured Products')}}</span>
                </h3>
            </div>
            <div class="caorusel-box arrow-round gutters-5">
                <div class="slick-carousel" data-slick-items="5" data-slick-xl-items="4" data-slick-lg-items="3"  data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
                    @foreach (filter_products(\App\Product::where('published', 1)->where('featured', '1'))->limit(12)->get() as $key => $product)
                    <div class="caorusel-card">
                        <div class="product-card-2 card card-product shop-cards shop-tech">
                            <div class="card-body p-0">

                                <div class="card-image">
                                    <a href="{{ route('product', $product->slug) }}" class="d-block">
                                        @php
                                            $img = '';
                                            if($product->thumbnail_img) {
                                                $img = explode('/',$product->thumbnail_img)[2];
                                            }
                                        @endphp
                                        <img
                                            class="img-fit lazyload mx-auto"
                                            src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                            data-src="{{ my_asset('thumbnails/'.$img) }}"
                                            onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                            alt="{{ __($product->name) }}">
                                    </a>
                                </div>

                                <div class="p-md-3 p-2">
                                    <div class="price-box">
                                        @if(home_base_price($product->id) != home_discounted_base_price($product->id))
                                            <del class="old-product-price strong-400">{{ home_base_price($product->id) }}</del>
                                        @endif
                                        <span class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                    </div>
                                    <div class="star-rating star-rating-sm mt-1">
                                        {{ renderStarRating($product->rating) }}
                                    </div>
                                    <h2 class="product-title p-0">
                                        <a href="{{ route('product', $product->slug) }}" class="text-truncate">{{ __($product->name) }}</a>
                                    </h2>

                                    @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                        <div class="club-point mt-2 bg-soft-base-1 border-light-base-1 border">
                                            {{ translate('Club Point') }}:
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
</section>
