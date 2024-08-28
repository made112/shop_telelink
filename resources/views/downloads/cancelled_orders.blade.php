<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style media="all">
        body { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body>
<div style="margin-left:auto;margin-right:auto;">
    <style media="all">
        *{
            margin: 0;
            padding: 0;
            line-height: 1.5;
            font-family: sans-serif;
            color: #333542;
            font-family: 'dejavu sans', sans-serif;
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
    @endphp

    <div style="background: #eceff4;padding: 1.5rem;">
        <table>
            <tr>
                <td>
                    @if($generalsetting->logo != null)
                        <img src="{{ my_asset($generalsetting->logo) }}" height="120" style="display:inline-block;">
                    @else
                        <img src="{{ my_asset('frontend/images/logo/logo.png') }}" height="120" style="display:inline-block;">
                    @endif
                </td>
            </tr>
        </table>

    </div>

    <div style="border-bottom:1px solid #eceff4;margin: 0 1.5rem;"></div>

    <div style="padding: 1.5rem;">
        <table class="padding text-left small border-bottom">
            <thead>
            <tr>
                <th>{{translate('Order Code')}}</th>
                <th>{{translate('Num. of Products')}}</th>
                <th>{{translate('Customer')}}</th>
                <th>{{translate('Amount')}}</th>
                <th>{{translate('Delivery Status')}}</th>
                <th>{{translate('Payment Method')}}</th>
                <th>{{translate('Payment Status')}}</th>
            </tr>
            </thead>
            <tbody class="strong">
            @if($orders != null)
                @foreach ($orders as $key => $order)
                    @if($order != null)
                        <tr>
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
</body>
</html>
