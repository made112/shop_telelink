@extends('layouts.login')

@section('content')
    <style>
        .show-hide-password {
            position: absolute;
            right: 10px;
            top: 28%;
            font-size: 18px;
            z-index: 3;
            cursor: pointer;
        }
        html[dir="rtl"] .show-hide-password {
            right: unset;
            left: 10px;
        }
        .iti__country-list {
            z-index: 4!important;
        }
        .position-relative {
            position: relative;
        }
    </style>
@php
    $generalsetting = \App\GeneralSetting::first();
@endphp

{{--<div class="flex-row">--}}
{{--    <div class="flex-col-xl-6 blank-index d-flex align-items-center justify-content-center"--}}
{{--    @if ($generalsetting->admin_login_sidebar != null)--}}
{{--        style="background-image:url('{{ my_asset($generalsetting->admin_login_sidebar) }}');"--}}
{{--    @else--}}
{{--        style="background-image:url('{{ my_asset('img/bg-img/login-box.jpg') }}');"--}}
{{--    @endif>--}}

{{--    </div>--}}
{{--    <div class="flex-col-xl-6">--}}
{{--        <div class="pad-all">--}}
{{--        <div class="text-center">--}}
{{--            <br>--}}
{{--			@if($generalsetting->logo != null)--}}
{{--                <img loading="lazy"  src="{{ my_asset($generalsetting->logo) }}" class="" height="44">--}}
{{--            @else--}}
{{--                <img loading="lazy"  src="{{ my_asset('frontend/images/logo/logo.png') }}" class="" height="44">--}}
{{--            @endif--}}

{{--            <br>--}}
{{--            <br>--}}
{{--            <br>--}}

{{--        </div>--}}
{{--            <form class="pad-hor" method="POST" role="form" action="{{ route('login') }}">--}}
{{--                @csrf--}}
{{--                <div class="form-group">--}}
{{--                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ translate('Email') }}">--}}
{{--                    @if ($errors->has('email'))--}}
{{--                        <span class="invalid-feedback" role="alert">--}}
{{--                            <strong>{{ $errors->first('email') }}</strong>--}}
{{--                        </span>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <div class="form-group">--}}
{{--                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="{{ translate('Password') }}">--}}
{{--                    @if ($errors->has('password'))--}}
{{--                        <span class="invalid-feedback" role="alert">--}}
{{--                            <strong>{{ $errors->first('password') }}</strong>--}}
{{--                        </span>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <div class="col-sm-6">--}}
{{--                        <div class="checkbox pad-btm text-left">--}}
{{--                            <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>--}}
{{--                            <label for="demo-form-checkbox">--}}
{{--                                {{ translate('Remember Me') }}--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    @if(env('MAIL_USERNAME') != null && env('MAIL_PASSWORD') != null)--}}
{{--                        <div class="col-sm-6">--}}
{{--                            <div class="checkbox pad-btm text-right">--}}
{{--                                <a href="{{ route('password.request') }}" class="btn-link">{{__('Forgot password')}} ?</a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <button type="submit" class="btn btn-primary btn-lg btn-block">--}}
{{--                    {{ translate('Login') }}--}}
{{--                </button>--}}
{{--            </form>--}}
{{--            @if (env("DEMO_MODE") == "On")--}}
{{--                <div class="col-sm-6">--}}
{{--                    <div class="cls-content-sm panel" style="width: 100% !important;">--}}
{{--                        <div class="pad-all">--}}
{{--                            <table class="table table-responsive table-bordered">--}}
{{--                                <tbody>--}}
{{--                                    <tr>--}}
{{--                                        <td>admin@example.com</td>--}}
{{--                                        <td>123456</td>--}}
{{--                                        <td><button class="btn btn-info btn-xs" onclick="autoFill()">copy</button></td>--}}
{{--                                    </tr>--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}


                <div class="mb-5 text-center">
                    @if($generalsetting->logo != null)
                    <img loading="lazy" style="width: 100%;object-fit: cover;height: 150px;"  src="{{ my_asset($generalsetting->logo) }}" class="" height="44">
                    @else
                        <img loading="lazy"  src="{{ my_asset('frontend/images/logo/logo.png') }}" class="" height="44">
                    @endif
                    <h1 class="h3 text-primary mb-0">{{ translate('Welcome to') }} {{ env('APP_NAME') }}</h1>
                    <p>{{ translate('Login to your account.') }}</p>
                </div>
                <form class="pad-hor" method="POST" role="form" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ translate('Email Or Phone') }}">
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group position-relative">
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="{{ translate('Password') }}">
                        <i class="fa fa-eye show-hide-password" onclick="showHidePassword(this)"></i>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <div class="text-left">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <span>{{ translate('Remember Me') }}</span>
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </div>
                        @if(env('MAIL_USERNAME') != null && env('MAIL_PASSWORD') != null)
                            <div class="col-sm-6">
                                <div class="text-right">
                                    <a href="{{ route('password.request') }}" class="text-reset fs-14">{{translate('Forgot password')}} ?</a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        {{ translate('Login') }}
                    </button>
                </form>
                @if (env("DEMO_MODE") == "On")
                    <div class="mt-4">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>admin@example.com</td>
                                <td>123456</td>
                                <td><button class="btn btn-info btn-xs" onclick="autoFill()">{{ translate('Copy') }}</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endif


@endsection

@section('script')
    <script type="text/javascript">
        function autoFill(){
            $('#email').val('admin@example.com');
            $('#password').val('123456');
        }
        function showHidePassword(el) {
            if ($('input[name="password"]').attr("type") === 'text'){
                $('input[name="password"]').attr("type", 'password');
                $('input[name="password_confirmation"]').attr("type", 'password');
                $(el).removeClass('fa-eye-slash')
                $(el).addClass('fa-eye')
            }else {
                $('input[name="password"]').attr("type", 'text');
                $('input[name="password_confirmation"]').attr("type", 'text');
                $(el).removeClass('fa-eye')
                $(el).addClass('fa-eye-slash')
            }
        }
    </script>
@endsection
