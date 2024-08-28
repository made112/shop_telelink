@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('17', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading bord-btm clearfix pad-all h-100">
        <h3 class="panel-title pull-left pad-no">{{__('Refund Request All')}}</h3>
        <div class="pull-right clearfix">
            <form class="" id="sort_by_rating" action="{{ route('paid_refund') }}" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Enter Name Product') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{__('Order Id')}}</th>
                    <th>{{__('Seller Name')}}</th>
                    <th>{{__('Product')}}</th>
                    <th>{{__('Price')}}</th>
                    <th>{{__('Seller Approval')}}</th>
                    <th>{{__('Admin Approval')}}</th>
                    <th>{{__('Refund Status')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($refunds as $key => $refund)
                    <tr>
                        <td>{{ ($key+1) + ($refunds->currentPage() - 1)*$refunds->perPage() }}</td>
                        <td>{{ $refund->order->code }}</td>
                        <td>
                            @if ($refund->seller != null)
                                {{ $refund->seller->name }}
                            @endif
                        </td>
                        <td>
                            @if ($refund->orderDetail != null && $refund->orderDetail->product != null)
                                <a href="{{ route('product', $refund->orderDetail->product->slug) }}" target="_blank" class="media-block">
                                    <div class="media-left">
                                        <img loading="lazy"  class="img-md" src="{{ my_asset($refund->orderDetail->product->thumbnail_img)}}" alt="Image">
                                    </div>
                                    <div class="media-body">{{ __($refund->orderDetail->product->name) }}</div>
                                </a>
                            @endif
                        </td>
                        <td>
                            @if ($refund->orderDetail != null)
                                {{single_price($refund->orderDetail->price)}}
                            @endif
                        </td>
                        <td>
                            @if ($refund->seller_approval == 1)
                                <div class="label label-table label-success">
                                    {{__('Approved')}}
                                </div>
                            @else
                                <div class="label label-table label-warning">
                                    {{__('Pending')}}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if ($refund->admin_approval == 1)
                                <div class="label label-table label-success">
                                    {{__('Approved')}}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if ($refund->refund_status == 1)
                                <div class="label label-table label-success">
                                    {{__('Paid')}}
                                </div>
                            @else
                                <div class="label label-table label-warning">
                                    {{__('Non-Paid')}}
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $refunds->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
@endif
