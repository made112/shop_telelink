<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="image/x-icon" href="{{ my_asset(\App\GeneralSetting::first()->favicon) }}" rel="shortcut icon"/>
    <title>Laravel</title>
    <meta charset="UTF-8">
    <style media="all">
        .text-center {
            text-align: center;
        }

        * {
            margin: 0;
            padding: 5px 0;
            line-height: 1.5 !important;
            font-family: "dejavu sans";
            color: #333542;
        }

        body {
            font-family: "dejavu sans";
            font-size: 16px;
        }

        .gry-color *,
        .gry-color {
            color: #878f9c;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: 8px 11.2px;
        }

        table.padding td {
            padding: 11.2px;
        }

        table.sm-padding td {
            padding: 3.2px 11.2px;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .small {
            font-size: 13.6px;
        }
    </style>

</head>
<body>
<div style="margin-left:auto;margin-right:auto;">

    @php
        $generalsetting = \App\GeneralSetting::first();
    @endphp
    <div style="background: #eceff4;padding: 24px;">
        <table>
            <tr style="width: 100%;">
                <td style="font-size: 40px;padding-top: 40px;width: 80%;" class="text-left strong">فاتورة</td>
                <td class="text-right">
                    @if($generalsetting->logo != null)
                        <img loading="lazy" src="{{ my_asset($generalsetting->logo) }}" height="120"
                             style="display:inline-block;">
                    @else
                        <img loading="lazy" src="{{ my_asset('frontend/images/logo/logo.png') }}" height="120"
                             style="display:inline-block;">
                    @endif
                </td>
            </tr>
        </table>
        <table style="width: 100%;display: block;">
            <tr style="width: 100%;">
                <td class="text-right" style=""></td>
                <td style="font-size: 19.2px;width: 60%;"
                    class="strong text-right">{{ $generalsetting->site_name }}</td>
            </tr>
            <tr>
                <td class="text-right"></td>
                <td class="gry-color small text-right">
                    @if(strlen($generalsetting->address) > 30)
                        {{ substr($generalsetting->address, 0, 30) }}...
                    @else
                        {{$generalsetting->address}}
                    @endif

                </td>
            </tr>
            <tr>
                <td class="text-right small" style="text-align: right"><span class="gry-color small">
                    </span> <span class="strong">{{ $order->code }}</span>
                    رقم الطلبية:
                </td>
                <td class="gry-color small text-right" style="width: 50%;">
                    <span>{{ $generalsetting->email }}</span>
                    {{ translate('Email') }}:
                </td>
            </tr>
            <tr>
                <td class="text-right small"><span class="gry-color small">
                    </span> <span class=" strong">{{ date('d-m-Y', $order->date) }}</span>
                    {{ translate('Order Date') }}:
                </td>
                <td class="gry-color text-right small">
                    <span>{{ $generalsetting->phone }}</span>
                    {{ translate('Phone') }}:
                </td>
            </tr>
        </table>

    </div>

    <div style="padding: 24px;padding-bottom: 0">
        <table>
            @php
                $shipping_address = json_decode($order->shipping_address);
            @endphp
            <tr style="width: 90%;text-align: right">
                <td style="width: 100%;" class="strong small gry-color">فاتورة إلى:</td>
            </tr>
            <tr>
                <td class="strong text-right">{{ isset($shipping_address->name) ? $shipping_address->name : '' }}</td>
            </tr>
            <tr>
                <td class="gry-color small text-right">
                    <span>{{ isset(json_decode($order->shipping_address)->city) ? getNameCity(json_decode($order->shipping_address)->city) : '' }}</span>
                    {{ translate('City') }}:
                </td>
            </tr>
            <tr>
                <td class="gry-color small text-right">{{ isset($shipping_address->address) ? $shipping_address->address : '' }}
                    , {{ isset($shipping_address->country) ? $shipping_address->country : '' }}</td>
            </tr>
            <tr>
                <td class="gry-color small text-right">
                    <span>{{ isset($shipping_address->email) ? $shipping_address->email : '' }}</span>
                    {{ translate('Email') }}:
                </td>
            </tr>
            <tr>
                <td class="gry-color small text-right">
                    <span>{{ isset($shipping_address->phone) ? $shipping_address->phone : '' }}</span>
                    {{ translate('Phone') }}:
                </td>
            </tr>
        </table>
    </div>

    <div style="padding: 24px;width: 100%;">
        <table class="padding text-left small border-bottom" style="width: 100%;">
            <thead>
            <tr class="gry-color" style="background: #eceff4;width: 100%;">
                <th width="35%">{{ translate('Product Name') }}</th>
                <th width="15%">{{ translate('Delivery Type') }}</th>
                <th>{{translate('Payment Method')}}</th>
                <th width="10%">{{ translate('Qty') }}</th>
                <th width="15%">{{ translate('Unit Price') }}</th>
                <th width="10%">{{ translate('Tax') }}</th>
                <th width="15%" class="text-right">{{ translate('Total') }}</th>
            </tr>
            </thead>
            <tbody class="strong">
            @foreach ($order->orderDetails as $key => $orderDetail)
                @if ($orderDetail->product != null)
                    <tr class="">
                        <td>
                            @if(strlen($orderDetail->product->name) > 30)
                                <span>{{ substr($orderDetail->product->name, 0 , 30). '...' }}</span>
                            @else
                                <span>{{ $orderDetail->product->name }}</span>
                            @endif
                            @if(isset($orderDetail->variation) && $orderDetail->variation !== '') <span>({{ $orderDetail->variation }})</span> @endif
                        </td>
                        <td>
                            @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                {{ translate('Home Delivery') }}
                            @elseif ($orderDetail->shipping_type == 'pickup_point')
                                @if ($orderDetail->pickup_point != null)
                                    {{ $orderDetail->pickup_point->name }} ({{ translate('Pickip Point') }})
                                @endif
                            @endif
                        </td>
                        <td>
                            {{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}
                        </td>
                        <td class="gry-color">{{ $orderDetail->quantity }}</td>
                        <td class="gry-color currency">{{ single_price($orderDetail->price/$orderDetail->quantity) }}</td>
                        <td class="gry-color currency">{{ single_price($orderDetail->tax/$orderDetail->quantity) }}</td>
                        <td class="text-right currency">{{ single_price($orderDetail->price+$orderDetail->tax) }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="padding:0 24px;">
        <table style="width: 100%;margin-right:auto;" class="text-right sm-padding small strong">
            <tbody>
            <tr>
                <td class="currency" style="width: 77%">{{ single_price($order->orderDetails->sum('price')) }}</td>
                <th class="gry-color text-right">{{ translate('Sub Total') }}</th>
            </tr>
            <tr>
                <td class="currency">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</td>
                <th class="gry-color text-right">تكلفة الشحن</th>
            </tr>
            <tr>
                <td class="currency">{{ single_price($order->orderDetails->sum('tax')) }}</td>
                <th class="gry-color text-right">اجمالي الضريبة</th>
            </tr>
            <tr>
                <td class="currency">{{ single_price($order->grand_total) }}</td>
                <th class="text-right strong" style="border-top: 1px solid #eceff4;">المجموع الكلي</th>
            </tr>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
