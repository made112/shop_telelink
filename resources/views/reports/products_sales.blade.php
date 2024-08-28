@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
    @php
        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
    @endphp
    <div class="pad-all text-center clearfix">
        <div class="select clearfix float-right mar-lft" style="width: 150px;">
            <form id="formExport" action="{{ route('products_sales.download')}}" method="GET">
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
                <h3 class="panel-title">{{ translate('Best Category Report') }}</h3>
            </div>

            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no demo-dt-basic order-column cell-border compact stripe">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Order Code') }}</th>
                            <th>{{ translate('Num. of Products') }}</th>
                            <th>{{ translate('Customer') }}</th>
                            <th>{{ translate('Amount') }}</th>
                            <th>{{ translate('Delivery Status') }}</th>
                            <th>{{ translate('Payment Status') }}</th>
                            @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                <th width="20%">{{ translate('Refund') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($sales) > 0)
                            @foreach ($sales as $key => $sale)
                                @if($sale != null)
                                    <tr>
                                        <td>
                                            {{ ($key+1) }}
                                        </td>
                                        <td>
                                            {{ $sale->code }}
                                        </td>
                                        <td>
                                            {{ count($sale->orderDetails) }}
                                        </td>
                                        <td>
                                            @if ($sale->user != null)
                                                {{ $sale->user->name }}
                                            @else
                                                Guest ({{ $sale->guest_id }})
                                            @endif
                                        </td>
                                        <td>
                                            {{ single_price($sale->grand_total) }}
                                        </td>
                                        <td>
                                            @php
                                                $status = 'Delivered';
                                                foreach ($sale->orderDetails as $key => $saleDetail) {
                                                    if($saleDetail->delivery_status != 'delivered'){
                                                        $status = 'Pending';
                                                    }
                                                }
                                            @endphp
                                            {{ $status }}
                                        </td>
                                        <td>
                            <span class="badge badge--2 mr-4">
                                @if ($sale->payment_status == 'paid')
                                    <i class="bg-green"></i> {{ translate('Paid') }}
                                @else
                                    <i class="bg-red"></i> {{ translate('Unpaid') }}
                                @endif
                            </span>
                                        </td>
                                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                            <td>
                                                @if (count($sale->refund_requests) > 0)
                                                    {{ count($sale->refund_requests) }} {{ translate('Refund') }}
                                                @else
                                                    {{ translate('No Refund') }}
                                                @endif
                                            </td>
                                        @endif
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
