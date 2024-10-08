<!DOCTYPE html>
@if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <html dir="rtl" lang="en">
    @else
        <html lang="en">
        @endif
        <head>

            @php
                $seosetting = \App\SeoSetting::first();
            @endphp

            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="robots" content="index, follow">
            <title>@yield('meta_title', config('app.name', 'Laravel'))</title>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <meta name="app-url" content="{{ config('app.url') }}">
            <meta name="file-base-url" content="{{ getFileBaseURL() }}">
            <meta name="description" content="@yield('meta_description', $seosetting->description)"/>
            <meta name="keywords" content="@yield('meta_keywords', $seosetting->keyword)">
            <meta name="author" content="{{ $seosetting->author }}">
            <meta name="sitemap_link" content="{{ $seosetting->sitemap_link }}">

            @yield('meta')

            @if(!isset($detailedProduct) && !isset($shop) && !isset($page) && !isset($flash_deal))
            <!-- Schema.org markup for Google+ -->
                <meta itemprop="name" content="{{ config('app.name', 'Laravel') }}">
                <meta itemprop="description" content="{{ $seosetting->description }}">
                <meta itemprop="image" content="{{ my_asset(\App\GeneralSetting::first()->logo) }}">

                <!-- Twitter Card data -->
                <meta name="twitter:card" content="product">
                <meta name="twitter:site" content="@publisher_handle">
                <meta name="twitter:title" content="{{ config('app.name', 'Laravel') }}">
                <meta name="twitter:description" content="{{ $seosetting->description }}">
                <meta name="twitter:creator" content="@author_handle">
                <meta name="twitter:image" content="{{ my_asset(\App\GeneralSetting::first()->logo) }}">

                <!-- Open Graph data -->
                <meta property="og:title" content="{{ config('app.name', 'Laravel') }}"/>
                <meta property="og:type" content="website"/>
                <meta property="og:url" content="{{ route('home') }}"/>
                <meta property="og:image" content="{{ my_asset(\App\GeneralSetting::first()->logo) }}"/>
                <meta property="og:description" content="{{ $seosetting->description }}"/>
                <meta property="og:site_name" content="{{ env('APP_NAME') }}"/>
                <meta property="fb:app_id" content="{{ env('FACEBOOK_CLIENT_ID') }}">
                <meta property="fb:page_id" content="{{ env('FACEBOOK_CLIENT_ID') }}"/>
            @endif

        <!-- Favicon -->
            <link type="image/x-icon" href="{{ my_asset(\App\GeneralSetting::first()->favicon) }}" rel="shortcut icon"/>

            <!-- Fonts -->
            <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i"
                  rel="stylesheet" media="none" onload="if(media!='all')media='all'">

            <!-- Bootstrap -->
            <link rel="stylesheet" href="{{ auto_version(my_asset('frontend/css/bootstrap.min.css')) }}" type="text/css"
                  media="all">

            <!-- Icons -->
            <link rel="stylesheet" href="{{ auto_version(my_asset('frontend/css/font-awesome.min.css')) }}"
                  type="text/css" media="none" onload="if(media!='all')media='all'">
            <link rel="stylesheet" href="{{ auto_version(my_asset('frontend/css/line-awesome.min.css')) }}"
                  type="text/css" media="none" onload="if(media!='all')media='all'">

            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/bootstrap-tagsinput.css')) }}"
                  rel="stylesheet" media="none" onload="if(media!='all')media='all'">
            <link type="text/css" href="{{ auto_version(my_asset('css/summernote.min.css')) }}" rel="stylesheet"
                  media="none" onload="if(media!='all')media='all'">
            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/sweetalert2.min.css')) }}"
                  rel="stylesheet" media="none" onload="if(media!='all')media='all'">
            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/slick.css')) }}" rel="stylesheet"
                  media="all">
            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/xzoom.css')) }}" rel="stylesheet"
                  media="none" onload="if(media!='all')media='all'">
            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/jssocials.css')) }}" rel="stylesheet"
                  media="none" onload="if(media!='all')media='all'">
            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/jssocials-theme-flat.css')) }}"
                  rel="stylesheet" media="none" onload="if(media!='all')media='all'">
            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/intlTelInput.min.css')) }}"
                  rel="stylesheet" media="none" onload="if(media!='all')media='all'">
            <link type="text/css" href="{{ auto_version(my_asset('css/spectrum.css'))}}" rel="stylesheet" media="none"
                  onload="if(media!='all')media='all'">

            <!-- Global style (main) -->
            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/active-shop.css')) }}" rel="stylesheet"
                  media="all">


            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/main.css')) }}" rel="stylesheet"
                  media="all">

            @if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
            <!-- RTL -->
                <link type="text/css" href="{{ auto_version(my_asset('frontend/css/active.rtl.css')) }}"
                      rel="stylesheet" media="all">
            @endif

        <!-- color theme -->
            <link
                href="{{ auto_version(my_asset('frontend/css/colors/'.\App\GeneralSetting::first()->frontend_color.'.css'))}}"
                rel="stylesheet" media="all">

            <!-- Custom style -->
            <link type="text/css" href="{{ auto_version(my_asset('frontend/css/custom-style.css')) }}" rel="stylesheet"
                  media="all">
            {{--    <link rel="stylesheet" href="{{my_asset('css/aiz-core.css')}}">--}}
        <!-- jQuery -->
            <script src="{{ auto_version(my_asset('frontend/js/vendor/jquery.min.js')) }}"></script>
            {{--    <script src="{{my_asset('js/vendors.js')}}" ></script>--}}


            @if (\App\BusinessSetting::where('type', 'google_analytics')->first()->value == 1)
            <!-- Global site tag (gtag.js) - Google Analytics -->
                <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>

                <script>
                    window.dataLayer = window.dataLayer || [];

                    function gtag() {
                        dataLayer.push(arguments);
                    }

                    gtag('js', new Date());
                    gtag('config', '{{ env('TRACKING_ID') }}');
                </script>
            @endif

            @if (\App\BusinessSetting::where('type', 'facebook_pixel')->first()->value == 1)
            <!-- Facebook Pixel Code -->
                <script>
                    !function (f, b, e, v, n, t, s) {
                        if (f.fbq) return;
                        n = f.fbq = function () {
                            n.callMethod ?
                                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                        };
                        if (!f._fbq) f._fbq = n;
                        n.push = n;
                        n.loaded = !0;
                        n.version = '2.0';
                        n.queue = [];
                        t = b.createElement(e);
                        t.async = !0;
                        t.src = v;
                        s = b.getElementsByTagName(e)[0];
                        s.parentNode.insertBefore(t, s)
                    }(window, document, 'script',
                        'https://connect.facebook.net/en_US/fbevents.js');
                    fbq('init', {{ env('FACEBOOK_PIXEL_ID') }});
                    fbq('track', 'PageView');
                </script>
                <noscript>
                    <img height="1" width="1" style="display:none"
                         src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}/&ev=PageView&noscript=1"/>
                </noscript>
                <!-- End Facebook Pixel Code -->
            @endif
        </head>
        <body>


        <!-- MAIN WRAPPER -->
        <div class="body-wrap shop-default shop-cards shop-tech">

            <!-- Header -->
            @include('frontend.inc.nav')

            @yield('content')

            @include('frontend.inc.footer')

            @include('frontend.partials.modal')

            @if (\App\BusinessSetting::where('type', 'facebook_chat')->first()->value == 1)
                <div id="fb-root"></div>
                <!-- Your customer chat code -->
                <div class="fb-customerchat"
                     attribution=setup_tool
                     page_id="{{ env('FACEBOOK_PAGE_ID') }}">
                </div>
            @endif

            <div class="modal fade" id="addToCart">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size"
                     role="document">
                    <div class="modal-content position-relative">
                        <div class="c-preloader">
                            <i class="fa fa-spin fa-spinner"></i>
                        </div>
                        <button type="button" class="close absolute-close-btn" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div id="addToCart-modal-body">

                        </div>
                    </div>
                </div>
            </div>

        </div><!-- END: body-wrap -->

        <!-- SCRIPTS -->
        <!-- <a href="#" class="back-to-top btn-back-to-top"></a> -->

        <!-- Core -->
        <script src="{{ auto_version(my_asset('frontend/js/vendor/popper.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/vendor/bootstrap.min.js')) }}"></script>

        <!-- Plugins: Sorted A-Z -->
        <script src="{{ auto_version(my_asset('frontend/js/jquery.countdown.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/select2.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/nouislider.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/sweetalert2.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/slick.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/jssocials.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/bootstrap-tagsinput.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/summernote.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/xzoom.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/fb-script.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/lazysizes.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/intlTelInput.min.js')) }}"></script>
        <!-- App JS -->

        <script src="{{ auto_version(my_asset('frontend/js/jquery.validate.min.js')) }}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/active-shop.js')) }}"></script>

        <script>
            var AIZ = AIZ || {};
        </script>
        <script src="{{auto_version(my_asset('js/aiz-core.js'))}}"></script>
        <script src="{{auto_version(my_asset('js/jquery.validate.min.js'))}}"></script>
        <script src="{{ auto_version(my_asset('frontend/js/main.js')) }}"></script>


        <script>

            // Wait for the DOM to be ready
            $(function () {
                // Initialize form validation on the registration form.
                // It has the name attribute "registration"
                $("#form_user_login").validate({
                    // Specify validation rules
                    rules: {
                        // The key name on the left side is the name attribute
                        // of an input field. Validation rules are defined
                        // on the right side
                        email: {
                            required: true
                        },
                        password: {
                            required: true,
                        }
                    },
                    // Specify validation error messages
                    messages: {
                        password: {
                            required: "{{ translate('Please provide a password') }}",
                        },
                        email: "{{ translate('Please enter a valid email or phone') }}",
                    },
                    // Make sure the form is submitted to the destination defined
                    // in the "action" attribute of the form when valid
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
                $("form[name='registration']").validate({
                    // Specify validation rules
                    rules: {
                        // The key name on the left side is the name attribute
                        // of an input field. Validation rules are defined
                        // on the right side
                        name: "required",
                        country_code: "required",
                        phone: "required",
                        email: {
                            required: false,
                            // Specify that email should be validated
                            // by the built-in "email" rule
                            email: true
                        },
                        password: {
                            required: true,
                            minlength: 5
                        },
                        password_confirmation: {
                            required: true,
                            minlength: 5,
                            equalTo: "#password"
                        },
                        checkbox_example_1: {
                            required: false,
                        }
                    },
                    // Specify validation error messages
                    messages: {
                        name: "{{ translate('Please enter your name') }}",
                        phone: "{{ translate('Please enter your phone') }}",
                        country_code: "{{ translate('Please enter your country code') }}",
                        password: {
                            required: "{{ translate('Please provide a password') }}",
                            minlength: "{{ translate('Your password must be at least 5 characters long') }}"
                        },
                        email: "{{ translate('Please enter a valid email address') }}",
                        password_confirmation: {
                            required: "{{ translate('Password does not match') }}",
                            minlength: "{{ translate('Password does not match') }}",
                            equalTo: "{{translate('Password does not match')}}"
                        }
                    },
                    // Make sure the form is submitted to the destination defined
                    // in the "action" attribute of the form when valid
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
                $("#new-address-form").validate({
                    // Specify validation rules
                    rules: {
                        // The key name on the left side is the name attribute
                        // of an input field. Validation rules are defined
                        // on the right side
                        city: "required",
                        address: "required",
                        phone: {
                            required: true,
                            minlength: 9
                        }
                    },
                    // Specify validation error messages
                    messages: {
                        city: "{{ translate('Please enter your name') }}",
                        address: "{{ translate('Please enter your address') }}",
                        phone: {
                            required: "{{ translate('Please enter your phone') }}",
                            minlength: "{{ translate('Must match 59-999-999') }}"
                        }
                    },
                    // Make sure the form is submitted to the destination defined
                    // in the "action" attribute of the form when valid
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
            });

            function showFrontendAlert(type, message) {
                if (type == 'danger') {
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

            function validateFile(input, ext = [], type = '', size = 2000000, errorMsg = false) {
                input.on('change', function () {
                    var file_val = $(this).val();
                    if (file_val != '') {
                        console.log($(this)[0].files.length);
                        var file = $(this)[0].files[0];
                        if ($.inArray(file_val.substr(file_val.lastIndexOf('.') + 1).toLowerCase(), ext) < 0) {
                            showFrontendAlert('danger', '{{translate('The file extension must include: ')}}' + ext.toString())
                            $(this).val('');
                            if (errorMsg) {
                                $(this).parent().find('label > span').html('{{translate('Attach files.')}}');
                            } else {
                                $(this).parent().find('label > span').html('');
                            }
                            return;
                        }
                        if (file.size > size) {
                            showFrontendAlert('danger', '{{translate('File size must be less than ')}}' + convertBytesToMegaByte(size))
                            $(this).val('');
                            if (errorMsg) {
                                $(this).parent().find('label > span').html('{{translate('Attach files.')}}');
                            } else {
                                $(this).parent().find('label > span').html('');
                            }
                            return;
                        }
                    }
                })
            }

            function convertBytesToMegaByte(size) {
                if (size === 0) return '0 MB';
                var i = parseFloat(size / 1000000);
                return i + 'MB';
            }
        </script>

        @foreach (session('flash_notification', collect())->toArray() as $message)
            <script>
                showFrontendAlert('{{ $message['level'] }}', '{{ $message['message'] }}');
            </script>
        @endforeach
        <script>

            $(document).ready(function () {
                var inputEl = document.getElementById('phone-code');
                if (inputEl) {
                    var goodKey = '0123456789+ ';

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

                $('.category-nav-element').each(function (i, el) {
                    $(el).on('mouseover', function () {
                        if (!$(el).find('.sub-cat-menu').hasClass('loaded')) {
                            $.post('{{ route('category.elements') }}', {
                                _token: '{{ csrf_token()}}',
                                id: $(el).data('id')
                            }, function (data) {
                                $(el).find('.sub-cat-menu').addClass('loaded').html(data);
                            });
                        }
                    });
                });
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

                if ($('#currency-change').length > 0) {
                    $('#currency-change .dropdown-item a').each(function () {
                        $(this).on('click', function (e) {
                            e.preventDefault();
                            var $this = $(this);
                            var currency_code = $this.data('currency');
                            $.post('{{ route('currency.change') }}', {
                                _token: '{{ csrf_token() }}',
                                currency_code: currency_code
                            }, function (data) {
                                location.reload();
                            });

                        });
                    });
                }
            });

            $('#search').on('keyup', function () {
                search();
            });

            $('#search').on('focus', function () {
                search();
            });

            function search() {
                var search = $('#search').val();
                if (search.length > 0) {
                    $('body').addClass("typed-search-box-shown");

                    $('.typed-search-box').removeClass('d-none');
                    $('.search-preloader').removeClass('d-none');
                    $.post('{{ route('search.ajax') }}', {
                        _token: '{{ @csrf_token() }}',
                        search: search
                    }, function (data) {
                        if (data == '0') {
                            // $('.typed-search-box').addClass('d-none');
                            $('#search-content').html(null);
                            $('.typed-search-box .search-nothing').removeClass('d-none').html('Sorry, nothing found for <strong>"' + search + '"</strong>');
                            $('.search-preloader').addClass('d-none');

                        } else {
                            $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                            $('#search-content').html(data);
                            $('.search-preloader').addClass('d-none');
                        }
                    });
                } else {
                    $('.typed-search-box').addClass('d-none');
                    $('body').removeClass("typed-search-box-shown");
                }
            }

            function updateNavCart() {
                $.post('{{ route('cart.nav_cart') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                    $('#cart_items').html(data);
                });
            }

            function removeFromCart(key) {
                $.post('{{ route('cart.removeFromCart') }}', {_token: '{{ csrf_token() }}', key: key}, function (data) {
                    updateNavCart();
                    $('#cart-summary').html(data);
                    showFrontendAlert('success', '{{ translate('Item has been removed from cart') }}');
                    $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) - 1);
                });
            }

            function addToCompare(id) {
                $.post('{{ route('compare.addToCompare') }}', {_token: '{{ csrf_token() }}', id: id}, function (data) {
                    $('#compare').html(data);
                    showFrontendAlert('success', '{{ translate('Item has been added to compare list') }}');
                    $('#compare_items_sidenav').html(parseInt($('#compare_items_sidenav').html()) + 1);
                });
            }

            function addToWishList(id) {
                @if (Auth::check() && (Auth::user()->user_type == 'customer' || Auth::user()->user_type == 'seller'))
                $.post('{{ route('wishlists.store') }}', {_token: '{{ csrf_token() }}', id: id}, function (data) {
                    if (data != 0) {
                        $('#wishlist').html(data);
                        showFrontendAlert('success', '{{ translate('Item has been added to wishlist') }}');
                    } else {
                        showFrontendAlert('warning', '{{ translate('Please login first') }}');
                    }
                });
                @else
                showFrontendAlert('warning', '{{ translate('Please login first') }}');
                @endif
            }

            function showAddToCartModal(id) {
                if (!$('#modal-size').hasClass('modal-lg')) {
                    $('#modal-size').addClass('modal-lg');
                }
                $('#addToCart-modal-body').html(null);
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.post('{{ route('cart.showCartModal') }}', {_token: '{{ csrf_token() }}', id: id}, function (data) {
                    $('.c-preloader').hide();
                    $('#addToCart-modal-body').html(data);
                    $('.xzoom, .xzoom-gallery').xzoom({
                        Xoffset: 20,
                        bg: true,
                        tint: '#000',
                        defaultScale: -1
                    });
                    getVariantPrice(true);
                });
            }

            $('#option-choice-form input').on('change', function () {
                getVariantPrice();
            });

            function getVariantPrice(isShow = false) {
                if ($('#option-choice-form input[name=quantity]').val() > 0 && checkAddToCartValidity()) {
                    $.ajax({
                        type: "POST",
                        url: '{{ route('products.variant_price') }}',
                        data: $('#option-choice-form').serializeArray(),
                        success: function (data) {
                            $('#option-choice-form #chosen_price_div').removeClass('d-none');
                            $('#option-choice-form #chosen_price_div #chosen_price').html(data.price);
                            $('#available-quantity').html(data.quantity);
                            $('.input-number').prop('max', data.quantity);
                            console.log(data.quantity);
                            if (parseInt(data.quantity) < 1 && data.digital != 1) {
                                $('.buy-now').hide();
                                if (isShow) {
                                    $('.add-to-cart').show();
                                } else {
                                    $('.add-to-cart').hide();
                                }
                            } else {
                                $('.buy-now').show();
                                $('.add-to-cart').show();
                            }
                        }
                    });
                }
            }

            function checkAddToCartValidity() {
                var names = {};
                $('#option-choice-form input:radio').each(function () { // find unique names
                    names[$(this).attr('name')] = true;
                });
                var count = 0;
                $.each(names, function () { // then count them
                    count++;
                });

                if ($('#option-choice-form input:radio:checked').length == count) {
                    return true;
                }

                return false;
            }

            function addToCart() {
                if (checkAddToCartValidity()) {
                    $('#addToCart').modal();
                    $('.c-preloader').show();
                    $.ajax({
                        type: "POST",
                        url: '{{ route('cart.addToCart') }}',
                        data: $('#option-choice-form').serializeArray(),
                        success: function (data) {
                            $('#addToCart-modal-body').html(null);
                            $('.c-preloader').hide();
                            $('#modal-size').removeClass('modal-lg');
                            $('#addToCart-modal-body').html(data);
                            updateNavCart();
                            $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) + 1);
                        }
                    });
                } else {
                    showFrontendAlert('warning', '{{ translate('Please choose all the options') }}');
                }
            }

            function buyNow() {
                if (checkAddToCartValidity()) {
                    $('#addToCart').modal();
                    $('.c-preloader').show();
                    $.ajax({
                        type: "POST",
                        url: '{{ route('cart.addToCart') }}',
                        data: $('#option-choice-form').serializeArray(),
                        success: function (data) {
                            //$('#addToCart-modal-body').html(null);
                            //$('.c-preloader').hide();
                            //$('#modal-size').removeClass('modal-lg');
                            //$('#addToCart-modal-body').html(data);
                            updateNavCart();
                            $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) + 1);
                            window.location.replace("{{ route('cart') }}");
                        }
                    });
                } else {
                    showFrontendAlert('warning', '{{ translate('Please choose all the options') }}');
                }
            }

            function show_purchase_history_details(order_id) {
                $('#order-details-modal-body').html(null);

                if (!$('#modal-size').hasClass('modal-lg')) {
                    $('#modal-size').addClass('modal-lg');
                }

                $.post('{{ route('purchase_history.details') }}', {
                    _token: '{{ @csrf_token() }}',
                    order_id: order_id
                }, function (data) {
                    $('#order-details-modal-body').html(data);
                    $('#order_details').modal();
                    $('.c-preloader').hide();
                });
            }

            function show_order_details(order_id) {
                $('#order-details-modal-body').html(null);

                if (!$('#modal-size').hasClass('modal-lg')) {
                    $('#modal-size').addClass('modal-lg');
                }

                $.post('{{ route('orders.details') }}', {
                    _token: '{{ @csrf_token() }}',
                    order_id: order_id
                }, function (data) {
                    $('#order-details-modal-body').html(data);
                    $('#order_details').modal();
                    $('.c-preloader').hide();
                });
            }

            function cartQuantityInitialize() {
                $('.btn-number').click(function (e) {
                    e.preventDefault();

                    fieldName = $(this).attr('data-field');
                    type = $(this).attr('data-type');
                    var input = $("input[name='" + fieldName + "']");
                    var currentVal = parseInt(input.val());

                    if (!isNaN(currentVal)) {
                        if (type == 'minus') {

                            if (currentVal > input.attr('min')) {
                                input.val(currentVal - 1).change();
                            }
                            if (parseInt(input.val()) == input.attr('min')) {
                                $(this).attr('disabled', true);
                            }

                        } else if (type == 'plus') {

                            if (currentVal < input.attr('max')) {
                                input.val(currentVal + 1).change();
                            }
                            if (parseInt(input.val()) == input.attr('max')) {
                                $(this).attr('disabled', true);
                            }

                        }
                    } else {
                        input.val(0);
                    }
                });

                $('.input-number').focusin(function () {
                    $(this).data('oldValue', $(this).val());
                });

                $('.input-number').change(function () {

                    minValue = parseInt($(this).attr('min'));
                    maxValue = parseInt($(this).attr('max'));
                    valueCurrent = parseInt($(this).val());

                    name = $(this).attr('name');
                    if (valueCurrent >= minValue) {
                        $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
                    } else {
                        alert('Sorry, the minimum value was reached');
                        $(this).val($(this).data('oldValue'));
                    }
                    if (valueCurrent <= maxValue) {
                        $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
                    } else {
                        alert('Sorry, the maximum value was reached');
                        $(this).val($(this).data('oldValue'));
                    }


                });
                $(".input-number").keydown(function (e) {
                    // Allow: backspace, delete, tab, escape, enter and .
                    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                        // Allow: Ctrl+A
                        (e.keyCode == 65 && e.ctrlKey === true) ||
                        // Allow: home, end, left, right
                        (e.keyCode >= 35 && e.keyCode <= 39)) {
                        // let it happen, don't do anything
                        return;
                    }
                    // Ensure that it is a number and stop the keypress
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });
            }

            function imageInputInitialize() {
                $('.custom-input-file').each(function () {
                    var $input = $(this),
                        $label = $input.next('label'),
                        labelVal = $label.html();

                    $input.on('change', function (e) {
                        var fileName = '';

                        if (this.files && this.files.length > 1)
                            fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                        else if (e.target.value)
                            fileName = e.target.value.split('\\').pop();

                        if (fileName)
                            $label.find('span').html(fileName);
                        else
                            $label.html(labelVal);
                    });

                    // Firefox bug fix
                    $input
                        .on('focus', function () {
                            $input.addClass('has-focus');
                        })
                        .on('blur', function () {
                            $input.removeClass('has-focus');
                        });
                });
            }


        </script>

        @yield('script')

        </body>
        </html>
