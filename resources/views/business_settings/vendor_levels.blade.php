@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
    @php
    $bronze_level = \App\BusinessSetting::where('type', 'BRONZE_LEVEL')->first();
    $silver_level = \App\BusinessSetting::where('type', 'SILVER_LEVEL')->first();
    $gold_level = \App\BusinessSetting::where('type', 'GOLD_LEVEL')->first();
    $diamond_level = \App\BusinessSetting::where('type', 'DIAMOND_LEVEL')->first();
    @endphp
    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <!--Horizontal Form-->
                <div class="panel-heading">
                    <h3 class="panel-title">{{ translate('Seller Levels')}}</h3>
                </div>
                <form class="form-horizontal" action="{{ route('business_settings.vendor_levels.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="panel-body">
                        <div class="form-group">
                            <input type="hidden" name="type_bronze" value="BRONZE_LEVEL">
                            <label class="col-lg-3 control-label">{{ translate('Bronze Level') }}</label>
                            <div class="col-lg-8">
                                <input type="number" min="0" value="@if(isset($bronze_level)){{ $bronze_level->value }}@endif" placeholder="{{translate('Bronze Level')}}" name="bronze" class="form-control @error('bronze') is-invalid @enderror">
                                @error('bronze')
                                <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type_silver" value="SILVER_LEVEL">
                            <label class="col-lg-3 control-label">{{ translate('Silver Level') }}</label>
                            <div class="col-lg-8">
                                <input type="number" min="0" value="@if(isset($silver_level)){{ $silver_level->value }}@endif" placeholder="{{translate('Silver Level')}}" name="silver" class="form-control @error('silver') is-invalid @enderror">
                                @error('silver')
                                <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type_gold" value="GOLD_LEVEL">
                            <label class="col-lg-3 control-label">{{ translate('Gold Level') }}</label>
                            <div class="col-lg-8">
                                <input type="number" min="0" value="@if(isset($gold_level)){{ $gold_level->value }}@endif" placeholder="{{translate('Gold Level')}}" name="gold" class="form-control @error('gold') is-invalid @enderror">
                                @error('gold')
                                <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type_diamond" value="DIAMOND_LEVEL">
                            <label class="col-lg-3 control-label">{{ translate('Diamond Level') }}</label>
                            <div class="col-lg-8">
                                <input type="number" min="0" value="@if(isset($diamond_level)){{ $diamond_level->value }}@endif" placeholder="{{translate('Diamond Level')}}" name="diamond" class="form-control @error('diamond') is-invalid @enderror">
                                @error('diamond')
                                <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
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

    </div>

@endsection
@endif
