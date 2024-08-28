@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="pad-all text-center clearfix">
        <form class="" style="display: inline-block" action="{{ route('stock_report.index') }}" method="GET">
            <div class="box-inline mar-btm pad-rgt">
                 {{ translate('Sort by Category') }}:
                 <div class="select">
                     <select id="demo-ease" class="demo-select2" name="category_id" required>
                         @foreach (\App\Category::all() as $key => $category)
                             <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                         @endforeach
                     </select>
                 </div>
            </div>
            <button class="btn btn-default" type="submit">{{ translate('Filter') }}</button>
        </form>
        <div class="select clearfix float-right mar-lft" style="width: 150px;">
            <form id="formExport" action="{{ route('stock_report.download')}}" method="GET">
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
                <h3 class="panel-title">{{ translate('Product wise stock report') }}</h3>
            </div>

            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no demo-dt-basic">
                        <thead>
                            <tr>
                                <th>{{ translate('Product Name') }}</th>
                                <th>{{ translate('Stock') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $key => $product)
                                @php
                                    $qty = 0;
                                    if ($product->variant_product) {
                                        foreach ($product->stocks as $key => $stock) {
                                            $qty += $stock->qty;
                                        }
                                    }
                                    else {
                                        $qty = $product->current_stock;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ __($product->name) }}</td>
                                    <td>{{ $qty }}</td>
                                </tr>
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

    </script>
@endsection
@endif
