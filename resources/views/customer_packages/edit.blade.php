@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('6', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{translate('Update Package Information')}}</h3>
            </div>

            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" action="{{ route('customer_packages.update', $customer_package->id) }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PATCH">
            	@csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name">{{translate('Package Name')}}</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="{{translate('Name')}}" value="{{ $customer_package->name }}" id="name" name="name" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                            <div class="text text-danger"
                                 style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name">{{translate('Amount')}}</label>
                        <div class="col-sm-10">
                            <input type="number" min="0" step="0.01" placeholder="{{translate('Amount')}}" value="{{ $customer_package->amount }}" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" required>
                            @error('amount')
                            <div class="text text-danger"
                                 style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name">{{translate('Product Upload')}}</label>
                        <div class="col-sm-10">
                            <input type="number" min="0" step="1" placeholder="{{translate('Product Upload')}}" value="{{ $customer_package->product_upload }}" id="product_upload" name="product_upload" class="form-control @error('product_upload') is-invalid @enderror" required>
                            @error('product_upload')
                            <div class="text text-danger"
                                 style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="logo">{{translate('Package Logo')}}</label>
                        <div class="col-sm-10">
                            <input type="file" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror">
                            @error('logo')
                            <div class="text text-danger"
                                 style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                            @enderror
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
