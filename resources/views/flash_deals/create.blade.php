@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="col-sm-12">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Flash Deal Information')}}</h3>
        </div>

        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('flash_deals.store') }}" method="POST" enctype="multipart/form-data">
        	@csrf
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{translate('Title')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Title')}}" id="name" name="title" class="form-control @error('title') is-invalid @enderror" >
                        @error('title')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="background_color">{{translate('Background Color')}} <small>(Hexa-code)</small></label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('#FFFFFF')}}" id="background_color" name="background_color" class="form-control" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label" for="name">{{translate('Text Color')}}</label>
                    <div class="col-lg-9">
                        <select name="text_color" id="text_color" class="form-control demo-select2" >
                            <option value="">{{translate('Select One')}}</option>
                            <option value="white">{{translate('White')}}</option>
                            <option value="dark">{{translate('Dark')}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="banner">{{translate('Banner')}} <small>(1920x500)</small></label>
                    <div class="col-sm-9">
                        <input type="file" id="banner" name="banner" class="form-control @error('banner') is-invalid @enderror">
                        @error('banner')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="start_date">{{translate('Date')}}</label>
                    <div class="col-sm-9">
                        <div id="demo-dp-range">
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="form-control @error('start_date') is-invalid @enderror" name="start_date">
                                @error('start_date')
                                <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ $message }}</div>
                                @enderror
                                <span class="input-group-addon">{{translate('to')}}</span>
                                <input type="text" class="form-control @error('end_date') is-invalid @enderror" name="end_date">
                                @error('end_date')
                                <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="col-sm-3 control-label" for="products">{{translate('Products')}}</label>
                    <div class="col-sm-9">
                        <select name="products[]" id="products" class="form-control @error('Products') is-invalid @enderror  demo-select2" multiple  data-placeholder="{{ translate('Choose Products') }}">
                            @foreach(\App\Product::all() as $product)
                                <option value="{{$product->id}}">{{__($product->name)}}</option>
                            @endforeach
                        </select>
                        @error('products')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <br>
                <div class="form-group" id="discount_table">

                </div>
            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{translate('Save')}}</button>
            </div>
        </form>
        <!--===================================================-->
        <!--End Horizontal Form-->

    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $('#products').on('change', function(){
                var product_ids = $('#products').val();
                if(product_ids.length > 0){
                    $.post('{{ route('flash_deals.product_discount') }}', {_token:'{{ csrf_token() }}', product_ids:product_ids}, function(data){
                        $('#discount_table').html(data);
                        $('.demo-select2').select2();
                    });
                }
                else{
                    $('#discount_table').html(null);
                }
            });
        });
    </script>
@endsection
@endif
