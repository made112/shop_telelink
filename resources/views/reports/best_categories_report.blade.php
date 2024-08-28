@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="pad-all text-center clearfix">
        <div class="select clearfix float-right mar-lft" style="width: 150px;">
            <form id="formExport" action="{{ route('best_categories.download')}}" method="GET">
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
                <h3 class="panel-title">{{ translate('Best Category Report') }}</h3>
            </div>

            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no demo-dt-basic order-column cell-border compact stripe">
                        <thead>
                        <tr>
                            <th>{{ translate('Category Name') }}</th>
                            <th>{{ translate('Number of Sale') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($categories) > 0)
                        @foreach ($categories as $key => $category)
                            @if($category != null)
                                <tr>
                                    <td>{{ $category['name'] }}</td>
                                    <td>
                                        {{ $category['num_of_sale'] }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @endif
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
    </script>
@endsection
@endif
