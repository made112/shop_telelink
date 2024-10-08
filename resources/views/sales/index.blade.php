@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('4', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
@php
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp
<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading bord-btm clearfix pad-all h-100">
        <h3 class="panel-title pull-left pad-no">{{translate('Orders')}}</h3>
        <div class="pull-right clearfix">
            <form class="" id="sort_orders" action="" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div class="select" style="min-width: 300px;">
                        <select class="form-control demo-select2" name="delivery_status" id="delivery_status" onchange="sort_orders()">
                            <option value="">{{translate('Filter by Deliver Status')}}</option>
                            <option value="pending"   @isset($delivery_status) @if($delivery_status == 'pending') selected @endif @endisset>{{translate('Pending')}}</option>
                            <option value="on_review"   @isset($delivery_status) @if($delivery_status == 'on_review') selected @endif @endisset>{{translate('On review')}}</option>
                            <option value="on_delivery"   @isset($delivery_status) @if($delivery_status == 'on_delivery') selected @endif @endisset>{{translate('On delivery')}}</option>
                            <option value="delivered"   @isset($delivery_status) @if($delivery_status == 'delivered') selected @endif @endisset>{{translate('Delivered')}}</option>
                            <option value="cancelled"   @isset($delivery_status) @if($delivery_status == 'cancelled') selected @endif @endisset>{{translate('Cancelled')}}</option>
                        </select>
                    </div>
                </div>
                <div class="box-inline pad-rgt pull-left">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Order Code') }}</th>
                    <th>{{ translate('Num. of Products') }}</th>
                    <th>{{ translate('Customer') }}</th>
                    <th>{{ translate('Amount') }}</th>
                    <th>{{ translate('Delivery Type') }}</th>
                    <th>{{ translate('Delivery Status') }}</th>
                    <th>{{translate('Payment Method')}}</th>
                    <th>{{ translate('Payment Status') }}</th>
                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <th>{{ translate('Refund') }}</th>
                    @endif
                    <th width="15%">{{translate('options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order_id)
                    @php
                        $order = \App\Order::find($order_id->id);
                    @endphp
                    @if($order !== null)
                    <tr>
                        <td>
                            {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}
                        </td>
                        <td>
                            {{ $order->code }}
                        </td>
                        <td>
                            {{ count($order->orderDetails) }}
                        </td>
                        <td>
                            @if ($order->user != null)
                                {{ $order->user->name }}
                            @else
                                Guest ({{ $order->guest_id }})
                            @endif
                        </td>
                        <td>
                            {{ single_price($order->grand_total) }}
                        </td>
                        <td>
                            @php
                                $delivery_type = $order->orderDetails->first()->delivery_type;
                                if ($delivery_type == 'direct_delivery'){
                                    echo  translate('Direct');
                                }elseif ($delivery_type == 'collective_delivery') {
                                    echo  translate('Collective');
                                }else {
                                    echo  translate('N/A');
                                }
                            @endphp
                        </td>
                        <td>
                            @php
                                $status = $order->orderDetails->first()->delivery_status;
                            @endphp
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </td>
                        <td>
                            {{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}
                        </td>
                        <td>
                            <span class="badge badge--2 mr-4">
                                @if ($order->payment_status == 'paid')
                                    <i class="bg-green"></i> {{ translate('Paid') }}
                                @else
                                    <i class="bg-red"></i> {{ translate('Unpaid') }}
                                @endif
                            </span>
                        </td>
                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                            <td>
                                @if (count($order->refund_requests) > 0)
                                    {{ count($order->refund_requests) }} {{ translate('Refund') }}
                                @else
                                    {{ translate('No Refund') }}
                                @endif
                            </td>
                        @endif
                        <td>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('sales.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('customer.invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                <i class="las la-download"></i>
                            </a>
                            @if($status != 'cancelled')<a onclick="confirm_modal('{{route('orders.destroy', $order->id)}}');" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.destroy', $order->id)}}" title="{{ translate('Cancel Order') }}">
                                <i class="las la-trash"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $orders->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
    <script type="text/javascript">
        function sort_orders(el){
            $('#sort_orders').submit();
        }
    </script>
@endsection
@endif
