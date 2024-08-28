@extends('frontend.layouts.app')

@section('content')
    @include('frontend.inc.alert_review_shop')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @include('frontend.inc.seller_side_nav')
                </div>

                <div class="col-lg-9">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{ translate('Shop Settings')}}
                                        <a href="{{ route('shop.visit', $shop->slug) }}" class="btn btn-link btn-sm" target="_blank">({{ translate('Visit Shop')}})<i class="la la-external-link"></i>)</a>
                                    </h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{ translate('Home')}}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{ translate('Dashboard')}}</a></li>
                                            <li class="active"><a href="{{ route('shops.index') }}">{{ translate('Shop Settings')}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form class="" id="shops_update" action="{{ route('shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="PATCH">
                            @csrf
                            <div class="card bg-white mt-4">
                                <div class="card-header mb-0 h6 px-3 py-2">
                                    {{ translate('Basic info')}}
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Shop Name')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="{{ translate('Shop Name')}}" name="name" value="{{ $shop->name }}" >
                                            @error('name')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping')
{{--                                        <div class="row">--}}
{{--                                            <div class="col-md-2">--}}
{{--                                                <label>{{ translate('Shipping Cost')}} <span class="required-star">*</span></label>--}}
{{--                                            </div>--}}
{{--                                            <div class="col-md-10">--}}
{{--                                                <input type="number" min="0" class="form-control mb-3" placeholder="{{ translate('Shipping Cost')}}" name="shipping_cost" value="{{ $shop->shipping_cost }}" required>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}



                                    @endif
                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label>{{ translate('Pickup Points')}} <span class="required-star"></span></label>
                                        </div>
                                        <div class="col-md-10">
                                            <select class="form-control mb-3 selectpicker @error('pick_up_point_id') is-invalid @enderror" data-placeholder="{{ translate('Select Pickup Point') }}" id="pick_up_point" name="pick_up_point_id[]" multiple>
                                                @foreach (\App\PickupPoint::all() as $pick_up_point)
                                                    @if (Auth::user()->shop->pick_up_point_id != null)
                                                        <option value="{{ $pick_up_point->id }}" @if (in_array($pick_up_point->id, json_decode(Auth::user()->shop->pick_up_point_id))) selected @endif>{{ $pick_up_point->name }}</option>
                                                    @else
                                                        <option value="{{ $pick_up_point->id }}">{{ $pick_up_point->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Logo')}} <small>({{ translate('120x120')}})</small></label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="file" name="logo" id="file-2" class="custom-input-file custom-input-file--4 @error('logo') is-invalid @enderror" data-multiple-caption="{count} files selected" accept="image/*" />

                                            <label for="file-2" class="mw-100 ">
                                                <span></span>
                                                <strong>
                                                    <i class="fa fa-upload"></i>
                                                    {{ translate('Choose image')}}
                                                </strong>
                                            </label>
                                            @error('logo')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Commercial Interest')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('commercial_interest') is-invalid @enderror" placeholder="{{ translate('Commercial Interest')}}" name="commercial_interest" value="{{ $shop->commercial_interest }}" >
                                            @error('commercial_interest')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Phone Number')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" placeholder="{{ translate('Phone Number')}}" name="phone" value="{{ $shop->phone }}" >
                                            @error('phone')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Address')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('address') is-invalid @enderror" placeholder="{{ translate('Address')}}" name="address" value="{{ $shop->address }}" >
                                            @error('address')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Meta Title')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" placeholder="{{ translate('Meta Title')}}" name="meta_title" value="{{ $shop->meta_title }}" >
                                            @error('meta_title')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Meta Description')}} <span class="required-star">*</span></label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <textarea name="meta_description" rows="6" class="form-control @error('meta_description') is-invalid @enderror" >{{ $shop->meta_description }}</textarea>
                                            @error('meta_description')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-styled btn-base-1">{{ translate('Save')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form class="" action="{{ route('shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="PATCH">
                            @csrf
                            <div class="card bg-white mt-4">
                                <div class="card-header mb-0 h6 px-3 py-2">
                                    {{ translate('Slider Settings')}}
                                </div>
                                <div class="card-body p-3">
                                    <div id="shop-slider-images">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>{{ translate('Slider Images')}} <small>(1400x400)</small></label>
                                            </div>
                                            <div class="offset-2 offset-md-0 col-10 col-md-10 mb-3">
                                                <div class="row">
                                                    @if ($shop->sliders != null)
                                                        @foreach (json_decode($shop->sliders) as $key => $sliders)
                                                            <div class="col-md-6">
                                                                <div class="img-upload-preview">
                                                                    <img loading="lazy"  src="{{ my_asset($sliders) }}" alt="" class="img-fluid">
                                                                    <input type="hidden" name="previous_sliders[]" value="{{ $sliders }}">
                                                                    <button type="button" class="btn btn-danger close-btn remove-files"><i class="fa fa-times"></i></button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <input type="file" name="sliders[]" id="slide-0" class="custom-input-file custom-input-file--4" data-multiple-caption="{count} files selected" multiple accept="image/*" />
                                                <label for="slide-0" class="mw-100 m-0">
                                                    <span></span>
                                                    <strong>
                                                        <i class="fa fa-upload"></i>
                                                        {{ translate('Choose image')}}
                                                    </strong>
                                                </label>
                                                @error('sliders')
                                                <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                                @enderror
                                                @error('sliders.*')
                                                <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-info mb-3" onclick="add_more_slider_image()">{{  translate('Add More') }}</button>
                                    </div>
                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-styled btn-base-1">{{ translate('Save')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form class="" action="{{ route('shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="delivery_settings" value="1">
                            @csrf
                            <div class="card bg-white mt-4">
                                <div class="card-header mb-0 h6 px-3 py-2">
                                    {{ translate('Delivery Settings')}}
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer bg-light-base-1">
                                                <input onchange="toggleDeliverySetting(this)" type="checkbox" @if($shop->deal_with == 1) checked @endif name="deal_with" value="deal_with">
                                                <span class="checkbox-box"></span>
                                                <span class="d-block ml-2 strong-600">
                                            {{  translate('Deal with iBuy') }}
                                        </span>
                                            </label>
                                        </div>
                                        <div class="col-6 delivery_settings">
                                            <label class="d-flex align-items-center p-3 border rounded gry-bg c-pointer bg-light-base-1">
                                                <input type="checkbox" name="collective_delivery" @if($shop->collective_delivery == 1) checked @endif value="collective_delivery">
                                                <span class="checkbox-box"></span>
                                                <span class="d-block ml-2 strong-600">
                                            {{  translate('Collective Delivery Included') }}
                                        </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="delivery_settings">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>{{ translate('Shipping Cost Jerusalem')}}</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="number" min="0" class="form-control mb-3" placeholder="{{ translate('Shipping Cost Jerusalem')}}" name="shipping_cost_j" value="{{ $shop->shipping_cost_j }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>{{ translate('Shipping Cost West Bank')}}</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="number" min="0" class="form-control mb-3" placeholder="{{ translate('Shipping Cost West Bank')}}" name="shipping_cost_wb" value="{{ $shop->shipping_cost_wb }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>{{ translate('Shipping Cost Occupied Interior')}}</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="number" min="0" class="form-control mb-3" placeholder="{{ translate('Shipping Cost Occupied Interior')}}" name="shipping_cost_oi" value="{{ $shop->shipping_cost_oi }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>{{ translate('Free Shipping')}}</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="number" min="0" class="form-control mb-3" placeholder="{{ translate('Free Shipping')}}" name="shipping_free" value="{{ $shop->shipping_free }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-styled btn-base-1">{{ translate('Save')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form class="" action="{{ route('shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="PATCH">
                            @csrf
                            <div class="card bg-white mt-4">
                                <div class="card-header mb-0 h6 px-3 py-2">
                                    {{ translate('Jawwal Pay Account')}}
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Username')}}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('pay_username') is-invalid @enderror" placeholder="{{ translate('Username')}}" name="pay_username" value="{{ isset($shop->jawwal_payment) && is_string($shop->jawwal_payment) && $shop->jawwal_payment != null ? json_decode($shop->jawwal_payment)->pay_username : null }}">
                                            @error('pay_username')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Password')}}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('pay_password') is-invalid @enderror" placeholder="{{ translate('Password')}}" name="pay_password" value="{{ isset($shop->jawwal_payment) && is_string($shop->jawwal_payment) && $shop->jawwal_payment != null ? json_decode($shop->jawwal_payment)->pay_password : null }}">
                                            @error('pay_password')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('IFrame')}}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('pay_iframe') is-invalid @enderror" placeholder="{{ translate('IFrame')}}" name="pay_iframe" value="{{ isset($shop->jawwal_payment) && is_string($shop->jawwal_payment) && $shop->jawwal_payment != null ? json_decode($shop->jawwal_payment)->pay_iframe : null }}">
                                            @error('pay_iframe')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Integration Payment ID')}}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('pay_integration_id') is-invalid @enderror" placeholder="{{ translate('Integration Payment ID')}}" name="pay_integration_id" value="{{ isset($shop->jawwal_payment) && is_string($shop->jawwal_payment) && $shop->jawwal_payment != null ? json_decode($shop->jawwal_payment)->pay_integration_id : null }}">
                                            @error('pay_integration_id')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-styled btn-base-1">{{ translate('Save')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form class="" action="{{ route('shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="PATCH">
                            @csrf
                            <div class="card bg-white mt-4">
                                <div class="card-header mb-0 h6 px-3 py-2">
                                    {{ translate('Social Media Link')}}
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label ><i class="line-height-1_8 size-24 mr-2 fa fa-facebook bg-facebook c-white text-center"></i>{{ translate('Facebook')}} </label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('facebook') is-invalid @enderror" placeholder="{{ translate('Facebook')}}" name="facebook" value="{{ $shop->facebook }}">
                                            @error('facebook')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label><i class="line-height-1_8 size-24 mr-2 fa fa-twitter bg-twitter c-white text-center"></i>{{ translate('Twitter')}} </label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('twitter') is-invalid @enderror" placeholder="{{ translate('Twitter')}}" name="twitter" value="{{ $shop->twitter }}">
                                            @error('twitter')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label><i class="line-height-1_8 size-24 mr-2 fa fa-google bg-google c-white text-center"></i>{{ translate('Google')}} </label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('google') is-invalid @enderror" placeholder="{{ translate('Google')}}" name="google" value="{{ $shop->google }}">
                                            @error('google')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label><i class="line-height-1_8 size-24 mr-2 fa fa-youtube bg-youtube c-white text-center"></i>{{ translate('Youtube')}} </label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control @error('youtube') is-invalid @enderror" placeholder="{{ translate('Youtube')}}" name="youtube" value="{{ $shop->youtube }}">
                                            @error('youtube')
                                            <div class="text text-danger" style="font-size: 10px;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-styled btn-base-1">{{ translate('Save')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script>
        var slide_id = 1;
        function add_more_slider_image(){
            var shopSliderAdd =  '<div class="row">';
            shopSliderAdd +=  '<div class="col-2">';
            shopSliderAdd +=  '<button type="button" onclick="delete_this_row(this)" class="btn btn-link btn-icon text-danger"><i class="fa fa-trash-o"></i></button>';
            shopSliderAdd +=  '</div>';
            shopSliderAdd +=  '<div class="col-10">';
            shopSliderAdd +=  '<input type="file" name="sliders[]" id="slide-'+slide_id+'" class="custom-input-file custom-input-file--4" data-multiple-caption="{count} files selected" multiple accept="image/*" />';
            shopSliderAdd +=  '<label for="slide-'+slide_id+'" class="mw-100 mb-3">';
            shopSliderAdd +=  '<span></span>';
            shopSliderAdd +=  '<strong>';
            shopSliderAdd +=  '<i class="fa fa-upload"></i>';
            shopSliderAdd +=  "{{ translate('Choose image')}}";
            shopSliderAdd +=  '</strong>';
            shopSliderAdd +=  '</label>';
            shopSliderAdd +=  '</div>';
            shopSliderAdd +=  '</div>';
            $('#shop-slider-images').append(shopSliderAdd);

            slide_id++;
            imageInputInitialize();
        }
        function delete_this_row(em){
            $(em).closest('.row').remove();
        }


        $(document).ready(function(){
            var deal_with_input = $('input[name="deal_with"]');
            toggleDeliverySetting(deal_with_input);
            $('.remove-files').on('click', function(){
                $(this).parents(".col-md-6").remove();
            });

            $('body').on('click', function () {
                validateFile($('input[type="file"]'), ['png', 'jpeg', 'jpg', 'svg']);
            });

        });
        function toggleDeliverySetting(ele) {
            var checked = $(ele).is(':checked');
            if (checked) {
                $(ele).parent().parent().addClass('offset-3');
                $('.delivery_settings').addClass('deal_with');
            }else {
                $(ele).parent().parent().removeClass('offset-3');
                $('.delivery_settings').removeClass('deal_with');
            }

        }
        // $('input[name="deal_with"]').onchange(to)
    </script>
@endsection
