@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('14', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">{{translate('Orders')}}</h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Order Code')}}</th>
                    <th>{{translate('Num. of Products')}}</th>
                    <th>{{translate('Customer')}}</th>
                    <th>{{translate('Amount')}}</th>
                    <th>{{translate('Delivery Type')}}</th>
                    <th>{{translate('Delivery Status')}}</th>
                    <th>{{translate('Payment Method')}}</th>
                    <th>{{translate('Payment Status')}}</th>
                    <th width="15%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order_id)
                    @php
                        $order = \App\Order::find($order_id->id);
                    @endphp
                    @if($order != null)
                        <tr>
                            <td>
                                {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}
                            </td>
                            <td>
                                {{ $order->code }} @if($order->viewed == 0) <span class="pull-right badge badge-info">{{ translate('New') }}</span> @endif
                            </td>
                            <td>
                                {{ count($order->orderDetails) }}
                            </td>
                            <td>
                                @if ($order->user_id != null)
                                    {{ $order->user->name }}
                                @else
                                    Guest ({{ $order->guest_id }})
                                @endif
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax')) }}
                            </td>
                            <td>
                                {{ isset($order->delivery_type) ? __($order->delivery_type) : 'N/A' }}
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
                                    @if ($order->orderDetails->first()->payment_status == 'paid')
                                        <i class="bg-green"></i> {{ translate('Paid') }}
                                    @else
                                        <i class="bg-red"></i> {{ translate('Unpaid') }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('pick_up_point.order_show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                    <i class="las la-eye"></i>
                                </a>
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('admin.invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
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
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
    <script type="text/javascript">

    </script>
@endsection
@endif
