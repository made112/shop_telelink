@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{auto_version(my_asset('css/vendors.css'))}}">
    <link type="text/css" href="{{ auto_version(my_asset('frontend/css/slick.css')) }}" rel="stylesheet" media="all">

    <style>
        .h-200px {
            height: 200px !important;
        }

        .img-fit {
            width: unset !important;
            max-width: 100%;
            object-fit: cover;
        }

        .dropdown-toggle::after {
            content: none !important;
        }

        .slick-slide:focus {
            outline: 0;
        }

        .slick-dots {
            position: absolute;
            margin: 0;
            padding: 0;
            right: 5%;
            bottom: 20px;
        }

        .slick-carousel:not(.slick-initialized) > div {
            display: none;
        }

        .slick-dots li {
            list-style: none;
            display: inline-block;
            margin: 0 5px;
        }

        .slick-dots li button {
            background: #fff;
            height: 4px;
            width: 25px;
            border: 0;
            color: transparent;
            font-size: 0;
        }

        .slick-prev,
        .slick-next {
            opacity: 0.3;
            transition: all 0.3s;
            width: 30px;
            background: rgba(0, 0, 0, 0.1);
            height: 60px;
            top: calc(50% - 30px);
            position: absolute;
            border: 0;
            z-index: 99;
            font-size: 20px;
            color: #fff;
        }

        .slick-prev {
            left: 0;
        }

        .slick-next {
            right: 0;
        }

        :hover .slick-prev,
        :hover .slick-next {
            opacity: 1;
        }

        .slick-next .next-icon,
        .slick-prev .prev-icon {
            background: none;
            height: 30px;
            width: 30px;
            position: absolute;
            top: calc(50% - 15px);
            left: calc(50% - 15px);
        }

        .slick-next .next-icon:after,
        .slick-prev .prev-icon:after {
            position: absolute;
            content: "";
            top: 7px;
            width: 16px;
            height: 16px;
            border: solid white;
            border-width: 0 2px 2px 0;
        }

        .slick-next .next-icon:after {
            -webkit-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            transform: rotate(-45deg);
            left: 4px;
        }

        .slick-prev .prev-icon:after {
            -webkit-transform: rotate(135deg);
            -ms-transform: rotate(135deg);
            transform: rotate(135deg);
            left: 12px;
        }

        .aiz-card-box {
            overflow: hidden;
        }

        .aiz-card-box:hover .aiz-p-hov-icon a {
            transform: translateX(0);
            -webkit-transform: translateX(0);
        }

        .opacity-50 {
            opacity: 0.5 !important;
        }

        .fs-15 {
            font-size: 1.375rem !important;
        }

        .text-primary {
            color: var(--primary) !important;
        }

        .lh-1-4 {
            line-height: 1.4 !important;
        }

        .fs-13 {
            font-size: 1.125rem !important;
        }

        .carousel-box {
            padding: 0 10px;
        }

    </style>
@endsection

@section('content')
    @if(env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
        <div class="">
            <div class="alert alert-danger d-flex align-items-center">
                {{translate('Please Configure SMTP Setting to work all email sending funtionality')}},
                <a class="alert-link ml-2"
                   href="{{ route('smtp_settings.index') }}">{{ translate('Configure Now') }}</a>
            </div>
        </div>
    @endif
    {{--    @if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))--}}
    <div class="row gutters-10">
        <div class="col-lg-6">
            <div class="row gutters-10">
                <div class="col-6">
                    <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">{{ translate('Total') }}</span>
                                {{ translate('Product category') }}
                            </div>
                            <div class="h3 fw-700 mb-3 mt-0 text-white">{{ \App\Category::all()->count() }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                            <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                  d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                        </svg>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">{{ translate('Total') }}</span>
                                {{ translate('Product sub sub category') }}
                            </div>
                            <div class="h3 fw-700 mb-3 mt-0 text-white">{{ \App\SubSubCategory::all()->count() }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                            <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                  d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                        </svg>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">{{ translate('Total') }}</span>
                                {{ translate('Product sub category') }}
                            </div>
                            <div class="h3 fw-700 mb-3 text-white mt-0">{{ \App\SubCategory::all()->count() }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                            <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                  d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                        </svg>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-grad-4 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">{{ translate('Total') }}</span>
                                {{ translate('Product brand') }}
                            </div>
                            <div class="h3 fw-700 mb-3 text-white mt-0">{{ \App\Brand::all()->count() }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                            <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                  d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row gutters-10">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0 fs-14">{{ translate('Products') }}</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="pie-1" class="w-100" height="305"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0 fs-14">{{ translate('Sellers') }}</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="pie-2" class="w-100" height="305"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--    @endif--}}


    {{--    @if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))--}}
    <div class="row gutters-10">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fs-14">{{ translate('Category wise product sale') }}</h6>
                </div>
                <div class="card-body">
                    <canvas id="graph-1" class="w-100" height="500"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fs-14">{{ translate('Category wise product stock') }}</h6>
                </div>
                <div class="card-body">
                    <canvas id="graph-2" class="w-100" height="500"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{--    @endif--}}

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0 fs-14">{{ translate('Top 12 Products') }}</h6>
        </div>
        <div class="card-body">
            <div class="slick-carousel" data-slick-items="5" data-slick-xl-items="4" data-slick-lg-items="3"
                 data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
                @foreach (filter_products(\App\Product::where('published', 1)->orderBy('num_of_sale', 'desc'))->limit(12)->get() as $key => $product)
                    <div class="carousel-box">
                        <div
                            class="aiz-card-box border border-light rounded shadow-sm hov-shadow-md mb-2 has-transition bg-white">
                            <div class="position-relative">
                                <a href="{{ route('product', $product->slug) }}" class="d-block">
                                    <img
                                        class="img-fit lazyload mx-auto h-200px"
                                        src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                        data-src="{{ my_asset($product->thumbnail_img) }}"
                                        onerror="this.onerror=null;this.src='{{ my_asset('frontend/images/placeholder.jpg') }}';"
                                        alt="{{  __($product->name) }}">
                                </a>
                            </div>
                            <div class="p-md-3 p-2 text-left">
                                <div class="fs-15">
                                    @if(home_base_price($product->id) != home_discounted_base_price($product->id))
                                        <del class="fw-600 opacity-50 mr-1">{{ home_base_price($product->id) }}</del>
                                    @endif
                                    <span
                                        class="fw-700 text-primary">{{ home_discounted_base_price($product->id) }}</span>
                                </div>
                                <div class="rating rating-sm mt-1">
                                    {{ renderStarRating($product->rating) }}
                                </div>
                                <h3 class="fw-600 fs-13 lh-1-4 mb-0">
                                    <a href="{{ route('product', $product->slug) }}"
                                       class="d-block text-truncate text-reset">{{ $product->name }}</a>
                                </h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script src="{{ auto_version(my_asset('frontend/js/slick.min.js')) }}"></script>
    <script type="text/javascript">
        function slickInit() {
            if ($(".slick-carousel").length > 0) {
                $(".slick-carousel")
                    .not(".slick-initialized")
                    .each(function () {
                        var $this = $(this);

                        var slidesRtl = false;

                        var slidesPerViewXs = $this.data("slick-xs-items");
                        var slidesPerViewSm = $this.data("slick-sm-items");
                        var slidesPerViewMd = $this.data("slick-md-items");
                        var slidesPerViewLg = $this.data("slick-lg-items");
                        var slidesPerViewXl = $this.data("slick-xl-items");
                        var slidesPerView = $this.data("slick-items");

                        var slidesCenterMode = $this.data("slick-center");
                        var slidesArrows = $this.data("slick-arrows");
                        var slidesDots = $this.data("slick-dots");
                        var slidesRows = $this.data("slick-rows");
                        var slidesAutoplay = $this.data("slick-autoplay");

                        slidesPerViewXs = !slidesPerViewXs
                            ? slidesPerView
                            : slidesPerViewXs;
                        slidesPerViewSm = !slidesPerViewSm
                            ? slidesPerView
                            : slidesPerViewSm;
                        slidesPerViewMd = !slidesPerViewMd
                            ? slidesPerView
                            : slidesPerViewMd;
                        slidesPerViewLg = !slidesPerViewLg
                            ? slidesPerView
                            : slidesPerViewLg;
                        slidesPerViewXl = !slidesPerViewXl
                            ? slidesPerView
                            : slidesPerViewXl;
                        slidesPerView = !slidesPerView ? 1 : slidesPerView;
                        slidesCenterMode = !slidesCenterMode ? false : slidesCenterMode;
                        slidesArrows = !slidesArrows ? true : slidesArrows;
                        slidesDots = !slidesDots ? false : slidesDots;
                        slidesRows = !slidesRows ? 1 : slidesRows;
                        slidesAutoplay = !slidesAutoplay ? false : slidesAutoplay;

                        if ($("html").attr("dir") === "rtl") {
                            slidesRtl = true;
                        }

                        $this.slick({
                            slidesToShow: slidesPerView,
                            autoplay: slidesAutoplay,
                            dots: slidesDots,
                            arrows: slidesArrows,
                            infinite: true,
                            rtl: slidesRtl,
                            rows: slidesRows,
                            centerPadding: "0px",
                            centerMode: slidesCenterMode,
                            speed: 300,
                            prevArrow:
                                '<button type="button" class="slick-prev"><i class="la la-angle-left"></i></button>',
                            nextArrow:
                                '<button type="button" class="slick-next"><i class="la la-angle-right"></i></button>',
                            responsive: [
                                {
                                    breakpoint: 1500,
                                    settings: {
                                        slidesToShow: slidesPerViewXl,
                                    },
                                },
                                {
                                    breakpoint: 1200,
                                    settings: {
                                        slidesToShow: slidesPerViewLg,
                                    },
                                },
                                {
                                    breakpoint: 992,
                                    settings: {
                                        slidesToShow: slidesPerViewMd,
                                    },
                                },
                                {
                                    breakpoint: 768,
                                    settings: {
                                        slidesToShow: slidesPerViewSm,
                                        dots: true,
                                        arrows: false,
                                    },
                                },
                                {
                                    breakpoint: 576,
                                    settings: {
                                        slidesToShow: slidesPerViewXs,
                                        dots: true,
                                        arrows: false,
                                    },
                                },
                            ],
                        });
                    });
            }
        }

        $(document).ready(function () {
            @php
                $products = DB::table('products')
                 ->where('published', 1)
                 ->select('added_by',  DB::raw('count(*) total'))
                 ->groupBy('added_by')
                 ->get();
            $sellersCount = DB::table('sellers')
                 ->select('verification_status',  DB::raw('count(*) total'))
                 ->groupBy('verification_status')
                 ->get();

            $categories = \App\Category::all();

            @endphp
            AIZ.plugins.chart('#pie-1', {
                type: 'doughnut',
                data: {
                    labels: [
                        '{{translate('Total published products')}}',
                        '{{translate('Total sellers products')}}',
                        '{{translate('Total admin products')}}'
                    ],
                    datasets: [
                        {
                            data: [
                                {{ $products[0]->total + $products[1]->total }},
                                {{ $products[1]->total }},
                                {{ $products[0]->total }}
                            ],
                            backgroundColor: [
                                "#fd3995",
                                "#34bfa3",
                                "#5d78ff",
                                '#fdcb6e',
                                '#d35400',
                                '#8e44ad',
                                '#006442',
                                '#4D8FAC',
                                '#CA6924',
                                '#C91F37'
                            ]
                        }
                    ]
                },
                options: {
                    cutoutPercentage: 70,
                    legend: {
                        labels: {
                            fontFamily: 'Poppins',
                            boxWidth: 10,
                            usePointStyle: true
                        },
                        onClick: function () {
                            return '';
                        },
                        position: 'bottom'
                    }
                }
            });

            AIZ.plugins.chart('#pie-2', {
                type: 'doughnut',
                data: {
                    labels: [
                        '{{translate('Total sellers')}}',
                        '{{translate('Total approved sellers')}}',
                        '{{translate('Total pending sellers')}}'
                    ],
                    datasets: [
                        {
                            data: [
                                {{ $sellersCount[0]->total + $sellersCount[1]->total }},
                                {{ $sellersCount[1]->total }},
                                {{ $sellersCount[0]->total }}
                            ],
                            backgroundColor: [
                                "#fd3995",
                                "#34bfa3",
                                "#5d78ff",
                                '#fdcb6e',
                                '#d35400',
                                '#8e44ad',
                                '#006442',
                                '#4D8FAC',
                                '#CA6924',
                                '#C91F37'
                            ]
                        }
                    ]
                },
                options: {
                    cutoutPercentage: 70,
                    legend: {
                        labels: {
                            fontFamily: 'Montserrat',
                            boxWidth: 10,
                            usePointStyle: true
                        },
                        onClick: function () {
                            return '';
                        },
                        position: 'bottom'
                    }
                }
            });
            var sfs = {
                labels: [
                    @foreach ($categories as $key => $category)
                        '{{ $category->name }}',
                    @endforeach
                ],
                datasets: [
                    @foreach ($categories as $key => $category)
                    {{ \App\Product::where('category_id', $category->id)->sum('num_of_sale') }},
                    @endforeach
                ]
            }
            AIZ.plugins.chart('#graph-1', {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($categories as $key => $category)
                            '{{ $category->name }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: '{{ translate('Number of sale') }}',
                        data: [
                            @foreach ($categories as $key => $category)
                            {{ \App\Product::where('category_id', $category->id)->sum('num_of_sale') }},
                            @endforeach
                        ],
                        backgroundColor: [
                            @foreach ($categories as $key => $category)
                                'rgba(55, 125, 255, 0.4)',
                            @endforeach
                        ],
                        borderColor: [
                            @foreach ($categories as $key => $category)
                                'rgba(55, 125, 255, 1)',
                            @endforeach
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            gridLines: {
                                color: '#f2f3f8',
                                zeroLineColor: '#f2f3f8'
                            },
                            ticks: {
                                fontColor: "#8b8b8b",
                                fontFamily: 'Poppins',
                                fontSize: 10,
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                color: '#f2f3f8'
                            },
                            ticks: {
                                fontColor: "#8b8b8b",
                                fontFamily: 'Poppins',
                                fontSize: 10
                            }
                        }]
                    },
                    legend: {
                        labels: {
                            fontFamily: 'Poppins',
                            boxWidth: 10,
                            usePointStyle: true
                        },
                        onClick: function () {
                            return '';
                        },
                    }
                }
            });
            AIZ.plugins.chart('#graph-2', {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($categories as $key => $category)
                            '{{ $category->name }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: '{{ translate('Number of Stock') }}',
                        data: [
                            @foreach ($categories as $key => $category)
                            @php
                                $products = \App\Product::where('category_id', $category->id)->get();
                                $qty = 0;
                                foreach ($products as $key => $product) {
                                    if ($product->variant_product) {
                                        foreach ($product->stocks as $key => $stock) {
                                            $qty += $stock->qty;
                                        }
                                    }
                                    else {
                                        $qty = $product->current_stock;
                                    }
                                }
                            @endphp
                            {{ $qty }},
                            @endforeach
                        ],
                        backgroundColor: [
                            @foreach ($categories as $key => $category)
                                'rgba(253, 57, 149, 0.4)',
                            @endforeach
                        ],
                        borderColor: [
                            @foreach ($categories as $key => $category)
                                'rgba(253, 57, 149, 1)',
                            @endforeach
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            gridLines: {
                                color: '#f2f3f8',
                                zeroLineColor: '#f2f3f8'
                            },
                            ticks: {
                                fontColor: "#8b8b8b",
                                fontFamily: 'Poppins',
                                fontSize: 10,
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                color: '#f2f3f8'
                            },
                            ticks: {
                                fontColor: "#8b8b8b",
                                fontFamily: 'Poppins',
                                fontSize: 10
                            }
                        }]
                    },
                    legend: {
                        labels: {
                            fontFamily: 'Poppins',
                            boxWidth: 10,
                            usePointStyle: true
                        },
                        onClick: function () {
                            return '';
                        },
                    }
                }
            });
            slickInit();

        })
    </script>
@endsection
