@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('4', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
    @php
        $delivery_status = $order->orderDetails->first()->delivery_status;
        $payment_status = $order->orderDetails->first()->payment_status;
    @endphp
    <div class="panel">
        <div class="panel-body">
            <div class="invoice-masthead">
                <div class="invoice-text">
                    <h3 class="h1 text-thin mar-no text-primary">{{ translate('Order Details') }}</h3>
                </div>
            </div>
            <div class="invoice-bill row">
                <div class="pt-4 col-12 mb-5">
                    <ul class="process-steps clearfix">
                        <li @if($delivery_status == 'pending') class="active" @else class="done" @endif>
                            <div class="icon">1</div>
                            <div class="title">{{ translate('Order placed')}}</div>
                        </li>
                        <li @if($delivery_status == 'on_review') class="active"
                            @elseif($delivery_status == 'on_delivery' || $delivery_status == 'delivered') class="done" @endif>
                            <div class="icon">2</div>
                            <div class="title">{{ translate('On review')}}</div>
                        </li>
                        <li @if($delivery_status == 'on_delivery') class="active"
                            @elseif($delivery_status == 'delivered') class="done" @endif>
                            <div class="icon">3</div>
                            <div class="title">{{ translate('On delivery')}}</div>
                        </li>
                        <li @if($delivery_status == 'delivered') class="done" @endif>
                            <div class="icon">4</div>
                            <div class="title">{{ translate('Delivered')}}</div>
                        </li>
                    </ul>
                </div>
                @if($delivery_status != 'cancelled')
                    <div class="col-lg-offset-3 col-lg-3">
                        <label for=update_payment_status"">{{translate('Payment Status')}}</label>
                        <select class="form-control demo-select2" data-minimum-results-for-search="Infinity"
                                id="update_payment_status">
                            <option value="paid"
                                    @if ($payment_status == 'paid') selected @endif>{{translate('Paid')}}</option>
                            <option value="unpaid"
                                    @if ($payment_status == 'unpaid') selected @endif>{{translate('Unpaid')}}</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label for=update_delivery_status"">{{translate('Delivery Status')}}</label>
                        <select class="form-control demo-select2" data-minimum-results-for-search="Infinity"
                                id="update_delivery_status">
                            <option value="pending"
                                    @if ($delivery_status == 'pending') selected @endif>{{translate('Pending')}}</option>
                            <option value="on_review"
                                    @if ($delivery_status == 'on_review') selected @endif>{{translate('On review')}}</option>
                            <option value="on_delivery"
                                    @if ($delivery_status == 'on_delivery') selected @endif>{{translate('On delivery')}}</option>
                            <option value="delivered"
                                    @if ($delivery_status == 'delivered') selected @endif>{{translate('Delivered')}}</option>
                        </select>
                    </div>
                @else
                    <div class="text-center">
                       <span class="text-bold text-danger" style="font-size: 20px">
                           {{translate('Cancelled Order')}}
                       </span>
                    </div>
                @endif

                <div class="col-sm-6 text-xs-center mt-5">
                    <address>
                        <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                        {{ json_decode($order->shipping_address)->email }}<br>
                        {{ json_decode($order->shipping_address)->phone }}<br>
                        {{ getNameCity(json_decode($order->shipping_address)->city) }} <br>
                        {{ json_decode($order->shipping_address)->address }} {{ isset(json_decode($order->shipping_address)->postal_code) ? ', '. json_decode($order->shipping_address)->postal_code : '' }}
                        <br>
                        {{ json_decode($order->shipping_address)->country }}
                    </address>
                    @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                        <br>
                        <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                        {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }}
                        , {{ translate('Amount') }}
                        : {{ single_price(json_decode($order->manual_payment_data)->amount) }}
                        , {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                        <br>
                        <a href="{{ my_asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank"><img
                                src="{{ my_asset(json_decode($order->manual_payment_data)->photo) }}" alt=""
                                height="100"></a>
                    @endif
                </div>
                <div class="col-sm-6 text-xs-center mt-5">
                    <table class="invoice-details">
                        <tbody>
                        <tr>
                            <td class="text-main text-bold">
                                {{translate('Order #')}}
                            </td>
                            <td class="text-right text-info text-bold">
                                {{ $order->code }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{translate('Order Status')}}
                            </td>
                            @php
                                $status = $order->orderDetails->first()->delivery_status;
                            @endphp
                            <td class="text-right">
                                @if($status == 'delivered')
                                    <span
                                        class="badge badge-success">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                @else
                                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{translate('Order Date')}}
                            </td>
                            <td class="text-right">
                                {{ date('d-m-Y h:i A', $order->date) }} (UTC)
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{translate('Total amount')}}
                            </td>
                            <td class="text-right">
                                {{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax')) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{translate('Payment method')}}
                            </td>
                            <td class="text-right">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr class="new-section-sm bord-no">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-bordered invoice-summary">
                        <thead>
                        <tr class="bg-trans-dark">
                            <th class="min-col">#</th>
                            <th width="10%">
                                {{translate('Photo')}}
                            </th>
                            <th class="text-uppercase">
                                {{translate('Description')}}
                            </th>
                            <th class="text-uppercase">
                                {{translate('Delivery Type')}}
                            </th>
                            <th class="min-col text-center text-uppercase">
                                {{translate('Qty')}}
                            </th>
                            <th class="min-col text-center text-uppercase">
                                {{translate('Price')}}
                            </th>
                            <th class="min-col text-right text-uppercase">
                                {{translate('Total')}}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->orderDetails as $key => $orderDetail)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>
                                    @if ($orderDetail->product != null)
                                        <a href="{{ route('product', $orderDetail->product->slug) }}"
                                           target="_blank"><img height="50"
                                                                src={{ my_asset($orderDetail->product->thumbnail_img) }}/></a>
                                    @else
                                        <strong>{{ translate('N/A') }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @if ($orderDetail->product != null)
                                        <strong><a href="{{ route('product', $orderDetail->product->slug) }}"
                                                   target="_blank">{{ $orderDetail->product->name }}</a></strong>
                                        <small>{{ $orderDetail->variation }}</small>
                                    @else
                                        <strong>{{ translate('Product Unavailable') }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                        {{ translate('Home Delivery') }}
                                    @elseif ($orderDetail->shipping_type == 'pickup_point')
                                        @if ($orderDetail->pickup_point != null)
                                            {{ $orderDetail->pickup_point->name }} ({{ translate('Pickup Point') }})
                                        @else
                                            {{ translate('Pickup Point') }}
                                        @endif
                                    @endif
                                    @php
                                        $delivery_type = $orderDetail->delivery_type;
                                        if ($delivery_type == 'direct_delivery'){
                                            echo  '/ ' .translate('Direct');
                                        }elseif ($delivery_type == 'collective_delivery') {
                                            echo  '/ ' .translate('Collective');
                                        }else {
                                            echo  '/ ' .translate('N/A');
                                        }
                                    @endphp
                                </td>
                                <td class="text-center">
                                    {{ $orderDetail->quantity }}
                                </td>
                                <td class="text-center">
                                    {{ single_price($orderDetail->price/$orderDetail->quantity) }}
                                </td>
                                <td class="text-center">
                                    {{ single_price($orderDetail->price) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix">
                <table class="table invoice-total">
                    <tbody>
                    <tr>
                        <td>
                            <strong>{{translate('Sub Total')}} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->orderDetails->sum('price')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{translate('Tax')}} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->orderDetails->sum('tax')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{translate('Shipping')}} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->orderDetails->sum('shipping_cost')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{translate('TOTAL')}} :</strong>
                        </td>
                        <td class="text-bold h4">
                            {{ single_price($order->grand_total) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-right no-print">
                <a href="{{ route('customer.invoice.download', $order->id) }}" class="btn btn-default"><i
                        class="demo-pli-printer icon-lg"></i></a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#update_delivery_status').on('change', function () {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            $.post('{{ route('sales.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function (data) {
                showAlert('success', 'Delivery status has been updated');
            });
        });

        $('#update_payment_status').on('change', function () {
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            $.post('{{ route('sales.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function (data) {
                showAlert('success', 'Payment status has been updated');
            });
        });
    </script>
@endsection
@endif
