@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{ route('sellers.create')}}" class="btn btn-rounded btn-info pull-right">{{translate('Add New Seller')}}</a>
        </div>
    </div>

    <br>

    <!-- Basic Data Tables -->
    <!--===================================================-->
    <div class="panel">
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{translate('Sellers')}}</h3>
            <div class="pull-right clearfix">
                <form class="" id="sort_sellers" action="" method="GET">
                    <div class="box-inline pad-rgt pull-left">
                        <div class="select" style="min-width: 300px;">
                            <select class="form-control demo-select2" name="approved_status" id="approved_status" onchange="sort_sellers()">
                                <option value="">{{translate('Filter by Approval')}}</option>
                                <option value="1"  @isset($approved) @if($approved == 'paid') selected @endif @endisset>{{translate('Approved')}}</option>
                                <option value="0"  @isset($approved) @if($approved == 'unpaid') selected @endif @endisset>{{translate('Non-Approved')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-inline pad-rgt pull-left">
                        <div class="" style="min-width: 200px;">
                            <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name or email & Enter') }}">
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
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Seller Phone')}}</th>
                    <th>{{translate('Email Address')}}</th>
                    <th>{{ translate('Earning Points') }}</th>
                    <th>{{ translate('Classification By Points') }}</th>
                    <th class="none">{{ translate('Commercial Interest') }}</th>
                    <th class="none">{{translate('Shop Phone')}}</th>
                    <th class="none">{{translate('Shop Address')}}</th>
                    <th class="none">{{translate('Verification Info')}}</th>
                    <th class="none">{{translate('Approval')}}</th>
                    <th class="none">{{ translate('Num. of Products') }}</th>
                    <th class="none">{{ translate('Due to seller') }}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sellers as $key => $seller)
                    @if($seller->user != null)
                        <tr>
                            <td>{{ ($key+1) + ($sellers->currentPage() - 1)*$sellers->perPage() }}</td>
                            <td>{{$seller->user->name}}</td>
                            <td>{{$seller->user->phone}}</td>
                            <td>{{$seller->user->email}}</td>
                            <td>
                                @php
                                $count = 0;
                                foreach (\App\ClubPoint::where('user_id', $seller->user->id)->get('points') as $club) {
                                    $count += intval($club->points);
                                }
                                echo $count.' '. translate('Point');
                                @endphp
                            </td>
                            <td>
                                {{ translate(convert_points_to_levels($count)) }}
                            </td>
                            <td>{{ isset($seller->user->shop->commercial_interest) ? $seller->user->shop->commercial_interest : null }}</td>
                            <td>{{ isset($seller->user->shop->phone) ? $seller->user->shop->phone : null }}</td>
                            <td>{{ isset($seller->user->shop->address) ? $seller->user->shop->address : null }}</td>
                            <td>
                                @if ($seller->verification_info != null)
                                    <a href="{{ route('sellers.show_verification_request', $seller->id) }}">
                                        <div class="label label-table label-info">
                                            {{translate('Show')}}
                                        </div>
                                    </a>
                                @endif
                            </td>
                            <td>
                                <label class="switch">
                                    <input onchange="update_approved(this)" value="{{ $seller->id }}" type="checkbox" <?php if($seller->verification_status == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>{{ \App\Product::where('user_id', $seller->user->id)->count() }}</td>
                            <td>
                                @if ($seller->admin_to_pay >= 0)
                                    {{ single_price($seller->admin_to_pay) }}
                                @else
                                    {{ single_price(abs($seller->admin_to_pay)) }} (Due to Admin)
                                @endif
                            </td>
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                        {{translate('Actions')}} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a onclick="show_seller_profile('{{$seller->id}}');">{{translate('Profile')}}</a></li>
                                        <li><a href="{{route('sellers.login', encrypt($seller->id))}}">{{translate('Log in as this Seller')}}</a></li>
                                        <li><a onclick="show_seller_payment_modal('{{$seller->id}}');">{{translate('Pay Now')}}</a></li>
                                        <li><a href="{{route('sellers.payment_history', encrypt($seller->id))}}">{{translate('Payment History')}}</a></li>
                                        <li><a href="{{route('sellers.edit', encrypt($seller->id))}}">{{translate('Edit')}}</a></li>
                                        <li><a onclick="confirm_modal('{{route('sellers.destroy', $seller->id)}}');">{{translate('Delete')}}</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $sellers->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">

            </div>
        </div>
    </div>

    <div class="modal fade" id="profile_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">

            </div>
        </div>
    </div>


@endsection

@section('script')
    <script type="text/javascript">
        function show_seller_payment_modal(id){
            $.post('{{ route('sellers.payment_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#payment_modal #modal-content').html(data);
                $('#payment_modal').modal('show', {backdrop: 'static'});
                $('.demo-select2-placeholder').select2();
            });
        }

        function show_seller_profile(id){
            $.post('{{ route('sellers.profile_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#profile_modal #modal-content').html(data);
                $('#profile_modal').modal('show', {backdrop: 'static'});
            });
        }

        function update_approved(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('sellers.approved') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    showAlert('success', 'Approved sellers updated successfully');
                }
                else{
                    showAlert('danger', 'Something went wrong');
                }
            });
        }

        function sort_sellers(el){
            $('#sort_sellers').submit();
        }
    </script>
@endsection
@endif
