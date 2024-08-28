@foreach (\App\HomeCategory::where('status', 1)->orderBy('order', 'asc')->get() as $key => $homeCategory)
    @if ($homeCategory->category != null)
        <section class="mb-4">
            <div class="container">
                <div class="px-2 py-4 p-md-4 bg-white shadow-sm">
                    <div class="section-title-1 clearfix">
                        <h3 class="heading-5 strong-700 mb-0 float-lg-left">
                            <a href="{{ route('products.category', $homeCategory->category->slug) }}">
                                <span class="mr-4">{{ translate($homeCategory->category->name) }}</span>
                            </a>
                        </h3>
                        @if(count($homeCategory->category->subcategories)>0)
                        <ul class="inline-links float-lg-right nav mt-3 mb-2 m-lg-0 d-none d-lg-block">
                            @foreach($homeCategory->category->subcategories->take(9) as $subcategory)
                                <li><a href="{{route('products.subcategory', $subcategory->slug)}}">{{$subcategory->name}}</a></li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    <div class="caorusel-box arrow-round gutters-5 d-flex parent-products">
                        <div class="bigproduct">
                        @foreach (filter_products(\App\Product::where('published', 1)->where('category_id', $homeCategory->category->id))->latest()->limit(7)->take(1)->get() as $key => $product)
                            <div class="caorusel-card" style="height: 98%;width: 100%;">
                                <div class="product-box-2 alt-box mt-2 custom-product">
                                    <div class="position-relative overflow-hidden">
                                        <a href="{{ route('product', $product->slug) }}" class="d-block product-image h-100 text-center">
                                            <img
                                                class="img-fit lazyload"
                                                src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                data-src="{{ my_asset($product->thumbnail_img) }}"
                                                onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                alt="{{ __($product->name) }}">
                                        </a>
                                    </div>
                                    <div class="p-md-4 p-2">
                                        <div class="price-box">
                                            <span class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                        </div>
                                        <h2 class="product-title p-0">
                                            <a href="{{ route('product', $product->slug) }}">{{ __($product->name) }}</a>
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
                        @endforeach
                        </div>
                        <div class="second-parent-product" >
                        @foreach (filter_products(\App\Product::where('published', 1)->where('category_id', $homeCategory->category->id))->latest()->limit(7)->skip(1)->take(6)->get() as $key => $product)
                                @php
                                    $img = '';
                                    if($product->thumbnail_img) {
                                        $img = explode('/',$product->thumbnail_img)[2];
                                    }
                                @endphp
                                @if($key > 3)
                                    <div class="other-theme-product caorusel-card my-1 w-50">
                                        <div class="row no-gutters product-box-2 align-items-center p-md-3">
                                            <div class="col-4">
                                                <div class="position-relative overflow-hidden h-100">
                                                    <a href="{{ route('product', $product->slug) }}" class="d-block product-image h-100">
                                                        <img
                                                            class="img-fit lazyload mx-auto"
                                                            src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                            data-src="{{ my_asset('thumbnails/'.$img) }}"
                                                            onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                            alt="{{ __($product->name) }}">
                                                    </a>
                                                    <div class="product-btns">
                                                        <button class="btn add-wishlist" title="Add to Wishlist" onclick="addToWishList({{ $product->id }})">
                                                            <i class="la la-heart-o"></i>
                                                        </button>
                                                        <button class="btn add-compare" title="Add to Compare" onclick="addToCompare({{ $product->id }})">
                                                            <i class="la la-refresh"></i>
                                                        </button>
                                                        <button class="btn quick-view" title="Quick view" onclick="showAddToCartModal({{ $product->id }})">
                                                            <i class="la la-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-8 border-left">
                                                <div class="p-3">
                                                    <h2 class="product-title mb-0 p-0 text-truncate">
                                                        <a href="{{ route('product', $product->slug) }}">{{ __($product->name) }}</a>
                                                    </h2>
                                                    <div class="star-rating star-rating-sm mb-2">
                                                        {{ renderStarRating($product->rating) }}
                                                    </div>
                                                    <div class="clearfix">
                                                        <div class="price-box float-left">
                                                            @if(home_base_price($product->id) != home_discounted_base_price($product->id))
                                                                <del class="old-product-price strong-400">{{ home_base_price($product->id) }}</del>
                                                            @endif
                                                            <span class="product-price strong-600">
                                                        {{ home_discounted_base_price($product->id) }}
                                                    </span>
                                                        </div>
                                                        <div class="float-right">
                                                            <button class="add-to-cart btn" title="Add to Cart" onclick="showAddToCartModal({{ $product->id }})">
                                                                <i class="la la-shopping-cart"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                <div class="caorusel-card" style="width: 25%;">
                                <div class="product-box-2 bg-white alt-box my-2">
                                    <div class="position-relative overflow-hidden">
                                        <a href="{{ route('product', $product->slug) }}" class="d-block product-image h-100 text-center">
                                            <img
                                                class="img-fit lazyload"
                                                src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                data-src="{{ my_asset('thumbnails/'.$img) }}"
                                                onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                alt="{{ __($product->name) }}">
                                        </a>
                                        <div class="product-btns clearfix">
                                            <button class="btn add-wishlist" title="Add to Wishlist" onclick="addToWishList({{ $product->id }})" tabindex="0">
                                                <i class="la la-heart-o"></i>
                                            </button>
                                            <button class="btn add-compare" title="Add to Compare" onclick="addToCompare({{ $product->id }})" tabindex="0">
                                                <i class="la la-refresh"></i>
                                            </button>
                                            <button class="btn quick-view" title="Quick view" onclick="showAddToCartModal({{ $product->id }})" tabindex="0">
                                                <i class="la la-eye"></i>
                                            </button>
                                        </div>
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
                                            <a href="{{ route('product', $product->slug) }}" class=" text-truncate">{{ __($product->name) }}</a>
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
                                @endif
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endforeach
