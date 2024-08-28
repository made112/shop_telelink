@extends('layouts.webview')

@section('content')
    @php
        try {
            $status = $order->orderDetails->first()->delivery_status;
         } catch (\Exception $e) {

         }
    @endphp
    <div id="page-content">
        <section class="py-4">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 mx-auto">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center py-4 mb-4">
                                    <i class="la la-check-circle la-3x text-success mb-3"></i>
                                    <h1 class="h3 mb-3">{{ translate('Thank You for Your Order!')}}</h1>
                                    <h2 class="h5 strong-700">{{ translate('Order Code:')}} {{ $order->code }}</h2>
                                    <p class="text-muted text-italic">{{  translate('A copy or your order summary has been sent to') }} {{ json_decode($order->shipping_address)->email }}</p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="strong-600 mb-3 pb-2">{{ translate('Order Summary')}}</h5>
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
                                                        <td>{{ json_decode($order->shipping_address)->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Email')}}:</td>
                                                        <td>{{ json_decode($order->shipping_address)->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Shipping address')}}:</td>
                                                        <td>{{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->city }}, {{ json_decode($order->shipping_address)->country }}</td>
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
                                                        <td class="w-50 strong-600">{{ translate('Total order amount')}}:</td>
                                                        <td>{{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax')) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Shipping')}}:</td>
                                                        <td>{{ translate('Flat shipping rate')}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="w-50 strong-600">{{ translate('Payment method')}}:</td>
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
                                                <th>{{ translate('Variation')}}</th>
                                                <th>{{ translate('Quantity')}}</th>
                                                <th>{{ translate('Delivery Type')}}</th>
                                                <th class="text-right">{{ translate('Price')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($order->orderDetails as $key => $orderDetail)
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td>
                                                        @if ($orderDetail->product != null)
                                                            <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">
                                                                {{ $orderDetail->product->name }}
                                                            </a>
                                                        @else
                                                            <strong>{{  translate('Product Unavailable') }}</strong>
                                                        @endif
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
                                                                {{ $orderDetail->pickup_point->name }} ({{ translate('Pickip Point') }})
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="text-right">{{ single_price($orderDetail->price) }}</td>
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
                                                            <span class="strong-600">{{ single_price($order->orderDetails->sum('price')) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Shipping')}}</th>
                                                        <td class="text-right">
                                                            <span class="text-italic">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Tax')}}</th>
                                                        <td class="text-right">
                                                            <span class="text-italic">{{ single_price($order->orderDetails->sum('tax')) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Coupon Discount')}}</th>
                                                        <td class="text-right">
                                                            <span class="text-italic">{{ single_price($order->coupon_discount) }}</span>
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
<div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        function show_make_payment_modal(order_id){
            $.post('{{ route('checkout.make_payment') }}', {_token:'{{ csrf_token() }}', order_id : order_id}, function(data){
                $('#payment_modal_body').html(data);
                $('#payment_modal').modal('show');
                $('input[name=order_id]').val(order_id);
            });
        }
    </script>
@endsection

