@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">{{translate('Seller Payments')}}</h3>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-responsive mar-no" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{translate('Date')}}</th>
                        <th>{{translate('Seller')}}</th>
                        <th>{{translate('Amount')}}</th>
                        <th>{{ translate('Payment Method') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $key => $payment)
                        @if (\App\Seller::find($payment->seller_id) != null && \App\Seller::find($payment->seller_id)->user != null)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $payment->created_at }}</td>
                                <td>
                                    @if (\App\Seller::find($payment->seller_id) != null)
                                        {{ \App\Seller::find($payment->seller_id)->user->name }} ({{ \App\Seller::find($payment->seller_id)->user->shop->name }})
                                    @endif
                                </td>
                                <td>
                                    {{ single_price($payment->amount) }}
                                </td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} @if ($payment->txn_code != null) (TRX ID : {{ $payment->txn_code }}) @endif</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@endif
