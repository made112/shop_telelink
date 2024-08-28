@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="pad-all text-center clearfix">
        <div class="select clearfix float-right mar-lft" style="width: 150px;">
            <form id="formExport" action="{{ route('preferred_products.download')}}" method="GET">
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
                <h3 class="panel-title">{{ translate('Preferred Products Report') }}</h3>
            </div>

            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no demo-dt-basic">
                        <thead>
                        <tr>
                            <th>{{ translate('Product Name') }}</th>
                            <th>{{ translate('Number of Sale') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (filter_products(\App\Product::where('published', 1)->orderBy('num_of_sale', 'desc'))->limit(20)->get() as $key => $product)
                            @if($product != null)
                                <tr>
                                    <td><a href="{{ route('product', $product->slug) }}" target="_blank" class="media-block d-flex align-items-center">
                                            <div class="media-left">
                                                <img
                                                    class="img-md lazyload"
                                                    loading="lazy"
                                                    src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                    data-src="{{ my_asset($product->thumbnail_img) }}"
                                                    onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                                    alt="{{  __($product->name) }}">
                                            </div>
                                            <div class="media-body">{{ __($product->name) }}</div>
                                        </a></td>
                                    <td>{{ $product->num_of_sale }}</td>
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
    </script>
@endsection
@endif
