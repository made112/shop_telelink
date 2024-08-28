@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('23', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
    <div class="panel">
        <!--Panel heading-->
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{ translate('OTP Logger') }}</h3>
        </div>


        <div class="panel-body">
            <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th width="20%">{{translate('Request')}}</th>
                    <th>{{translate('Response')}}</th>
                    <th>{{translate('Date')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($loggers as $key => $log)
                    <tr>
                        <td>{{ ($key+1) + ($loggers->currentPage() - 1)*$loggers->perPage() }}</td>
                        <td>
                            {{ $log->request_content }}
                        </td>
                        <td>{{ $log->response_content }}</td>

                        <td>{{ date('Y-m-d H:i:s', strtotime($log->created_at)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $loggers->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
@endif
