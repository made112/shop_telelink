@extends('frontend.layouts.app')

@section('content')

    @php
        $digit_status = isProductDigit();
    @endphp
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

                    @if(!$digit_status)
                        <div class="col">
                            <div class="icon-block icon-block--style-1-v5 text-center ">
                                <div class="block-icon mb-0 c-gray-light">
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
                    @endif

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center active">
                            <div class="block-icon mb-0">
                                <i class="la la-credit-card"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ $digit_status ? translate('2. Payment') : translate('4. Payment')}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center">
                            <div class="block-icon c-gray-light mb-0">
                                <i class="la la-check-circle"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ $digit_status ? translate('3. Confirmation') : translate('5. Confirmation')}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-3 gry-bg">
            <div class="container">
                <div class="row cols-xs-space cols-sm-space cols-md-space">
                    <div class="col-lg-8">
                        <form action="{{ route('payment.checkout') }}" class="form-default" data-toggle="validator"
                              role="form" method="POST" id="checkout-form">
                            @csrf
                            <div class="card">
                                <div class="card-title px-4 py-3">
                                    <h3 class="heading heading-5 strong-500">
                                        {{ translate('Select a payment option')}}
                                    </h3>
                                </div>
                                <div class="card-body text-center">
                                    <div class="row">
                                        <div class="col-md-6 mx-auto">
                                            <div class="row">
                                                @if(\App\BusinessSetting::where('type', 'paypal_payment')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4" data-toggle="tooltip"
                                                               data-title="Paypal">
                                                            <input type="radio" id="" name="payment_option"
                                                                   value="paypal" checked>
                                                            <span>
                                                                <img loading="lazy"
                                                                     src="{{ my_asset('frontend/images/icons/cards/paypal.png')}}"
                                                                     class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'visa_payment')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4 w-100"
                                                               style="height: 111.31px;" data-toggle="tooltip"
                                                               data-title="Visa">
                                                            <input type="radio" id="" name="payment_option" value="visa"
                                                                   checked>
                                                            <span class="h-100">
                                                                <img loading="lazy"
                                                                     style="object-fit: contain;width: 100%;height: 100%;"
                                                                     src="{{ my_asset('frontend/images/icons/cards/visa2.png')}}"
                                                                     class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'jawwal_pay_payment')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4 w-100"
                                                               style="height: 111.31px;" data-toggle="tooltip"
                                                               data-title="Jawwal Pay">
                                                            <input type="radio" id="" name="payment_option"
                                                                   value="jawwal_pay">
                                                            <span class="h-100">
                                                                <img loading="lazy"
                                                                     style="object-fit: contain;width: 100%;height: 100%;"
                                                                     src="{{ my_asset('frontend/images/icons/cards/jawwal_pay.png')}}"
                                                                     class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'stripe_payment')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4" data-toggle="tooltip"
                                                               data-title="Stripe">
                                                            <input type="radio" id="" name="payment_option"
                                                                   value="stripe">
                                                            <span>
                                                                <img loading="lazy"
                                                                     src="{{ my_asset('frontend/images/icons/cards/stripe.png')}}"
                                                                     class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'sslcommerz_payment')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4" data-toggle="tooltip"
                                                               data-title="sslcommerz">
                                                            <input type="radio" id="" name="payment_option"
                                                                   value="sslcommerz">
                                                            <span>
                                                                <img loading="lazy"
                                                                     src="{{ my_asset('frontend/images/icons/cards/sslcommerz.png')}}"
                                                                     class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'instamojo_payment')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4" data-toggle="tooltip"
                                                               data-title="Instamojo">
                                                            <input type="radio" id="" name="payment_option"
                                                                   value="instamojo">
                                                            <span>
                                                                <img loading="lazy"
                                                                     src="{{ my_asset('frontend/images/icons/cards/instamojo.png')}}"
                                                                     class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'razorpay')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4" data-toggle="tooltip"
                                                               data-title="Razorpay">
                                                            <input type="radio" id="" name="payment_option"
                                                                   value="razorpay">
                                                            <span>
                                                                <img loading="lazy"
                                                                     src="{{ my_asset('frontend/images/icons/cards/rozarpay.png')}}"
                                                                     class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'voguepay')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4" data-toggle="tooltip"
                                                               data-title="VoguePay">
                                                            <input type="radio" id="" name="payment_option"
                                                                   value="voguepay">
                                                            <span>
                                                                <img loading="lazy"
                                                                     src="{{ my_asset('frontend/images/icons/cards/vogue.png')}}"
                                                                     class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'payhere')->first()->value == 1)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4" data-toggle="tooltip"
                                                               data-title="payhere">
                                                            <input type="radio" id="" name="payment_option"
                                                                   value="payhere">
                                                            <span>
                                                               <img loading="lazy"
                                                                    src="{{ my_asset('frontend/images/icons/cards/payhere.png')}}"
                                                                    class="img-fluid">
                                                           </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\Addon::where('unique_identifier', 'paytm')->first() != null && \App\Addon::where('unique_identifier', 'paytm')->first()->activated)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4" data-toggle="tooltip"
                                                               data-title="Paytm">
                                                            <input type="radio" id="" name="payment_option"
                                                                   value="paytm">
                                                            <span>
                                                                <img loading="lazy"
                                                                     src="{{ my_asset('frontend/images/icons/cards/paytm.jpg')}}"
                                                                     class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'cash_payment')->first()->value == 1)
                                                    @php
                                                        $digital = 0;
                                                        foreach(Session::get('cart') as $cartItem){
                                                            if($cartItem['digital'] == 1){
                                                                $digital = 1;
                                                            }
                                                            if ($digit_status) {
                                                                $digital = 1;
                                                            }
                                                        }
                                                    @endphp
                                                    @if($digital != 1)
                                                        <div class="col-6">
                                                            <label class="payment_option mb-4" data-toggle="tooltip"
                                                                   data-title="Cash on Delivery">
                                                                <input type="radio" id="" name="payment_option"
                                                                       value="cash_on_delivery">
                                                                <span>
                                                                    <img loading="lazy"
                                                                         src="{{ my_asset('frontend/images/icons/cards/cash.png')}}"
                                                                         style="object-fit: contain;height: 111px;width: 100%;"
                                                                         class="img-fluid">
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endif
                                                @if (Auth::check())
                                                    @if (\App\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                                                        @foreach(\App\ManualPaymentMethod::all() as $method)
                                                            @if($method->type == 'qr_code')
                                                                <div class="col-6">
                                                                    <label class="payment_option mb-4 w-100"
                                                                           style="height: 111.31px;"
                                                                           data-toggle="tooltip"
                                                                           data-title="{{ $method->heading }}">
                                                                        <input type="radio" id="" name="payment_option"
                                                                               value="{{ $method->heading }}">
                                                                        <span class="h-100">
                                                                        <img loading="lazy"
                                                                             src="{{ my_asset($method->photo)}}"
                                                                             class="img-fluid img-fit">
                                                                    </span>
                                                                    </label>
                                                                </div>
                                                            @else
                                                                <div class="col-6">
                                                                    <label class="payment_option mb-4 w-100"
                                                                           style="height: 111.31px;"
                                                                           data-toggle="tooltip"
                                                                           data-title="{{ $method->heading }}">
                                                                        <input type="radio" id="" name="payment_option"
                                                                               value="{{ $method->heading }}">
                                                                        <span class="h-100">
                                                                      <img loading="lazy"
                                                                           src="{{ my_asset($method->photo)}}"
                                                                           class="img-fluid img-fit">
                                                                  </span>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if (Auth::check() && \App\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)
                                        <div class="or or--1 mt-2">
                                            <span>or</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-xxl-6 col-lg-8 col-md-10 mx-auto">
                                                <div class="text-center bg-gray py-4">
                                                    <i class="fa"></i>
                                                    <div class="h5 mb-4">{{ translate('Your wallet balance :')}}
                                                        <strong>{{ single_price(Auth::user()->balance) }}</strong></div>
                                                    @if(Auth::user()->balance < $total)
                                                        <button type="button" class="btn btn-base-2"
                                                                disabled>{{ translate('Insufficient balance')}}</button>
                                                    @else
                                                        <button type="button" onclick="use_wallet()"
                                                                class="btn btn-base-1">{{ translate('Pay with wallet')}}</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="pt-3">
                                <input id="agree_checkbox" type="checkbox" required>
                                <label for="agree_checkbox">{{ translate('I agree to the')}}</label>
                                <a href="{{ route('terms') }}">{{ translate('terms and conditions')}}</a>,
                                <a href="{{ route('returnpolicy') }}">{{ translate('return policy')}}</a> &
                                <a href="{{ route('privacypolicy') }}">{{ translate('privacy policy')}}</a>
                            </div>

                            <div class="row align-items-center pt-3">
                                <div class="col-6">
                                    <a href="{{ route('home') }}" class="link link--style-3">
                                        <i class="ion-android-arrow-back"></i>
                                        {{ translate('Return to shop')}}
                                    </a>
                                </div>
                                <div class="col-6 text-right">
                                    <button type="button" onclick="submitOrder(this)"
                                            class="btn btn-styled btn-base-1">{{ translate('Complete Order')}}</button>
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
<div id="load_qr_code_modal">

</div>
<style>
    #payment_modal {
        z-index: 2000 !important;
    }
</style>

@section('script')
    <script type="text/javascript">
        function use_wallet() {
            $('input[name=payment_option]').val('wallet');
            if ($('#agree_checkbox').is(":checked")) {
                $('#checkout-form').submit();
            } else {
                showFrontendAlert('error', '{{ translate('You need to agree with our policies') }}');
            }
        }

        function submitOrder(el) {
            $(el).prop('disabled', true);
            if ($('#agree_checkbox').is(":checked")) {
                $('#checkout-form').submit();
            } else {
                showFrontendAlert('error', '{{ translate('You need to agree with our policies') }}');
                $(el).prop('disabled', false);
            }
        }
    </script>
@endsection
