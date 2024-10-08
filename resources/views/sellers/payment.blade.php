@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">{{ \App\Seller::find($payments->first()->seller_id)->user->name }} ({{ \App\Seller::find($payments->first()->seller_id)->user->shop->name }})</h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-bordered demo-dt-basic" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Date')}}</th>
                    <th>{{translate('Amount')}}</th>
                    <th>{{ translate('Payment Method') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $key => $payment)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $payment->created_at }}</td>
                        <td>
                            {{ single_price($payment->amount) }}
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} @if ($payment->txn_code != null) (TRX ID : {{ $payment->txn_code }}) @endif</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection
@endif
