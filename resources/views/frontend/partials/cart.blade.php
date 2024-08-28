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
                                                    src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                    data-src="{{ my_asset($product->thumbnail_img) }}"
                                                    onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                    class="img-fluid lazyload" alt="{{ __($product->name) }}">
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
