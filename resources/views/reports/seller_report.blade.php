@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="pad-all text-center clearfix">
        <form class="" style="display: inline-block" action="{{ route('seller_report.index') }}" method="GET">
            <div class="box-inline mar-btm pad-rgt">
                 {{ translate('Sort by verification status') }}:
                 <div class="select">
                     <select class="demo-select2" name="verification_status" required>
                        <option value="1">{{ translate('Approved') }}</option>
                        <option value="0">{{ translate('Non Approved') }}</option>
                     </select>
                 </div>
            </div>
            <button class="btn btn-default" type="submit">{{ translate('Filter') }}</button>
        </form>

        <div class="select clearfix float-right mar-lft" style="width: 150px;">
            <form id="formExport" action="{{ route('seller_report.download')}}" method="GET">
                @csrf
                <select id="exportFiles" class="demo-select2" name="export" required>
                    <option value="">{{ translate('Select To Export') }}</option>
                    <option value="pdf">{{ translate('PDF') }}</option>
                    <option value="excel">{{ translate('Excel') }}</option>
                    <option value="word">{{ translate('Word') }}</option>
                </select>
            </form>
        </div>
    </div>


    <div class="col-md-offset-2 col-md-8">
        <div class="panel">
            <!--Panel heading-->
            <div class="panel-heading">
                <h3 class="panel-title">{{ translate('Seller report') }}</h3>
            </div>

            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no demo-dt-basic">
                        <thead>
                            <tr>
                                <th>{{ translate('Seller Name') }}</th>
                                <th>{{ translate('Email') }}</th>
                                <th>{{ translate('Shop Name') }}</th>
                                <th>{{ translate('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sellers as $key => $seller)
                                @if($seller->user != null)
                                    <tr>
                                        <td>{{ $seller->user->name }}</td>
                                        <td>{{ $seller->user->email }}</td>
                                        <td>{{ $seller->user->shop->name }}</td>
                                        <td>
                                            @if ($seller->verification_status == 1)
                                                <div class="label label-table label-success">
                                                    {{translate('Verified')}}
                                                </div>
                                            @elseif ($seller->verification_info != null)
                                                <a href="{{ route('sellers.show_verification_request', $seller->id) }}">
                                                    <div class="label label-table label-info">
                                                        {{translate('Requested')}}
                                                    </div>
                                                </a>
                                            @else
                                                <div class="label label-table label-danger">
                                                    {{translate('Not Verified')}}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        {{--Load Form Submit to export files--}}
        exportFile();

        function formatState (state) {
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "/user/pages/images/flags";
            var $state = $(
                '<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
            );
            return $state;
        };

        $(".exportFiles").select2({
            templateResult: formatState
        });
    </script>
@endsection
@endif
