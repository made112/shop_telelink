<div style="margin-left:auto;margin-right:auto;">
    <style media="all">
        *{
            margin: 0;
            padding: 0;
            line-height: 1.5;
            font-family: sans-serif;
            color: #333542;
        }
        div{
            font-size: 1rem;
        }
        .gry-color *,
        .gry-color{
            color:#878f9c;
        }
        table{
            width: 100%;
        }
        table th{
            font-weight: normal;
        }
        table.padding th{
            padding: .5rem .7rem;
        }
        table.padding td{
            padding: .7rem;
        }
        table.sm-padding td{
            padding: .2rem .7rem;
        }
        .border-bottom td,
        .border-bottom th{
            border-bottom:1px solid #eceff4;
        }
        .text-left{
            text-align:left;
        }
        .text-right{
            text-align:right;
        }
        .small{
            font-size: .85rem;
        }
        .strong{
            font-weight: bold;
        }
    </style>

    @php
        $generalsetting = \App\GeneralSetting::first();
        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
    @endphp

    <div style="background: #eceff4;padding: 1.5rem;">
        <table>
            <tr>
                <td>
                    @if($generalsetting->logo != null)
                        <img src="{{ my_asset($generalsetting->logo) }}" height="40" style="display:inline-block;">
                    @else
                        <img src="{{ my_asset('frontend/images/logo/logo.png') }}" height="40" style="display:inline-block;">
                    @endif
                </td>
            </tr>
        </table>

    </div>

    <div style="border-bottom:1px solid #eceff4;margin: 0 1.5rem;"></div>

    <div style="padding: 1.5rem;">
        <table class="padding text-left small border-bottom">
            <thead>
            <tr class="gry-color" style="background: #eceff4;">
                <th width="5%">#</th>
                <th width="10%">{{ translate('Order Code') }}</th>
                <th width="5%">{{ translate('Num. of Products') }}</th>
                <th width="15%">{{ translate('Customer') }}</th>
                <th width="10%">{{ translate('Amount') }}</th>
                <th width="15%">{{ translate('Delivery Status') }}</th>
                <th width="20%">{{ translate('Payment Status') }}</th>
                @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                    <th width="20%">{{ translate('Refund') }}</th>
                @endif
            </tr>
            </thead>
            <tbody class="strong">
            @foreach ($sales as $key => $sale)
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
            @endforeach
            </tbody>
        </table>
    </div>

</div>
