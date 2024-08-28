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
                                <div class="col-md-6 col-12 d-flex align-items-center">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{__('Send Refund Request')}}
                                    </h2>
                                </div>
                            </div>
                        </div>

                        <form class="" action="{{route('refund_request_send', $order_detail->id)}}" data-toggle="validator" method="POST" enctype="multipart/form-data" id="choice_form">
                            @csrf
                            <div class="form-box bg-white mt-4">
                                <div class="form-box-content p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{__('Product Name')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control mb-3" required name="name" placeholder="{{__('Product Name')}}" value="{{ $order_detail->product->name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{__('Product Price')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="number" class="form-control mb-3" required name="name" placeholder="{{__('Product Price')}}" value="{{ $order_detail->product->unit_price }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{__('Order Code')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control mb-3" required name="code" value="{{ $order_detail->order->code }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{__('Refund Reason')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10">
                                            <select required name="reason" id="reason" class="form-control mb-3">
                                                <option value="{{ translate('There is a break or damage to the order.') }}">{{ translate('There is a break or damage to the order.') }}</option>
                                                <option value="{{ translate('The product does not identical with the mentioned specifications.') }}">{{ translate('The product does not identical with the mentioned specifications.') }}</option>
                                                <option value="other" id="otherReason">{{ translate('Other') }}</option>
                                            </select>
                                        </div>
                                        <div id="reason-details" class="w-100"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-3">
                                <input id="agree_checkbox" type="checkbox">
                                <label for="agree_checkbox">{{ translate('I agree to the')}}</label>
                                <a target="_blank" href="{{ route('returnpolicy') }}">{{ translate('return policy')}}</a>
                            </div>
                            <div class="form-box mt-4 text-right">
                                <button type="submit" onclick="submitRefund(event,this)" class="btn btn-styled btn-base-1">{{ __('Send Request') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        var details = `
            <div class="col-md-12">
                <textarea name="reason_details" required rows="8" class="form-control mb-3"></textarea>
            </div>
        `;


        $('#reason').on('change',function () {
            if($(this).val() === 'other') {
                $('#reason-details').html(details);
            }else {
                $('#reason-details').html('');
            }
        })
        function submitRefund(e, el){
            e.preventDefault();
            $(el).prop('disabled', true);
            if($('#agree_checkbox').is(":checked")){
                $('#choice_form').submit();
            }else{
                showFrontendAlert('error','{{ translate('You need to agree with return policy') }}');
                $(el).prop('disabled', false);
            }
        }
    </script>
@endsection
