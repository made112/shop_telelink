<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ config('app.url') }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">
    <link name="favicon" type="image/x-icon" href="{{ my_asset(\App\GeneralSetting::first()->favicon) }}"
          rel="shortcut icon"/>

    <title>{{ config('app.name', 'Laravel') }}</title>
@yield('styles')
<!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    <link href="{{ auto_version(my_asset('css/bootstrap.min.css'))}}" rel="stylesheet">

    <!--active-shop Stylesheet [ REQUIRED ]-->
    <link href="{{ auto_version(my_asset('css/active-shop.min.css'))}}" rel="stylesheet">

    <!--active-shop Premium Icon [ DEMONSTRATION ]-->
    <link href="{{ auto_version(my_asset('css/demo/active-shop-demo-icons.min.css'))}}" rel="stylesheet">

    <!--Font Awesome [ OPTIONAL ]-->
    <link href="{{ auto_version(my_asset('plugins/font-awesome/css/font-awesome.min.css'))}}" rel="stylesheet">

    <!--Switchery [ OPTIONAL ]-->
    <link href="{{ auto_version(my_asset('plugins/switchery/switchery.min.css'))}}" rel="stylesheet">

    <!--DataTables [ OPTIONAL ]-->
    <link href="{{ auto_version(my_asset('plugins/datatables/media/css/dataTables.bootstrap.css')) }}" rel="stylesheet">
    <link
        href="{{ auto_version(my_asset('plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css')) }}"
        rel="stylesheet">

    <!--Select2 [ OPTIONAL ]-->
    <link href="{{ auto_version(my_asset('plugins/select2/css/select2.min.css'))}}" rel="stylesheet">

    <link href="{{ auto_version(my_asset('css/bootstrap-select.min.css'))}}" rel="stylesheet">

    <!--Chosen [ OPTIONAL ]-->
{{-- <link href="{{ my_asset('plugins/chosen/chosen.min.css')}}" rel="stylesheet"> --}}

<!--Bootstrap Tags Input [ OPTIONAL ]-->
    <link href="{{ auto_version(my_asset('plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.css')) }}"
          rel="stylesheet">

    <link type="text/css" href="{{ auto_version(my_asset('frontend/css/intlTelInput.min.css')) }}" rel="stylesheet"
          media="none" onload="if(media!='all')media='all'">

    <!--Summernote [ OPTIONAL ]-->
    <link href="{{ auto_version(my_asset('css/summernote.min.css')) }}" rel="stylesheet">

    <!--Theme [ DEMONSTRATION ]-->
<!-- <link href="{{ auto_version(my_asset('css/themes/type-full/theme-dark-full.min.css')) }}" rel="stylesheet"> -->
    <link href="{{ auto_version(my_asset('css/themes/type-c/theme-navy.min.css')) }}" rel="stylesheet">

    <!--Spectrum Stylesheet [ REQUIRED ]-->
    <link href="{{ auto_version(my_asset('css/spectrum.css'))}}" rel="stylesheet">

    <!--Custom Stylesheet [ REQUIRED ]-->
    <link href="{{ auto_version(my_asset('css/custom.css'))}}" rel="stylesheet">


    <!--JAVASCRIPT-->
    <script>
        $.ajaxSetup({
            beforeSend: function (xhr, type) {
                if (!type.crossDomain) {
                    xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                }
            },
        });
    </script>
    <!--=================================================-->
    <script src="{{auto_version(my_asset('js/vendors.js'))}}"></script>

    <!--active-shop [ RECOMMENDED ]-->
    <script src="{{ auto_version(my_asset('js/active-shop.min.js')) }}"></script>

    <!--Summernote [ OPTIONAL ]-->
    <script src="{{ auto_version(my_asset('js/summernote.min.js')) }}"></script>

    <script>
        var AIZ = AIZ || {};
    </script>
    <script src="{{auto_version(my_asset('js/aiz-core.js'))}}"></script>
    <script src="{{ auto_version(my_asset('frontend/js/jquery.validate.min.js')) }}"></script>
    <!--Alerts [ SAMPLE ]-->
    <script src="{{ auto_version(my_asset('js/demo/ui-alerts.js')) }}"></script>

    <!--Switchery [ OPTIONAL ]-->
    <script src="{{ auto_version(my_asset('plugins/switchery/switchery.min.js'))}}"></script>

    <!--DataTables [ OPTIONAL ]-->
    <script src="{{ auto_version(my_asset('plugins/datatables/media/js/jquery.dataTables.js'))}}"></script>
    <script src="{{ auto_version(my_asset('plugins/datatables/media/js/dataTables.bootstrap.js'))}}"></script>
    <script
        src="{{ auto_version(my_asset('plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js'))}}"></script>

    <!--DataTables Sample [ SAMPLE ]-->
    <script src="{{ auto_version(my_asset('js/demo/tables-datatables.js'))}}"></script>

    <!--Select2 [ OPTIONAL ]-->
    <script src="{{ auto_version(my_asset('plugins/select2/js/select2.min.js'))}}"></script>
    <script src="{{ auto_version(my_asset('js/bootstrap-select.min.js'))}}"></script>

    <!--Bootstrap Tags Input [ OPTIONAL ]-->
    <script src="{{ auto_version(my_asset('plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js'))}}"></script>

    <!--Bootstrap Validator [ OPTIONAL ]-->
    <script src="{{ auto_version(my_asset('plugins/bootstrap-validator/bootstrapValidator.min.js')) }}"></script>

    <!--Bootstrap Wizard [ OPTIONAL ]-->
    <script src="{{ auto_version(my_asset('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js')) }}"></script>

    <!--Bootstrap Datepicker [ OPTIONAL ]-->
    <script src="{{ auto_version(my_asset('plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')) }}"></script>

    <!--Form Component [ SAMPLE ]-->
    <script src="{{auto_version(my_asset('js/demo/form-wizard.js'))}}"></script>

    <!--Spectrum JavaScript [ REQUIRED ]-->
    <script src="{{ auto_version(my_asset('js/spectrum.js'))}}"></script>

    <!--Spartan Image JavaScript [ REQUIRED ]-->
    <script src="{{ auto_version(my_asset('js/spartan-multi-image-picker-min.js')) }}"></script>

    <!--Custom JavaScript [ REQUIRED ]-->
    <script src="{{ auto_version(my_asset('js/custom.js'))}}"></script>

    <script type="text/javascript">

        $(document).ready(function () {
            var inputEl = document.getElementById('phone-code');
            if (inputEl) {
                var goodKey = '0123456789';
                var checkInputTel = function (e) {
                    var key = (typeof e.which == "number") ? e.which : e.keyCode;
                    var start = this.selectionStart,
                        end = this.selectionEnd;

                    var filtered = this.value.split('').filter(filterInput);
                    this.value = filtered.join("");

                    /* Prevents moving the pointer for a bad character */
                    var move = (filterInput(String.fromCharCode(key)) || (key == 0 || key == 8)) ? 0 : 1;
                    this.setSelectionRange(start - move, end - move);
                }

                var filterInput = function (val) {
                    return (goodKey.indexOf(val) > -1);
                }

                inputEl.addEventListener('input', checkInputTel);
            }
            //$('div.alert').not('.alert-important').delay(3000).fadeOut(350);
            if ($('.active-link').parent().parent().parent().is('ul')) {
                $('.active-link').parent().parent().addClass('in');
                $('.active-link').parent().parent().parent().addClass('in');
            }
            if ($('.active-link').parent().parent().is('li')) {
                $('.active-link').parent().parent().addClass('active-sub');
            }
            if ($('.active-link').parent().is('ul')) {
                $('.active-link').parent().addClass('in');
            }

            if ($('#lang-change').length > 0) {
                $('#lang-change .dropdown-item a').each(function () {
                    $(this).on('click', function (e) {
                        e.preventDefault();
                        var $this = $(this);
                        var locale = $this.data('flag');
                        $.post('{{ route('language.change') }}', {
                            _token: '{{ csrf_token() }}',
                            locale: locale
                        }, function (data) {
                            location.reload();
                        });

                    });
                });
            }

        });

    </script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    @if (\App\BusinessSetting::where('type', 'google_analytics')->first()->value == 1)
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-133955404-1"></script>

        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', @php env('TRACKING_ID') @endphp);
        </script>
    @endif


</head>
<body>

@foreach (session('flash_notification', collect())->toArray() as $message)
    <script type="text/javascript">
        $(document).on('nifty.ready', function () {
            showAlert('{{ $message['level'] }}', '{{ $message['message'] }}');
        });
    </script>
@endforeach


<div id="container" class="effect h-auto overflow-auto aside-float aside-bright mainnav-lg">

    @include('inc.admin_nav')

    <div class="boxed">

        <!--CONTENT CONTAINER-->
        <!--===================================================-->
        <div id="content-container">
            <div id="page-content">

                @yield('content')

            </div>
        </div>
    </div>

    @include('inc.admin_sidenav')

    @include('inc.admin_footer')

    @include('partials.modal')

</div>
<script>
    $(".editor").each(function (el) {
        var $this = $(this);
        var buttons = $this.data("buttons");
        var minHeight = $this.data("min-height");
        var placeholder = $this.attr("placeholder");

        buttons = !buttons
            ? [
                ["font", ["bold", "underline", "italic", "clear"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["style", ["style"]],
                ["color", ["color"]],
                ["table", ["table"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "undo", "redo"]],
            ]
            : buttons;
        placeholder = !placeholder ? "" : placeholder;
        minHeight = !minHeight ? 200 : minHeight;
        $(".note-group-select-from-files").remove();
        $this.summernote({
            toolbar: buttons,
            disableDragAndDrop:true,
            dialogsFade: true,
            placeholder: placeholder,
            height: minHeight,
            callbacks: {
                onImageUpload: function (data) {
                    data.pop();
                }
            }
        });
    });
</script>
@yield('script')

</body>
</html>
