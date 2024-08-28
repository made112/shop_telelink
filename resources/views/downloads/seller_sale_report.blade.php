<div style="margin-left:auto;margin-right:auto;">
    <style media="all">
        *{
            margin: 0;
            padding: 0;
            line-height: 1.5;
            font-family: 'Dejavu Sans', sans-serif;
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
                <th style="width: 25%">{{ translate('Seller Name') }}</th>
                <th style="width: 25%">{{ translate('Shop Name') }}</th>
                <th style="width: 25%">{{ translate('Number of Product Sale') }}</th>
                <th style="width: 25%">{{ translate('Order Amount') }}</th>
            </tr>
            </thead>
            <tbody class="strong">
            @foreach ($sellers as $key => $seller)
                @if($seller->user != null)
                    <tr>
                        <td>{{ $seller->user->name }}</td>
                        <td>{{ $seller->user->shop->name }}</td>
                        <td>
                            @php
                                $num_of_sale = 0;
                                foreach ($seller->user->products as $key => $product) {
                                    $num_of_sale += $product->num_of_sale;
                                }
                            @endphp
                            {{ $num_of_sale }}
                        </td>
                        <td>
                            {{ single_price(\App\OrderDetail::where('seller_id', $seller->user->id)->sum('price')) }}
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

</div>
