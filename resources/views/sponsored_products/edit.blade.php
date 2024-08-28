@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="col-sm-12">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Sponsored Product Information')}}</h3>
        </div>

        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('sponsored_products.update', $sponsored_product->id) }}" method="POST" enctype="multipart/form-data">
        	@csrf
            <input type="hidden" name="_method" value="PATCH">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{translate('Title')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Title')}}" id="name" name="title" value="{{ $sponsored_product->title }}" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="sub_title">{{translate('Sub Title')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Sub Title')}}" id="sub_title" name="sub_title" value="{{ $sponsored_product->sub_title }}" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="banner">{{translate('Banner')}} <small>(1920x500)</small></label>
                    <div class="col-sm-9">
                        <input type="file" id="banner" name="banner" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="start_date">{{translate('Date')}}</label>
                    <div class="col-sm-9">
                        <div id="demo-dp-range">
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="form-control" name="start_date" value="{{ date('m/d/Y', $sponsored_product->start_date) }}">
                                <span class="input-group-addon">{{translate('to')}}</span>
                                <input type="text" class="form-control" name="end_date" value="{{ date('m/d/Y', $sponsored_product->end_date) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="products">{{translate('Products')}}</label>
                    <div class="col-sm-9">
                        <select name="product" id="products" class="form-control demo-select2" required data-placeholder="{{ translate('Choose Products') }}">
                            @foreach(\App\Product::all() as $product)
                                <option value="{{$product->id}}" <?php if($product->id == $sponsored_product->product_id) echo "selected";?> >{{__($product->name)}}</option>
                            @endforeach
                        </select>
                    </div>
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
