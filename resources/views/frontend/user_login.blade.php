@extends('frontend.layouts.app')

@section('content')
    <section class="gry-bg py-5">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="text-center px-35 pt-5">
                                <h1 class="heading heading-4 strong-500">
                                    {{ translate('Login to your account.')}}
                                </h1>
                            </div>
                            <div class="px-5 py-3 py-lg-4">
                                <div class="">
                                    <form id="form_user_login" class="form-default" role="form" action="{{ route('login') }}" method="POST" novalidate>
                                        @csrf
                                        <div class="form-group email-form-group">
                                            <div class="input-group input-group--style-1">
                                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                                    <input type="email" class="form-control form-control-sm {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ translate('Your Email')}}" name="email" id="email">
                                                @else
                                                    <input type="email" class="form-control form-control-sm {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Your Email') }}" name="email">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group phone-form-group">
                                            @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                                <span>{{  translate('Use country code before number') }}</span>
                                            <div class="input-group input-group--style-1">
                                                <input type="tel" id="phone-code" pattern="[0-9]{9}" class="border-right-0 h-auto w-100 form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Mobile Number') }}" name="email">
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            </div>
                                            @endif
                                        </div>

                                        <input type="hidden" name="country_code" value="">

                                        <div class="form-group">
                                            <button class="btn btn-link p-0" type="button" onclick="toggleEmailPhone(this)">{{ translate('Use Email Instead') }}</button>
                                        </div>

                                        <div class="form-group">
                                            <div class="input-group input-group--style-1 position-relative">
                                                <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ translate('Password')}}" name="password" id="password">
                                                <i class="fa fa-eye show-hide-password" onclick="showHidePassword(this)"></i>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <div class="checkbox pad-btm text-left">
                                                        <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                        <label for="demo-form-checkbox" class="text-sm">
                                                            {{  translate('Remember Me') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 text-right">
                                                <a href="{{ route('password.request') }}" class="link link-xs link--style-3">{{ __('Forgot password?')}}</a>
                                            </div>
                                        </div>


                                        <div class="text-center">
                                            <button type="submit" class="btn btn-styled btn-base-1 btn-md w-100">{{  translate('Login') }}</button>
                                        </div>
                                    </form>
                                    @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                        <div class="or or--1 mt-3 text-center">
                                            <span>or</span>
                                        </div>
                                        <div>
                                        @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                            <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="btn btn-styled btn-block btn-facebook btn-icon--2 btn-icon-left px-4 mb-3">
                                                <i class="icon fa fa-facebook"></i> {{ translate('Login with Facebook')}}
                                            </a>
                                        @endif
                                        @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                            <a href="{{ route('social.login', ['provider' => 'google']) }}" class="btn btn-styled btn-block btn-google btn-icon--2 btn-icon-left px-4 mb-3">
                                                <i class="icon fa fa-google"></i> {{ translate('Login with Google')}}
                                            </a>
                                        @endif
                                        @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                            <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="btn btn-styled btn-block btn-twitter btn-icon--2 btn-icon-left px-4">
                                                <i class="icon fa fa-twitter"></i> {{ translate('Login with Twitter')}}
                                            </a>
                                        @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-center px-35 pb-3">
                                <p class="text-md">
                                    {{ __('Need an account?')}} <a href="{{ route('user.registration') }}" class="strong-600">{{ translate('Register Now')}}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    @if (env("DEMO_MODE") == "On")
                        <div class="bg-white p-4 mx-auto mt-4">
                            <div class="">
                                <table class="table table-responsive table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td>{{ translate('Seller Account')}}</td>
                                            <td><button class="btn btn-info" onclick="autoFillSeller()">{{ translate('Copy credentials') }}</button></td>
                                        </tr>
                                        <tr>
                                            <td>{{ translate('Customer Account')}}</td>
                                            <td><button class="btn btn-info" onclick="autoFillCustomer()">{{ translate('Copy credentials') }}</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
        function autoFillSeller(){
            $('#email').val('seller@example.com');
            $('#password').val('123456');
        }
        function autoFillCustomer(){
            $('#email').val('customer@example.com');
            $('#password').val('123456');
        }
        var input = document.querySelector("#phone-code");
        var iti = intlTelInput(input, {
            separateDialCode: true,
        });

        var country = iti.getSelectedCountryData();
        console.log(country);
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function() {
            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);
        });

        var isPhoneShown = true;
        function toggleEmailPhone(el){
            if(isPhoneShown){
                $('.phone-form-group').hide();
                $('.phone-form-group').find('input[type="tel"]').attr('name', '');
                $('.email-form-group').find('input').attr('name', 'email');
                $('.email-form-group').show();
                isPhoneShown = false;
                $(el).html("{{ translate('Use Phone Instead') }}");
            }
            else{
                $('.phone-form-group').show();
                $('.email-form-group').hide();
                $('.email-form-group').find('input').attr('name', '');
                $('.phone-form-group').find('input[type="tel"]').attr('name', 'email');
                isPhoneShown = true;
                $(el).html("{{ translate('Use Email Instead') }}");
            }
        }

        $(document).ready(function(){
            $('.email-form-group').hide();
            $('.email-form-group').find('input').attr('name', '');
        });
    </script>
@endsection
