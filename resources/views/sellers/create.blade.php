@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))

@section('content')

<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Seller Information')}}</h3>
        </div>

        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('sellers.store') }}" method="POST" enctype="multipart/form-data">
        	@csrf
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{translate('Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control @error('name') is-invalid @enderror" >
                        @error('name')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="email">{{translate('Email Address')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Email Address')}}" id="email" name="email" class="form-control @error('email') is-invalid @enderror" >
                        @error('email')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="phone">{{translate('Phone Number')}}</label>
                    <div class="col-sm-9">
                        <input type="tel" required pattern="[0-9]{9}" id="phone-code" class="border-right-0 h-auto w-100 form-control @error('phone') is-invalid @enderror" placeholder="{{  translate('Mobile Number') }}" name="phone">
                        @error('phone')
                        <div class="text text-danger" style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                        @enderror
                    </div>
                </div>
                <input type="hidden" name="country_code" value="">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="password">{{translate('Password')}}</label>
                    <div class="col-sm-9 position-relative">
                        <input type="password" placeholder="{{translate('Password')}}" id="password" name="password" class="form-control @error('password') is-invalid @enderror" >
                        <i class="fa fa-eye show-hide-password" onclick="showHidePassword(this)"></i>
                        @error('password')
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

@endsection
@section('script')
    <script type="text/javascript">
        var isPhoneShown = true;

        var input = document.querySelector("#phone-code");
        var iti = intlTelInput(input, {
        separateDialCode: true
        });

        var country = iti.getSelectedCountryData();
        console.log(country);
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function() {
        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);
        });
    </script>
@endsection
@endif
