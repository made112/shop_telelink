<div class="modal-header">
    <h5 class="modal-title strong-600 heading-5">{{ translate('Order id')}}: {{ $order->code }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

@php
    $status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->delivery_status;
    $payment_status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->payment_status;
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp

<div class="modal-body gry-bg px-3 pt-0">
    <div class="pt-4">
        <ul class="process-steps clearfix">
            <li @if($status == 'pending') class="active" @else class="done" @endif>
                <div class="icon">1</div>
                <div class="title">{{ translate('Order placed')}}</div>
            </li>
            <li @if($status == 'on_review') class="active"
                @elseif($status == 'on_delivery' || $status == 'delivered') class="done" @endif>
                <div class="icon">2</div>
                <div class="title">{{ translate('On review')}}</div>
            </li>
            <li @if($status == 'on_delivery') class="active" @elseif($status == 'delivered') class="done" @endif>
                <div class="icon">3</div>
                <div class="title">{{ translate('On delivery')}}</div>
            </li>
            <li @if($status == 'delivered') class="done" @endif>
                <div class="icon">4</div>
                <div class="title">{{ translate('Delivered')}}</div>
            </li>
        </ul>
    </div>
    @if($status != 'cancelled')
        <div class="row mt-5">
            <div class="offset-lg-2 col-lg-4 col-sm-6">
                <div class="form-inline">
                    <select class="form-control selectpicker form-control-sm" data-minimum-results-for-search="Infinity"
                            id="update_payment_status">
                        <option value="unpaid"
                                @if ($payment_status == 'unpaid') selected @endif>{{ translate('Unpaid')}}</option>
                        <option value="paid"
                                @if ($payment_status == 'paid') selected @endif>{{ translate('Paid')}}</option>
                    </select>
                    <label class="my-2">{{ translate('Payment Status')}}</label>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="form-inline">
                    <select class="form-control selectpicker form-control-sm" data-minimum-results-for-search="Infinity"
                            id="update_delivery_status">
                        <option value="pending"
                                @if ($status == 'pending') selected @endif>{{ translate('Pending')}}</option>
                        <option value="on_review"
                                @if ($status == 'on_review') selected @endif>{{ translate('On review')}}</option>
                        <option value="on_delivery"
                                @if ($status == 'on_delivery') selected @endif>{{ translate('On delivery')}}</option>
                        {{--                    <option value="delivered" @if ($status == 'delivered') selected @endif>{{ translate('Delivered')}}</option>--}}
                    </select>
                    <label class="my-2">{{ translate('Delivery Status')}}</label>
                </div>
            </div>
        </div>
    @else
        <div class="text-center mt-4">
           <span class="text-bold text-danger" style="font-size: 20px">
               {{translate('Cancelled Order')}}
           </span>
        </div>
    @endif
    <div class="card mt-3">
        <div class="card-header py-2 px-3 ">
            <div class="heading-6 strong-600">{{ translate('Order Summary')}}</div>
        </div>
        <div class="card-body pb-0">
            <div class="row">
                <div class="col-lg-6">
                    <table class="details-table table">
                        <tr>
                            <td class="w-50 strong-600">{{ translate('Order Code')}}:</td>
                            <td>{{ $order->code }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 strong-600">{{ translate('Customer')}}:</td>
                            <td>{{ json_decode($order->shipping_address)->name }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 strong-600">{{ translate('Email')}}:</td>
                            @if ($order->user_id != null)
                                <td>{{ $order->user->email }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="w-50 strong-600">{{ translate('Shipping address')}}:</td>
                            <td>{{ json_decode($order->shipping_address)->address }}
                                , {{ json_decode($order->shipping_address)->postal_code }}
                                , {{ json_decode($order->shipping_address)->country }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 strong-600">{{ translate('City')}}:</td>
                            <td>{{ getNameCity(json_decode($order->shipping_address)->city) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="details-table table">
                        <tr>
                            <td class="w-50 strong-600">{{ translate('Order date')}}:</td>
                            <td>{{ date('d-m-Y H:i A', $order->date) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 strong-600">{{ translate('Order status')}}:</td>
                            <td>{{ $status }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 strong-600">{{ translate('Total order amount')}}:</td>
                            <td>{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('price') + $order->orderDetails->where('seller_id', Auth::user()->id)->sum('tax')) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 strong-600">{{ translate('Contact')}}:</td>
                            <td>{{ json_decode($order->shipping_address)->phone }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 strong-600">{{ translate('Payment method')}}:</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-9">
            <div class="card mt-4">
                <div class="card-header py-2 px-3 heading-6 strong-600">{{ translate('Order Details')}}</div>
                <div class="card-body pb-0">
                    <table class="details-table table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="40%">{{ translate('Product')}}</th>
                            <th>{{ translate('Variation')}}</th>
                            <th>{{ translate('Quantity')}}</th>
                            <th>{{ translate('Delivery Type')}}</th>
                            <th>{{ translate('Price')}}</th>
                            @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                <th>{{ translate('Refund')}}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>
                                    @if ($orderDetail->product != null)
                                        <a href="{{ route('product', $orderDetail->product->slug) }}"
                                           target="_blank">{{ $orderDetail->product->name }}</a>
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
                                            {{ $orderDetail->pickup_point->name }} ({{  translate('Pickip Point') }})
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $orderDetail->price }}</td>
                                @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                    <td>
                                        @if ($orderDetail->product != null && $orderDetail->product->refundable != 0 && $orderDetail->refund_request == null)
                                            <button type="submit" class="btn btn-styled btn-sm btn-base-1"
                                                    onclick="send_refund_request('{{ $orderDetail->id }}')">{{  translate('Send') }}</button>
                                        @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 0)
                                            <span class="strong-600">{{  translate('Pending') }}</span>
                                        @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 1)
                                            <span class="strong-600">{{  translate('Paid') }}</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card mt-4">
                <div class="card-header py-2 px-3 heading-6 strong-600">{{ translate('Order Ammount')}}</div>
                <div class="card-body pb-0">
                    <table class="table details-table">
                        <tbody>
                        <tr>
                            <th>{{ translate('Subtotal')}}</th>
                            <td class="text-right">
                                <span
                                    class="strong-600">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('price')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ translate('Shipping')}}</th>
                            <td class="text-right">
                                <span
                                    class="text-italic">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('shipping_cost')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ translate('Tax')}}</th>
                            <td class="text-right">
                                <span
                                    class="text-italic">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('tax')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th><span class="strong-600">{{ translate('Total')}}</span></th>
                            <td class="text-right">
                                <strong>
                                        <span>{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('price') + $order->orderDetails->where('seller_id', Auth::user()->id)->sum('tax') + $order->orderDetails->where('seller_id', Auth::user()->id)->sum('shipping_cost')) }}
                                        </span>
                                </strong>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#update_delivery_status').on('change', function () {
        var order_id = {{ $order->id }};
        var status = $('#update_delivery_status').val();
        $.post('{{ route('orders.update_delivery_status') }}', {
            _token: '{{ @csrf_token() }}',
            order_id: order_id,
            status: status
        }, function (data) {
            $('#order_details').modal('hide');
            showFrontendAlert('success', 'Order status has been updated');
            location.reload().setTimeOut(500);
        });
    });

    $('#update_payment_status').on('change', function () {
        var order_id = {{ $order->id }};
        var status = $('#update_payment_status').val();
        $.post('{{ route('orders.update_payment_status') }}', {
            _token: '{{ @csrf_token() }}',
            order_id: order_id,
            status: status
        }, function (data) {
            $('#order_details').modal('hide');
            //console.log(data);
            showFrontendAlert('success', 'Payment status has been updated');
            location.reload().setTimeOut(500);
        });
    });
</script>
