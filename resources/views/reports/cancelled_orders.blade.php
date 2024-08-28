@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="pad-all text-center clearfix">
        <div class="select clearfix float-right mar-lft" style="width: 150px;">
            <form id="formExport" action="{{ route('cancelled_orders.download')}}" method="GET">
                @csrf
                <select id="exportFiles" class="demo-select2" name="export" required>
                    <option value="">{{ translate('Select To Export') }}</option>
                    <option value="pdf">{{ translate('PDF') }}</option>
                    <option value="excel">{{ translate('Excel') }}</option>
                    <option value="word">{{ translate('Word') }}</option>
                </select>
            </form>
        </div>
    </div>


    <div class="col-md-offset-2 col-md-8">
        <div class="panel">
            <!--Panel heading-->
            <div class="panel-heading">
                <h3 class="panel-title">{{ translate('Cancelled Orders Report') }}</h3>
            </div>

            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no demo-dt-basic">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{translate('Order Code')}}</th>
                            <th>{{translate('Num. of Products')}}</th>
                            <th>{{translate('Customer')}}</th>
                            <th>{{translate('Amount')}}</th>
                            <th>{{translate('Delivery Status')}}</th>
                            <th>{{translate('Payment Method')}}</th>
                            <th>{{translate('Payment Status')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($orders != null)
                            @foreach ($orders as $key => $order)
                                @if($order != null)
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
                                            @php
                                                $status = $order->orderDetails->first()->delivery_status;
                                            @endphp
                                            <span class="badge badge-danger">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
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
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script type="text/javascript">
        {{--Load Form Submit to export files--}}
        exportFile();
    </script>
@endsection
@endif
