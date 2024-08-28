@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="col-sm-12">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Sponsored Information')}}</h3>
        </div>

        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('sponsored_products.store') }}" method="POST" enctype="multipart/form-data">
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
                    <label class="col-sm-3 control-label" for="sub_title">{{translate('Sub Title')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Sub Title')}}" id="sub_title" name="sub_title" class="form-control @error('sub_title') is-invalid @enderror" >
                        @error('sub_title')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ $message }}</div>
                        @enderror
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
                        <select name="product" id="products" class="form-control demo-select2" required data-placeholder="{{ translate('Choose Products') }}">
                            @foreach(\App\Product::all() as $product)
                                <option value="{{$product->id}}">{{__($product->name)}}</option>
                            @endforeach
                        </select>
                        @error('product')
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
@endif
