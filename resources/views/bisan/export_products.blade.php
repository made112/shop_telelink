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
                        <form id="formExport" action="{{ route('products_bisan_export.download')}}" method="GET">
                            @csrf
                            <button name="export" class="btn btn-default">
                                <img src="{{my_asset('frontend/images/excel.png')}}" style="width: 25px;margin-right: 5px">
                                <span>{{ translate('Export Products') }}</span>
                            </button>
                        </form>
                    </div>
                </h3>
            </div>
            <hr>
            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no demo-dt-basic dataTable display nowrap responsive" style="width: 100%;">
                        <thead>
                        <tr>
                            <th></th>
                            <th>{{ translate('Product Name') }}</th>
                            <th width="100%" class="none">{{ translate('Customer Info With Quantity') }}</th>
                            <th class="">{{ translate('Total Quantity') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $key => $product)
                            @if($product)
                                @php
                                $total_quantity = 0;
                                @endphp
                                <tr style="width: 100%;">
                                    <td></td>
                                    <td>{{ __($product['name']) }}</td>
                                    <td class="none">
                                        @if(count($product->orderDetails) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped mar-no demo-dt-basic dataTable no-footer dtr-inline display responsive nowrap w-100">
                                                <thead>
                                                    <tr>
                                                        <th>{{ translate('Customer Name') }}</th>
                                                        <th>{{ translate('Address') }}</th>
                                                        <th>{{ translate('Quantity') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($product->orderDetails as $key2 => $orderDetail)
                                                        @php
                                                            $total_quantity = $total_quantity + intval($orderDetail->quantity);
                                                        @endphp
                                                        @if($orderDetail->product['added_by'] === 'seller' )
                                                        <tr>
                                                            <td>{{ __($orderDetail->order->user['name']) }}</td>
                                                            <td>{{ __(json_decode($orderDetail->order->shipping_address)->address) }}</td>
                                                            <td>{{ $orderDetail->quantity }}</td>
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size: 15px;font-weight: bolder">
                                            {{ $total_quantity }}
                                        </span>
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
