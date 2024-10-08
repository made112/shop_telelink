@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ translate('Attribute Information')}}</h3>
        </div>

        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('attributes.update', $attribute->id) }}" method="POST" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PATCH">
            @csrf
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">{{ translate('Name')}}</label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="{{ translate('Name')}}" id="name" name="name" class="form-control" required value="{{ $attribute->name }}">
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{ translate('Save')}}</button>
            </div>
        </form>
        <!--===================================================-->
        <!--End Horizontal Form-->

    </div>
</div>

@endsection
@endif
