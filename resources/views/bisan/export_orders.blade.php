@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
    <style>
        .select:before {
            content: none !important;
        }
        .cus_details {
            display: flex;flex-direction: row;justify-content: space-between;align-items: center;border-bottom: 1px solid #ccc;padding: 10px 0px;
        }
        .cus_details:first-child {
            padding: 0 0 10px;
        }
        .cus_details:last-child {
            border-bottom: none;
        }
        .label-info {
            border-radius: 100%;
            padding: 5px 6px;
            font-size: 13px;
        }
        .dtr-details {
            width: 100%;
        }
    </style>
    <div class="col-md-offset-2 col-md-8">
        <div class="panel" style="margin-bottom: 50px;">
            <!--Panel heading-->
            <div class="panel-heading" style="padding: 6px 0px">
                <h3 class="panel-title clearfix">
                    {{ translate('Export Products To Bisan') }}
                    <div class="select clearfix mar-lft" style="width: 180px;float: right">
                        <form id="formExport" action="{{ route('orders_bisan_export.download')}}" method="GET">
                            @csrf
                            <button name="export" class="btn btn-default">
                                <img src="{{my_asset('frontend/images/excel.png')}}" style="width: 25px;margin-right: 5px">
                                <span>{{ translate('Export All Orders') }}</span>
                            </button>
                        </form>
                    </div>
                </h3>
            </div>
            <hr>
            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no demo-dt-basic dataTable no-footer dtr-inline display nowrap w-100">
                        <thead>
                        <tr>
                            <th>{{ translate('Seller Name') }}</th>
                            <th>{{ translate('Invoice All Orders') }}</th>
                            <th>{{ translate('Orders') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($sellers as $key => $seller)
                                @if($seller)
                                    <tr>
                                        <td rowspan="1">{{ $seller->user['name'] }}</td>
                                        <td rowspan="1">
                                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('seller_selling_excel.download', ['seller' => encrypt($seller->user->id)])}}">
                                                <i class="las la-download"></i>
                                            </a>
                                        </td>
                                        <td class="none">
                                            @php
                                            $orderDetails = \App\OrderDetail::where('seller_id', $seller->user->id)->get();
                                            @endphp
                                            @if(count($orderDetails) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-striped mar-no demo-dt-basic dataTable no-footer dtr-inline display responsive nowrap w-100">
                                                        <thead>
                                                        <tr>
                                                            <th>{{ translate('Customer Name') }}</th>
                                                            <th>{{ translate('Address') }}</th>
                                                            <th>{{ translate('Phone') }}</th>
                                                            <th>{{ translate('Product Name') }}</th>
                                                            <th>{{ translate('Quantity') }}</th>
                                                            <th>{{ translate('Invoice') }}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($orderDetails as $key2 => $orderDetail)
                                                            @if($orderDetail and $orderDetail->product)
                                                                @if($orderDetail->product['added_by'] === 'seller' )
                                                                   <tr>
                                                                       <td>{{ $orderDetail->order->user['name'] }}</td>
                                                                       <td>{{ json_decode($orderDetail->order->shipping_address)->address }}</td>
                                                                       <td>{{ $orderDetail->order->user['phone'] }}</td>
                                                                       <td>{{ __($orderDetail->product['name']) }}</td>
                                                                       <td>{{ $orderDetail->quantity }}</td>
                                                                       <td>
                                                                           <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('order_customer_excel.download', ['orderDetail' => encrypt($orderDetail->id)])}}">
                                                                               <i class="las la-download"></i>
                                                                           </a>
                                                                       </td>
                                                                   </tr>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div>{{ translate('There are no orders') }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
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
