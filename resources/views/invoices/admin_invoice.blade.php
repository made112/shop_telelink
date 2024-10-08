<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <style media="all">
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

        .currency {

        }
    </style>
</head>
<body>
<div style="margin-left:auto;margin-right:auto;">

    @php
        $generalsetting = \App\GeneralSetting::first();
    @endphp

    <div style="background: #eceff4;padding: 1.5rem;">
        <table>
            <tr>
                <td>
                    @if($generalsetting->logo != null)
                        <img loading="lazy" src="{{ my_asset($generalsetting->logo) }}" height="120"
                             style="display:inline-block;">
                    @else
                        <img loading="lazy" src="{{ my_asset('frontend/images/logo/logo.png') }}" height="120"
                             style="display:inline-block;">
                    @endif
                </td>
                <td style="font-size: 2.5rem;" class="text-right strong">{{ translate('INVOICE') }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="font-size: 1.2rem;" class="strong">{{ $generalsetting->site_name }}</td>
                <td class="text-right"></td>
            </tr>
            <tr>
                <td class="gry-color small">{{ $generalsetting->address }}</td>
                <td class="text-right"></td>
            </tr>
            <tr>
                <td class="gry-color small">{{ translate('Email') }}: {{ $generalsetting->email }}</td>
                <td class="text-right small"><span class="gry-color small">{{ translate('Order ID') }}:</span> <span
                        class="strong">{{ $order->code }}</span></td>
            </tr>
            <tr>
                <td class="gry-color small">Phone: {{ $generalsetting->phone }}</td>
                <td class="text-right small"><span class="gry-color small">{{ translate('Order Date') }}:</span> <span
                        class=" strong">{{ date('d-m-Y', $order->date) }}</span></td>
            </tr>
        </table>

    </div>


    <div style="padding: 1.5rem;padding-bottom: 0">
        <table>
            @php
                $shipping_address = json_decode($order->shipping_address);
            @endphp
            <tr>
                <td class="strong small gry-color">Bill to:</td>
            </tr>
            <tr>
                <td class="strong">{{ isset($shipping_address->name) ? $shipping_address->name : '' }}</td>
            </tr>
            <tr>
                <td class="gry-color small">{{ translate('City') }}
                    : {{ isset(json_decode($order->shipping_address)->city) ? getNameCity(json_decode($order->shipping_address)->city) : '' }}</td>
            </tr>
            <tr>
                <td class="gry-color small">{{ isset($shipping_address->address) ? $shipping_address->address : '' }}
                    , {{ isset($shipping_address->country) ? $shipping_address->country : '' }}</td>
            </tr>
            <tr>
                <td class="gry-color small">{{ translate('Email') }}
                    : {{ isset($shipping_address->email) ? $shipping_address->email : '' }}</td>
            </tr>
            <tr>
                <td class="gry-color small">{{ translate('Phone') }}
                    : {{ isset($shipping_address->phone) ? $shipping_address->phone : '' }}</td>
            </tr>
        </table>
    </div>

    <div style="padding: 1.5rem;">
        <table class="padding text-left small border-bottom">
            <thead>
            <tr class="gry-color" style="background: #eceff4;">
                <th width="35%">{{ translate('Product Name') }}</th>
                <th width="15%">{{ translate('Delivery Type') }}</th>
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
                        <td>{{ $orderDetail->product->name }} ({{ $orderDetail->variation }})</td>
                        <td>
                            @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                {{ translate('Home Delivery') }}
                            @elseif ($orderDetail->shipping_type == 'pickup_point')
                                @if ($orderDetail->pickup_point != null)
                                    {{ $orderDetail->pickup_point->name }} ({{ translate('Pickip Point') }})
                                @endif
                            @endif
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

    <div style="padding:0 1.5rem;">
        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
            <tbody>
            <tr>
                <th class="gry-color text-left">{{ translate('Sub Total') }}</th>
                <td class="currency">{{ single_price($order->orderDetails->sum('price')) }}</td>
            </tr>
            <tr>
                <th class="gry-color text-left">{{ translate('Shipping Cost') }}</th>
                <td class="currency">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</td>
            </tr>
            <tr class="border-bottom">
                <th class="gry-color text-left">{{ translate('Total Tax') }}</th>
                <td class="currency">{{ single_price($order->orderDetails->sum('tax')) }}</td>
            </tr>
            <tr>
                <th class="text-left strong">{{ translate('Grand Total') }}</th>
                <td class="currency">{{ single_price($order->grand_total) }}</td>
            </tr>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
