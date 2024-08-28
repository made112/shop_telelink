<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link name="favicon" type="image/x-icon" href="{{ my_asset(\App\GeneralSetting::first()->favicon) }}" rel="shortcut icon" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    <link href="{{ my_asset('css/bootstrap.min.css')}}" rel="stylesheet">

    <!--Font Awesome [ OPTIONAL ]-->
    <link href="{{ my_asset('plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">

    <!--active-shop Stylesheet [ REQUIRED ]-->
    <link href="{{ my_asset('css/active-shop.min.css')}}" rel="stylesheet">

    <!--active-shop Premium Icon [ DEMONSTRATION ]-->
    <link href="{{ my_asset('css/demo/active-shop-demo-icons.min.css')}}" rel="stylesheet">

    <link type="text/css" href="{{ my_asset('frontend/css/sweetalert2.min.css') }}" rel="stylesheet" media="none" onload="if(media!='all')media='all'">


    <!--Demo [ DEMONSTRATION ]-->
    <link href="{{ my_asset('css/demo/active-shop-demo.min.css') }}" rel="stylesheet">

    <!--Theme [ DEMONSTRATION ]-->
    <link href="{{ my_asset('css/themes/type-c/theme-navy.min.css') }}" rel="stylesheet">

    <link href="{{ my_asset('css/custom.css') }}" rel="stylesheet">
    <style>
        .h3, h3 {
            font-size: 2.75rem;
        }
        .panel-body {
            padding: 50px 25px;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 1.5rem !important;
        }
        .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
            margin-bottom: .5rem;
            font-weight: 500;
            line-height: 1.2;
        }
        .mb-5, .my-5 {
            margin-bottom: 3rem!important;
        }
        .mb-4, .my-4 {
            margin-bottom: 1.5rem!important;
        }
        .mb-2, .my-2 {
            margin-bottom: .5rem!important;
        }
        .mw-100 {
            max-width: 100%!important;
        }
        .text-primary {
            color: #377dff !important;
        }
        .mb-0, .my-0 {
            margin-bottom: 0!important;
        }
        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }
        button, input {
            overflow: visible;
            line-height: 2;
        }
        a, button, input, textarea, .btn, .has-transition {
            -webkit-transition: all 0.3s ease;
            transition: all 0.3s ease;
        }
        .form-control {
            display: block;
            width: 100%;
            height: calc(1.5em + .75rem + 2px);
            padding: .375rem .75rem;
            /*font-size: 1rem;*/
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .form-control {
            padding: 1.6rem 1.2rem;
            /*font-size: 0.875rem;*/
            height: calc(1.3125rem + 1.2rem + 2px);
            border: 1px solid #e2e5ec;
            color: #898b92;
        }
        .aiz-checkbox, .aiz-radio {
            display: inline-block;
            position: relative;
            padding-left: 28px;
            margin-bottom: 10px;
            cursor: pointer;
            /*font-size: 0.875rem;*/
            -webkit-transition: all 0.3s ease;
            transition: all 0.3s ease;
        }
        input[type=checkbox], input[type=radio] {
            box-sizing: border-box;
            padding: 0;
        }
        .aiz-checkbox > input, .aiz-radio > input {
            position: absolute;
            z-index: -1;
            opacity: 0;
        }
        .aiz-checkbox .aiz-square-check, .aiz-checkbox .aiz-rounded-check, .aiz-radio .aiz-square-check, .aiz-radio .aiz-rounded-check {
            position: absolute;
            top: 2px;
            left: 0;
        }
        .aiz-square-check {
            border-radius: 3px;
        }
        .aiz-square-check, .aiz-rounded-check {
            background: 0 0;
            position: relative;
            height: 16px;
            width: 16px;
            border: 1px solid #d1d7e2;
        }
        .aiz-square-check:after, .aiz-rounded-check:after {
            content: "";
            position: absolute;
            visibility: hidden;
            opacity: 0;
            top: 50%;
            left: 50%;
            -webkit-transition: all 0.3s ease;
            transition: all 0.3s ease;
        }
        .aiz-square-check:after {
            margin-left: -2px;
            margin-top: -6px;
            width: 5px;
            height: 10px;
            border-width: 0 2px 2px 0 !important;
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
            border: solid #377dff;
        }
        .aiz-square-check:after, .aiz-rounded-check:after {
            content: "";
            position: absolute;
            visibility: hidden;
            opacity: 0;
            top: 50%;
            left: 50%;
            -webkit-transition: all 0.3s ease;
            transition: all 0.3s ease;
        }
        .btn:not(:disabled):not(.disabled) {
            cursor: pointer;
        }
        .btn-primary, .btn-soft-primary:hover, .btn-outline-primary:hover {
            background-color: #377dff;
            border-color: #377dff;
            color: #fff;
        }
        .btn-group-lg>.btn, .btn-lg {
            padding: .5rem 1rem;
            /*font-size: 1.25rem;*/
            line-height: 1.5;
            border-radius: .3rem;
        }
        .btn-lg {
            padding: 0.75rem 1.5rem;
            /*font-size: 1rem;*/
        }
        .btn-block {
            display: block;
            width: 100%;
        }
        .aiz-checkbox > input:checked ~ .aiz-square-check:after, .aiz-radio > input:checked ~ .aiz-square-check:after, .aiz-checkbox > input:checked ~ .aiz-rounded-check:after, .aiz-radio > input:checked ~ .aiz-rounded-check:after {
            visibility: visible;
            opacity: 1;
        }

    </style>

</head>
<body>
    @php
        $generalsetting = \App\GeneralSetting::first();
    @endphp
    <div id="container" class="blank-index"
        @if ($generalsetting->admin_login_background != null)
            style="background-image:url('{{ my_asset($generalsetting->admin_login_background) }}');"
        @else
            style="background-image:url('{{ my_asset('img/bg-img/login-bg.jpg') }}');"
        @endif>
        <div class="cls-content">
            <div class="container">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col-md-5 col-xl-4 mx-auto">
                        <div class="panel">
                            <div class="panel-body">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--JAVASCRIPT-->
    <!--=================================================-->

    <!--jQuery [ REQUIRED ]-->
    <script src=" {{my_asset('js/jquery.min.js') }}"></script>


    <!--BootstrapJS [ RECOMMENDED ]-->
    <script src="{{ my_asset('js/bootstrap.min.js') }}"></script>


    <!--active-shop [ RECOMMENDED ]-->
    <script src="{{ my_asset('js/active-shop.min.js') }}"></script>

    <!--Alerts [ SAMPLE ]-->
    <script src="{{my_asset('js/demo/ui-alerts.js') }}"></script>
    <script src="{{ my_asset('frontend/js/sweetalert2.min.js') }}"></script>

    <script>
        function showFrontendAlert(type, message){
            if(type == 'danger'){
                type = 'error';
            }
            swal({
                position: 'center',
                type: type,
                title: message,
                showConfirmButton: false,
                timer: 3000
            });
        }
    </script>
    @foreach (session('flash_notification', collect())->toArray() as $message)
        <script>
            showFrontendAlert('{{ $message['level'] }}', '{{ $message['message'] }}');
        </script>
    @endforeach
    @yield('script')


</body>
</html>
