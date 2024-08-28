@php
$locale = config('app.locale');
$dir = 'ltr';
if ($locale === 'sa') {
    $dir = 'rtl';
}
@endphp
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
            <tr class="gry-color" style="background: #eceff4;">
                <th style="width: 50%">{{ translate('Product Image') }}</th>
                <th style="width: 50%">{{ translate('Product Name') }}</th>
                <th style="width: 50%">{{ translate('Number of Sale') }}</th>
            </tr>
            </thead>
            <tbody class="strong">
            @foreach ($products as $key => $product)
                @if($product != null)
                    <tr>
                        <td style="text-align: center">
                            <img
                                class="img-md"
                                height="80"
                                width="80"
                                src="{{ my_asset($product->thumbnail_img) }}"
                                alt="{{  __($product->name) }}" />
                        </td>
                        <td>{{ __($product->name) }}</td>
                        <td style="text-align: center">{{ $product->num_of_sale }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
