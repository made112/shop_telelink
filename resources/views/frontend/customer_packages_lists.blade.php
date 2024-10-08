@extends('frontend.layouts.app')

@section('content')
<div id="page-content">
    <section class="pb-4 pt-5">
        <div class="container">
            <div class="text-center">
                <h1 class="heading-3 strong-600">{{ translate('Premium Packages for Customers')}}</h1>
            </div>
            <div class="row justify-content-center gutters-10">
                @foreach ($customer_packages as $key => $customer_package)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="dashboard-widget text-center mt-4 c-pointer">
                            <img alt="Package Logo" src="{{ my_asset($customer_package->logo) }}" width="200" class="img-fluid mb-4">
                            <span class="d-block title">{{ $customer_package->name }}</span>
                            <p>{{ translate('Product Upload')}}: {{ $customer_package->product_upload }} {{ translate('Times')}}</p>
                            <span class="d-block title">{{ translate('Price')}}: {{ single_price($customer_package->amount) }}</span>
                            <hr>
                            @if ($customer_package->amount == 0)
                                <button class="btn btn-base-1 w-100" onclick="get_free_package({{ $customer_package->id}})">{{ translate('Free Package')}}</button>
                            @else
                                <button class="btn btn-base-1 w-100" onclick="show_price_modal({{ $customer_package->id}})">{{ translate('Get Package')}}</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="price_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title strong-600 heading-5">{{ translate('Purchase Your Package')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="" id="package_payment_form" action="{{ route('customer_packages.purchase') }}" method="post">
                @csrf
                <input type="hidden" name="customer_package_id" value="">
                <div class="modal-body gry-bg px-3 pt-3">
                    <div class="row">
                        <div class="col-md-2">
                            <label>{{ translate('Payment Method')}}</label>
                        </div>
                        <div class="col-md-10">
                            <div class="mb-3">
                                <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="payment_option">
                                    @if (\App\BusinessSetting::where('type', 'paypal_payment')->first()->value == 1)
                                        <option value="paypal">{{ translate('Paypal')}}</option>
                                    @endif
                                    @if (\App\BusinessSetting::where('type', 'stripe_payment')->first()->value == 1)
                                        <option value="stripe">{{ translate('Stripe')}}</option>
                                    @endif
                                    @if(\App\BusinessSetting::where('type', 'sslcommerz_payment')->first()->value == 1)
                                        <option value="sslcommerz">{{ translate('sslcommerz')}}</option>
                                    @endif
                                    @if(\App\BusinessSetting::where('type', 'instamojo_payment')->first()->value == 1)
                                        <option value="instamojo">{{ translate('Instamojo')}}</option>
                                    @endif
                                    @if(\App\BusinessSetting::where('type', 'razorpay')->first()->value == 1)
                                        <option value="razorpay">{{ translate('RazorPay')}}</option>
                                    @endif
{{--                                    @if(\App\BusinessSetting::where('type', 'paystack')->first()->value == 1)--}}
{{--                                        <option value="paystack">{{ translate('PayStack')}}</option>--}}
{{--                                    @endif--}}
                                    @if(\App\BusinessSetting::where('type', 'voguepay')->first()->value == 1)
                                        <option value="voguepay">{{ translate('Voguepay')}}</option>
                                    @endif
                                    @if(\App\BusinessSetting::where('type', 'payhere')->first()->value == 1)
                                        <option value="payhere">{{ translate('Payhere')}}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('cancel')}}</button>
                    <button type="submit" class="btn btn-base-1">{{ translate('Confirm')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        function show_price_modal(id){
            $('input[name=customer_package_id]').val(id);
            $('#price_modal').modal('show');
        }

        function get_free_package(id){
            $('input[name=customer_package_id]').val(id);
            $('#package_payment_form').submit();
        }
    </script>
@endsection
