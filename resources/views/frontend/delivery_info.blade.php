@extends('frontend.layouts.app')

@section('content')

    <div id="page-content">
        <section class="slice-xs sct-color-2 border-bottom">
            <div class="container container-sm">
                <div class="row cols-delimited justify-content-center">
                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center ">
                            <div class="block-icon c-gray-light mb-0">
                                <i class="la la-shopping-cart"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ translate('1. My Cart')}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center active">
                            <div class="block-icon mb-0">
                                <i class="la la-truck"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ translate('2. Delivery info')}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center ">
                            <div class="block-icon mb-0 c-gray-light">
                                <i class="la la-map-o"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ translate('3. Shipping info')}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center">
                            <div class="block-icon c-gray-light mb-0">
                                <i class="la la-credit-card"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ translate('4. Payment')}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center">
                            <div class="block-icon c-gray-light mb-0">
                                <i class="la la-check-circle"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">5. {{ translate('Confirmation')}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-4 gry-bg">
            <div class="container">
                <div class="row cols-xs-space cols-sm-space cols-md-space">
                    <div class="col-xl-8">
                        <form class="form-default" data-toggle="validator" action="{{ route('checkout.store_delivery_info') }}" role="form" method="POST">
                            @csrf
                            @php
                                $admin_products = array();
                                $seller_products = array();
                                $status = false;
                                $check_admin = false;
                                $check_seller = false;
                                $multiseller = array();
                                foreach (Session::get('cart') as $key => $cartItem){
                                    $product = \App\Product::find($cartItem['id']);
                                    if($product->added_by == 'admin'){
                                        array_push($admin_products, $cartItem['id']);
                                        $check_admin = true;
                                    }
                                    else{
                                        $product_ids = array();
                                        if(array_key_exists($product->user_id, $seller_products)){
                                            $product_ids = $seller_products[$product->user_id];
                                        }
                                        array_push($product_ids, $cartItem['id']);
                                        $check_seller = true;
                                        if (!in_array($product->user_id, $multiseller)){
                                            array_push($multiseller, $product->user_id);
                                        }
                                        $seller_products[$product->user_id] = $product_ids;
                                    }
                                }

                                if ($check_admin && $check_seller) {
                                    $status = true;
                                }elseif (count($multiseller) > 1) {
                                    $status = true;
                                } else $status  = false;
                            @endphp

                            @if (!empty($admin_products))
                            <div class="card mb-3">
                                <div class="card-header bg-white py-3">
                                    <h5 class="heading-6 mb-0">{{ \App\GeneralSetting::first()->site_name }} {{translate('Products')}}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table-cart">
                                                <tbody>
                                                    @foreach ($admin_products as $key => $id)
                                                    <tr class="cart-item">
                                                        <td class="product-image" width="25%">
                                                            <a href="{{ route('product', \App\Product::find($id)->slug) }}" target="_blank">
                                                                <img
                                                                    loading="lazy"
                                                                    class="lazyload img-fit"
                                                                    src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                                    data-src="{{ my_asset(\App\Product::find($id)->thumbnail_img) }}"
                                                                    onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                                >
                                                            </a>
                                                        </td>
                                                        <td class="product-name strong-600">
                                                            <a href="{{ route('product', \App\Product::find($id)->slug) }}" target="_blank" class="d-block c-base-2">
                                                                {{ \App\Product::find($id)->name }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer">
                                                        <input type="radio" name="shipping_type_admin" value="home_delivery" checked class="d-none" onchange="show_pickup_point(this)" data-target=".pickup_point_id_admin">
                                                        <span class="radio-box"></span>
                                                        <span class="d-block ml-2 strong-600">
                                                            {{  translate('Home Delivery') }}
                                                        </span>
                                                    </label>
                                                </div>
                                                @if (\App\BusinessSetting::where('type', 'pickup_point')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer">
                                                            <input type="radio" name="shipping_type_admin" value="pickup_point" class="d-none" onchange="show_pickup_point(this)" data-target=".pickup_point_id_admin">
                                                            <span class="radio-box"></span>
                                                            <span class="d-block ml-2 strong-600">
                                                                {{  translate('Local Pickup') }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>

                                            @if (\App\BusinessSetting::where('type', 'pickup_point')->first()->value == 1)
                                                <div class="mt-3 pickup_point_id_admin d-none">
                                                    <select class="pickup-select form-control-lg w-100" name="pickup_point_id_admin" data-placeholder="{{ translate('Select a pickup point') }}">
                                                            <option>{{ translate('Select your nearest pickup point')}}</option>
                                                        @foreach (\App\PickupPoint::where('pick_up_status',1)->get() as $key => $pick_up_point)
                                                            <option value="{{ $pick_up_point->id }}" data-address="{{ $pick_up_point->address }}" data-phone="{{ $pick_up_point->phone }}">
                                                                {{ $pick_up_point->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    @if($status)
                                        <br>
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="heading-light heading-6" style="font-size: .9rem!important;">{{translate('Select Type Shipping Delivery')}}</h6>
                                            </div>
                                            <div class="col-6">
                                                <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer bg-light-base-1">
                                                    <input type="radio" name="shipping_type_delivery_admin" value="direct_delivery" required checked class="d-none">
                                                    <span class="radio-box"></span>
                                                    <span class="d-block ml-2 strong-600">
                                                            {{  translate('Direct Delivery') }}
                                                        </span>
                                                </label>
                                                <span class="text-danger">{{ __('Hint: Your order will arrive within 1 to 3 days.') }}</span>
                                            </div>
                                            <div class="col-6">
                                                <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer bg-light-base-1">
                                                    <input type="radio" name="shipping_type_delivery_admin" value="collective_delivery" required class="d-none">
                                                    <span class="radio-box"></span>
                                                    <span class="d-block ml-2 strong-600">
                                                            {{  translate('Collective Delivery') }}
                                                        </span>
                                                </label>
                                                <span class="text-danger">{{ __('Hint: Your order will arrive within 7 to 10 days.') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @else
                                <div class="card mb-3">
                                    <div class="card-header bg-white py-3">
                                        <h5 class="heading-6 mb-0">{{ \App\GeneralSetting::first()->site_name }} {{translate('Products')}}</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($status)
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6 class="heading-light heading-6" style="font-size: .9rem!important;">{{translate('Select Type Shipping Delivery for stores that deal with '). \App\GeneralSetting::first()->site_name }}</h6>
                                                </div>
                                                <div class="col-6">
                                                    <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer bg-light-base-1">
                                                        <input type="radio" name="shipping_type_delivery_admin" value="direct_delivery" required checked class="d-none">
                                                        <span class="radio-box"></span>
                                                        <span class="d-block ml-2 strong-600">
                                                            {{  translate('Direct Delivery') }}
                                                        </span>
                                                    </label>
                                                    <span class="text-danger">{{ __('Hint: Your order will arrive within 1 to 3 days.') }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer bg-light-base-1">
                                                        <input type="radio" name="shipping_type_delivery_admin" value="collective_delivery" required class="d-none">
                                                        <span class="radio-box"></span>
                                                        <span class="d-block ml-2 strong-600">
                                                            {{  translate('Collective Delivery') }}
                                                        </span>
                                                    </label>
                                                    <span class="text-danger">{{ __('Hint: Your order will arrive within 7 to 10 days.') }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if (!empty($seller_products))
                                @foreach ($seller_products as $key => $seller_product)
                                    @php
                                    $shop_seller = \App\Shop::where('user_id', $key)->first();
                                    @endphp
                                    <div class="card mb-3">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="heading-6 mb-0">{{ $shop_seller->name }} Products</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row no-gutters">
                                                <div class="col-md-6">
                                                    <table class="table-cart">
                                                        <tbody>
                                                            @foreach ($seller_product as $id)
                                                            <tr class="cart-item">
                                                                <td class="product-image" width="25%">
                                                                    <a href="{{ route('product', \App\Product::find($id)->slug) }}" target="_blank">
                                                                        <img
                                                                            loading="lazy"
                                                                            class="lazyload img-fit"
                                                                            src="{{ my_asset('frontend/images/placeholder.gif') }}"
                                                                            data-src="{{ my_asset(\App\Product::find($id)->thumbnail_img) }}"
                                                                            onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                                        >
                                                                    </a>
                                                                </td>
                                                                <td class="product-name strong-600">
                                                                    <a href="{{ route('product', \App\Product::find($id)->slug) }}" target="_blank" class="d-block c-base-2">
                                                                        {{ \App\Product::find($id)->name }}
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer">
                                                                <input type="radio" name="shipping_type_{{ $key }}" value="home_delivery" checked class="d-none" onchange="show_pickup_point(this)" data-target=".pickup_point_id_{{ $key }}">
                                                                <span class="radio-box"></span>
                                                                <span class="d-block ml-2 strong-600">
                                                                    {{  translate('Home Delivery') }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                        @if (\App\BusinessSetting::where('type', 'pickup_point')->first()->value == 1)
                                                            @if (is_array(json_decode(\App\Shop::where('user_id', $key)->first()->pick_up_point_id)))
                                                                <div class="col-6">
                                                                    <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer">
                                                                        <input type="radio" name="shipping_type_{{ $key }}" value="pickup_point" class="d-none" onchange="show_pickup_point(this)" data-target=".pickup_point_id_{{ $key }}">
                                                                        <span class="radio-box"></span>
                                                                        <span class="d-block ml-2 strong-600">
                                                                            {{  translate('Local Pickup') }}
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>

                                                    @if (\App\BusinessSetting::where('type', 'pickup_point')->first()->value == 1)
                                                        @if (is_array(json_decode(\App\Shop::where('user_id', $key)->first()->pick_up_point_id)))
                                                            <div class="mt-3 pickup_point_id_{{ $key }} d-none">
                                                                <select class="pickup-select form-control-lg w-100" name="pickup_point_id_{{ $key }}" data-placeholder="{{ translate('Select a pickup point') }}">
                                                                    <option>{{ translate('Select your nearest pickup point')}}</option>
                                                                    @foreach (json_decode(\App\Shop::where('user_id', $key)->first()->pick_up_point_id) as $pick_up_point)
                                                                        @if (\App\PickupPoint::find($pick_up_point) != null)
                                                                            <option value="{{ \App\PickupPoint::find($pick_up_point)->id }}" data-address="{{ \App\PickupPoint::find($pick_up_point)->address }}" data-phone="{{ \App\PickupPoint::find($pick_up_point)->phone }}">
                                                                                {{ \App\PickupPoint::find($pick_up_point)->name }}
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            @if($status && $shop_seller->deal_with == 0 && $shop_seller->collective_delivery == 1)
                                            <br>
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6 class="heading-light heading-6" style="font-size: .9rem!important;">{{translate('Select Type Shipping Delivery')}}</h6>
                                                </div>
                                                <div class="col-6">
                                                    <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer bg-light-base-1">
                                                        <input type="radio" name="shipping_type_delivery_{{$key}}" value="direct_delivery" required checked class="d-none">
                                                        <span class="radio-box"></span>
                                                        <span class="d-block ml-2 strong-600">
                                                            {{  translate('Direct Delivery') }}
                                                        </span>
                                                    </label>
                                                    <span class="text-danger">{{ __('Hint: Your order will arrive within 1 to 3 days.') }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer bg-light-base-1">
                                                        <input type="radio" name="shipping_type_delivery_{{$key}}" value="collective_delivery" required class="d-none">
                                                        <span class="radio-box"></span>
                                                        <span class="d-block ml-2 strong-600">
                                                            {{  translate('Collective Delivery') }}
                                                        </span>
                                                    </label>
                                                    <span class="text-danger">{{ __('Hint: Your order will arrive within 7 to 10 days.') }}</span>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            <div class="row align-items-center pt-4">
                                <div class="col-md-6">
                                    <a href="{{ route('home') }}" class="link link--style-3">
                                        <i class="ion-android-arrow-back"></i>
                                        {{ translate('Return to shop')}}
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-styled btn-base-1">{{ translate('Continue to Shipping')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-4 ml-lg-auto">
                        @include('frontend.partials.cart_summary')
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        function display_option(key){

        }
        function show_pickup_point(el) {
        	var value = $(el).val();
        	var target = $(el).data('target');

            console.log(value);

        	if(value == 'home_delivery'){
                if(!$(target).hasClass('d-none')){
                    $(target).addClass('d-none');
                }
        	}else{
        		$(target).removeClass('d-none');
        	}
        }

    </script>
@endsection
