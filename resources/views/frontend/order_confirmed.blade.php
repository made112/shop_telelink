@extends('frontend.layouts.app')

@section('content')
    @php
        try {
            $status = $order->orderDetails->first()->delivery_status;
         } catch (\Exception $e) {

         }
    @endphp
    @if($order->manual_payment == 1)
        <script>
            $(document).ready(function () {
                show_make_payment_modal({{$order->id}});
            })
        </script>
    @endif
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
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">
                                    1. {{ translate('My Cart')}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center ">
                            <div class="block-icon mb-0 c-gray-light">
                                <i class="la la-truck"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">
                                    2. {{ translate('Delivery info')}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center ">
                            <div class="block-icon mb-0 c-gray-light">
                                <i class="la la-map-o"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">
                                    3. {{ translate('Shipping info')}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center ">
                            <div class="block-icon mb-0 c-gray-light">
                                <i class="la la-credit-card"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">
                                    4. {{ translate('Payment')}}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center active">
                            <div class="block-icon mb-0">
                                <i class="la la-check-circle"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">
                                    5. {{ translate('Confirmation')}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-4">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 mx-auto">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center py-4 border-bottom mb-4">
                                    <i class="la la-check-circle la-3x text-success mb-3"></i>
                                    <h1 class="h3 mb-3">{{ translate('Thank You for Your Order!')}}</h1>
                                    <h2 class="h5 strong-700">{{ translate('Order Code:')}} {{ $order->code }}</h2>
                                    @if(isset(json_decode($order->shipping_address)->phone) || isset(json_decode($order->shipping_address)->email))
                                        <p class="text-muted text-italic">{{  translate('A copy or your order summary has been sent to') }}
                                            @if(isset(json_decode($order->shipping_address)->email)) {{ json_decode($order->shipping_address)->email }}
                                            @elseif(isset(json_decode($order->shipping_address)->phone)) {{ json_decode($order->shipping_address)->phone }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="mb-4">
                                    <h5 class="strong-600 mb-3 border-bottom pb-2">{{ translate('Order Summary')}}</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="table-responsive">
                                                <table class="details-table table">
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Order Code')}}:</td>
                                                        <td>{{ $order->code }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Name')}}:</td>
                                                        @if(isset(json_decode($order->shipping_address)->name))
                                                            <td>{{ json_decode($order->shipping_address)->name }}</td>
                                                        @elseif(@Auth::user()->name)
                                                            <td>{{ @Auth::user()->name }}</td>
                                                        @endif
                                                    </tr>
                                                    @if(isset(json_decode($order->shipping_address)->email))
                                                        <tr>
                                                            <td class="w-50 strong-600">{{ translate('Email')}}:</td>
                                                            <td>{{ json_decode($order->shipping_address)->email }}</td>
                                                        </tr>
                                                    @elseif(@Auth::user()->email)
                                                        <tr>
                                                            <td class="w-50 strong-600">{{ translate('Email')}}:</td>
                                                            <td>{{ @Auth::user()->email }}</td>
                                                        </tr>
                                                    @endif

                                                    @if(isset(json_decode($order->shipping_address)->phone))
                                                        <tr>
                                                            <td class="w-50 strong-600">{{ translate('Phone')}}:</td>
                                                            <td>{{  json_decode($order->shipping_address)->phone }}</td>
                                                        </tr>
                                                    @elseif(@Auth::user()->phone)
                                                        <tr>
                                                            <td class="w-50 strong-600">{{ translate('Phone')}}:</td>
                                                            <td>{{ @Auth::user()->phone}}</td>
                                                        </tr>
                                                    @endif
                                                    @if(isset(json_decode($order->shipping_address)->city))
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('City')}}:</td>
                                                        <td>{{ getNameCity(json_decode($order->shipping_address)->city) }}</td>
                                                    </tr>
                                                    @elseif(@Auth::user()->city)
                                                        <tr>
                                                            <td class="w-50 strong-600">{{ translate('City')}}:</td>
                                                            <td>{{ getNameCity(@Auth::user()->city) }}</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Shipping address')}}
                                                            :
                                                        </td>
                                                        @if(isset(json_decode($order->shipping_address)->address))
                                                            <td>{{ isset(json_decode($order->shipping_address)->address) && json_decode($order->shipping_address)->address }}
                                                                , {{ isset(json_decode($order->shipping_address)->city) && getNameCity(json_decode($order->shipping_address)->city) }}
                                                                , {{ isset(json_decode($order->shipping_address)->country) && json_decode($order->shipping_address)->country }}</td>
                                                        @endif
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Delivery Type')}}:
                                                        </td>
                                                        <td>{{ !isOrderDetailssDigit($order->orderDetails) ? '-' : translate($order->delivery_type) }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="table-responsive">
                                                <table class="details-table table">
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Order date')}}:</td>
                                                        <td>{{ date('d-m-Y H:i A', $order->date) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Order status')}}:</td>
                                                        <td>{{ translate(ucfirst(str_replace('_', ' ', isset($status) ? $status : null))) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Total order amount')}}
                                                            :
                                                        </td>
                                                        <td>{{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax')) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Shipping')}}:</td>
                                                        <td>{{ translate('Flat shipping rate')}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Payment method')}}:
                                                        </td>
                                                        <td>{{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="strong-600 mb-3 border-bottom pb-2">{{ translate('Order Details')}}</h5>
                                    <div class="table-responsive">
                                        <table class="details-table table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th width="30%">{{ translate('Product')}}</th>
                                                <th>{{ translate('Store Name')}}</th>
                                                <th>{{ translate('Variation')}}</th>
                                                <th>{{ translate('Quantity')}}</th>
                                                <th>{{ translate('Shipping Type')}}</th>
                                                <th>{{ translate('Delivery Type')}}</th>
                                                <th>{{ translate('Shipping Cost')}}</th>
                                                <th class="text-right">{{ translate('Price')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($order->orderDetails as $key => $orderDetail)
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td>
                                                        @if ($orderDetail->product != null)
                                                            <a href="{{ route('product', $orderDetail->product->slug) }}"
                                                               target="_blank">
                                                                {{ $orderDetail->product->name }}
                                                            </a>
                                                        @else
                                                            <strong>{{  translate('Product Unavailable') }}</strong>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            if ($orderDetail->seller && $orderDetail->seller->user_type == 'seller') {
                                                                if (isset($orderDetail->seller->shop->name)) echo $orderDetail->seller->shop->name;
                                                                else echo '_';
                                                            }else {
                                                                echo translate('UNO');
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        {{ $orderDetail->variation }}
                                                    </td>
                                                    <td>
                                                        {{ $orderDetail->quantity }}
                                                    </td>
                                                    <td>
                                                        @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                                            {{  translate('Home Delivery') }}
                                                        @elseif ($orderDetail->shipping_type == 'pickup_point')
                                                            @if ($orderDetail->pickup_point != null)
                                                                {{ $orderDetail->pickup_point->name }}
                                                                ({{ translate('Pickip Point') }})
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $orderDetail->delivery_type == 'direct_delivery' || $orderDetail->delivery_type == 'direct' ? translate('Direct') : translate('Collective') }}
                                                    </td>
                                                    <td>
                                                        {{ $orderDetail->shipping_cost }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ single_price($orderDetail->price) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-5 col-md-6 ml-auto">
                                            <div class="table-responsive">
                                                <table class="table details-table">
                                                    <tbody>
                                                    <tr>
                                                        <th>{{ translate('Subtotal')}}</th>
                                                        <td class="text-right">
                                                            <span
                                                                class="strong-600">{{ single_price($order->orderDetails->sum('price')) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Shipping')}}</th>
                                                        <td class="text-right">
                                                            <span
                                                                class="text-italic">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Tax')}}</th>
                                                        <td class="text-right">
                                                            <span
                                                                class="text-italic">{{ single_price($order->orderDetails->sum('tax')) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Coupon Discount')}}</th>
                                                        <td class="text-right">
                                                            <span
                                                                class="text-italic">{{ single_price($order->coupon_discount) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th><span class="strong-600">{{ translate('Total')}}</span></th>
                                                        <td class="text-right">
                                                            <strong><span>{{ single_price($order->grand_total) }}</span></strong>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
<style>
    #payment_modal {
        z-index: 2000 !important;
    }
</style>
<div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title strong-600 heading-5">{{ translate('Make Payment')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="payment_modal_body"></div>
        </div>
    </div>
</div>
@section('script')
    <script type="text/javascript">
        function show_make_payment_modal(order_id) {
            $.post('{{ route('checkout.make_payment') }}', {
                _token: '{{ csrf_token() }}',
                order_id: order_id
            }, function (data) {
                $('#payment_modal_body').html(data);
                $('#payment_modal').modal('show');
                $('input[name=order_id]').val(order_id);
            });
        }
    </script>
@endsection

