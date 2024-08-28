@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('pick_up_points.create')}}" class="btn btn-rounded btn-info pull-right">{{translate('Add New Pick-up Point')}}</a>
    </div>
</div>

<br>

<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">{{translate('Pick-up Point')}}</h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Manager')}}</th>
                    <th>{{translate('Location')}}</th>
                    <th>{{translate('Pickup Station Contact')}}</th>
                    <th>{{translate('Status')}}</th>
                    {{-- <th>{{translate('Cash On Pickup')}}</th> --}}
                    <th width="15%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pickup_points as $key => $pickup_point)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$pickup_point->name}}</td>
                        @if ($pickup_point->staff != null && $pickup_point->staff->user != null)
                            <td>{{$pickup_point->staff->user->name}}</td>
                        @else
                            <td><div class="label label-table label-danger">
                                {{ translate('No Manager') }}
                            </div></td>
                        @endif
                        <td>{{$pickup_point->address}}</td>
                        <td>{{$pickup_point->phone}}</td>
                        <td>
                            @if ($pickup_point->pick_up_status != 1)
                                <div class="label label-table label-danger">
                                    {{ translate('Close') }}
                                </div>
                            @else
                                <div class="label label-table label-success">
                                    {{ translate('Open') }}
                                </div>
                            @endif
                        </td>
                        <td>
{{--                            <div class="btn-group dropdown">--}}
{{--                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">--}}
{{--                                    {{translate('Actions')}} <i class="dropdown-caret"></i>--}}
{{--                                </button>--}}
{{--                                <ul class="dropdown-menu dropdown-menu-right">--}}
{{--                                    <li><a href="{{route('pick_up_points.edit', encrypt($pickup_point->id))}}">{{translate('Edit')}}</a></li>--}}
{{--                                    <li><a onclick="confirm_modal('{{route('pick_up_points.destroy', $pickup_point->id)}}');">{{translate('Delete')}}</a></li>--}}
{{--                                </ul>--}}
{{--                            </div>--}}
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('pick_up_points.edit', encrypt($pickup_point->id))}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a onclick="confirm_modal('{{route('pick_up_points.destroy', $pickup_point->id)}}');" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('pick_up_points.destroy', $pickup_point->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $pickup_points->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
@endif
