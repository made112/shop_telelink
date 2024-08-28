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
                                        {{ translate('Add Your Product')}}
                                    </h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{ translate('Home')}}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{ translate('Dashboard')}}</a></li>
                                            <li><a href="{{ route('seller.products') }}">{{ translate('Products')}}</a></li>
                                            <li class="active"><a href="{{ route('seller.products.upload') }}">{{ translate('Add New Product')}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="form-validation" action="{{route('products.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
                            @csrf
                            <ul class="nav nav-pills pt-2" id="pills-tab" role="tablist">
                                @php
                                    $default = \App\Language::where('code', config('translatable.DEFAULT_LANGUAGE'))->first();
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link text-capitalize <?php if(config('translatable.DEFAULT_LANGUAGE') == $default->code) echo 'active show'; ?>" id="pills-contact-tab" data-toggle="pill" href="#pills-{{ $default->code }}" role="tab" aria-controls="pills-contact" aria-selected="<?php if(env('DEFAULT_LANGUAGE') == $default->code) echo 'true'; else echo 'false'; ?>">{{translate('Default')}} {{ $default->name }}</a>
                                </li>
                                @foreach (\App\Language::where('code', '!=', config('translatable.DEFAULT_LANGUAGE'))->get() as $key => $language)
                                    <li class="nav-item">
                                        <a class="nav-link text-capitalize" id="pills-contact-tab" data-toggle="pill" href="#pills-{{ $language->code }}" role="tab" aria-controls="pills-contact" aria-selected="false">{{ $language->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                    		<input type="hidden" name="added_by" value="seller">
                            <div class="tab-content" id="pills-tabContent">
                                @if ($default)
                                    <div class="tab-pane fade <?php if(config('translatable.DEFAULT_LANGUAGE') == $default->code) echo 'active show'; ?>" id="pills-{{ $default->code }}" role="tabpanel" aria-labelledby="pills-home-tab">
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('General')}}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Product Name')}} <span class="required-star">*</span></label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input required type="text" class="form-control @error('name.'.$default->code) is-invalid @enderror" name="name[{{$default->code}}]" placeholder="{{ translate('Product Name')}}" >
                                                        @error('name.'.$default->code)
                                                        <div class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Product Category')}} <span class="required-star">*</span></label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <div class="form-control c-pointer @error('category_id') is-invalid @enderror" data-toggle="modal" data-target="#categorySelectModal" id="product_category">{{ translate('Select a category')}}</div>
                                                        <input required type="hidden" name="category_id" id="category_id" value="" >
                                                        <input required type="hidden" name="subcategory_id" id="subcategory_id" value="" >
                                                        <input type="hidden" name="subsubcategory_id" id="subsubcategory_id" value="">
                                                        @error('category_id')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                        @error('subcategory_id')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                        @error('subsubcategory_id')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row" id="quantity">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Quantity') }} <span class="required-star">*</span></label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input required type="number" min="0" value="0" step="1" class="form-control @error('current_stock') is-invalid @enderror" name="current_stock" placeholder="{{ translate('Quantity') }}" >
                                                        @error('current_stock')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
{{--                                                @php--}}
{{--                                                    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();--}}
{{--                                                @endphp--}}
{{--                                                @if ($refund_request_addon != null && $refund_request_addon->activated == 1)--}}
{{--                                                    <div class="row mt-2">--}}
{{--                                                        <label class="col-md-2">{{ translate('Refundable')}} <span class="required-star">*</span></label>--}}
{{--                                                        <div class="col-md-10">--}}
{{--                                                            <label class="switch" style="margin-top:5px;">--}}
{{--                                                                <input type="checkbox" required name="refundable" checked>--}}
{{--                                                                <span class="slider round"></span>--}}
{{--                                                            </label>--}}
{{--                                                            @error('refundable')--}}
{{--                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>--}}
{{--                                                            @enderror--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                @endif--}}
                                                <div class="row mt-2">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Product Tag')}} <span class="required-star">*</span></label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input required type="text" class="form-control mb-3 tagsInput required" name="tags[{{$default->code}}][]" placeholder="{{ translate('Type & hit enter') }}" data-role="tagsinput" >
                                                        <span>{{ __('Example: the uses of the product, category of expected buyers.') }}</span>
                                                        @error('tags.'.$default->code.'*')
                                                        <div class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</div>
                                                        <script !src="">
                                                            $(document).ready(function (){
                                                                $('input[name="tags[{{$default->code}}][]"]').parent().find('.bootstrap-tagsinput').addClass('form-control is-invalid')
                                                            });
                                                        </script>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row form-group mt-2">
                                                    <label class="col-md-2" for="signinSrEmail">{{translate('Gallery Images')}} <span class="required-star">*</span></label>
                                                    <div class="col-md-10">
                                                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                            </div>
                                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                            <input required type="hidden" name="photos" class="selected-files">
                                                        </div>
                                                        @error('photos')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        <script !src="">
                                                            $(document).ready(function (){
                                                                $('body').find('input[name="photos"]').parent().find('.file-amount').addClass('is-invalid')
                                                            });
                                                        </script>
                                                        @enderror
                                                        <div class="file-preview box sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row mt-2">
                                                    <label class="col-md-2" for="signinSrEmail">{{translate('Thumbnail')}} <small>(290x300)</small> <span class="required-star">*</span></label>
                                                    <div class="col-md-10">
                                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                            </div>
                                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                            <input required type="hidden" name="thumbnail_img" class="selected-files">
                                                        </div>
                                                        @error('thumbnail_img')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        <script !src="">
                                                            $(document).ready(function (){
                                                                $('body').find('input[name="thumbnail_img"]').parent().find('.file-amount').addClass('is-invalid')
                                                            });
                                                        </script>
                                                        @enderror
                                                        <div class="file-preview box sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="unit_price">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Unit Price') }} <span class="required-star">*</span></label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input required type="number" min="0" value="0" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" name="unit_price" placeholder="{{ translate('Unit Price') }} ({{ translate('Base Price') }})" >
                                                        @error('unit_price')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row" id="purchase_price">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Purchase Price') }} <span class="required-star">*</span></label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input required type="number" min="0" value="0" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror" name="purchase_price" placeholder="{{ translate('Purchase Price')}}" >
                                                        @error('purchase_price')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row" id="discount">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Discount')}} <span class="required-star">*</span></label>
                                                    </div>
                                                    <div class="col-8 mb-3">
                                                        <input required type="number" min="0" value="0" step="0.01" class="form-control @error('discount') is-invalid @enderror" name="discount" placeholder="{{ translate('Discount') }}" >
                                                        @error('discount')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-4 col-md-2">
                                                        <div class="mb-3">
                                                            <select required class="form-control selectpicker @error('discount_type') is-invalid @enderror" name="discount_type" data-minimum-results-for-search="Infinity">
                                                                <option value="percent">%</option>
                                                                <option value="amount">$</option>
                                                            </select>
                                                            @error('discount_type')
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('Description')}}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Description')}} <span class="required-star">*</span></label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <div class="mb-3">
                                                            <textarea required class="editor @error('description.'.$default->code) is-invalid @enderror" name="description[{{$default->code}}]"></textarea>
                                                            @error('description.'.$default->code)
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('Customer Choice')}}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row mb-3">
                                                    <div class="col-8 col-md-3 order-1 order-md-0">
                                                        <input type="text" class="form-control" value="{{ translate('Colors')}}" disabled>
                                                    </div>
                                                    <div class="col-12 col-md-7 col-xl-8 order-3 order-md-0 mt-2 mt-md-0">
                                                        <select class="form-control color-var-select" name="colors[]" id="colors" multiple>
                                                            @foreach (\App\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                                <option value="{{ $color->code }}">{{ $color->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-4 col-xl-1 col-md-2 order-2 order-md-0 text-right">
                                                        <label class="switch" style="margin-top:5px;">
                                                            <input value="1" type="checkbox" name="colors_active" checked>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-3">
                                                        <label>{{ translate('Attributes')}}</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="">
                                                            <select name="choice_attributes[]" id="choice_attributes" class="form-control selectpicker" multiple data-placeholder="{{ translate('Choose Attributes') }}">
                                                                @foreach (\App\Attribute::all() as $key => $attribute)
                                                                    <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}</p>
                                                </div>
                                                <div id="customer_choice_options">

                                                </div>
                                                {{-- <div class="row">
                                                    <div class="col-2">
                                                        <button type="button" class="btn btn-info" onclick="add_more_customer_choice_option()">{{  translate('Add More Customer Choice Option') }}</button>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('Price') }}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Commission Percentage') }}</label>
                                                    </div>
                                                    <div class="col-10 mb-3">
                                                        <input type="text" value="{{\App\BusinessSetting::where('type', 'vendor_commission')->first()->value}} %" disabled class="form-control disabled" placeholder="{{ translate('Commission Percentage') }}" >
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Net price received') }}</label>
                                                    </div>
                                                    <div class="col-10 mb-3">
                                                        <input type="text" value="0" disabled class="form-control disabled" id="net_price_recieve" placeholder="{{ translate('Net price received') }}" >
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12" id="sku_combination">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('Additional options')}}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Product Brand')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <div class="">
                                                            <select class="form-control selectpicker @error('brand_id') is-invalid @enderror" data-placeholder="{{ translate('Select a brand') }}" id="brands" name="brand_id">
                                                                <option value="">{{ ('Select Brand') }}</option>
                                                                @foreach (\App\Brand::all() as $brand)
                                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('brand_id')
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Product Unit')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <select class="form-control demo-select2-placeholder" name="unit" id="unit">
                                                            <option value="">{{ ('Select Unit') }}</option>
                                                            @foreach (\App\Unit::all() as $unit)
                                                                <option value="{{ $unit->code }}">{{ $unit->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('unit')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Minimum Qty.')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input type="number" class="form-control @error('min_qty') is-invalid @enderror" name="min_qty" value="1" min="1" >
                                                        @error('min_qty')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2" for="signinSrEmail">{{translate('PDF Specification')}}</label>
                                                    <div class="col-md-10">
                                                        <div class="input-group" data-toggle="aizuploader" data-type="document">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                            </div>
                                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                            <input type="hidden" name="pdf" class="selected-files">
                                                        </div>
                                                        <div class="file-preview box sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $pos_addon = \App\Addon::where('unique_identifier', 'pos_system')->first();
                                                @endphp
                                                @if ($pos_addon != null && $pos_addon->activated == 1)
                                                    <div class="row mt-2">
                                                        <label class="col-md-2">{{ translate('Barcode')}}</label>
                                                        <div class="col-md-10 mb-3">
                                                            <input type="text" class="form-control @error('barcode') is-invalid @enderror" name="barcode" placeholder="{{  translate('Barcode') }}">
                                                            @error('barcode')
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('Videos')}}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Video From')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <div class="">
                                                            <select class="form-control selectpicker @error('video_provider') is-invalid @enderror" data-minimum-results-for-search="Infinity" name="video_provider">
                                                                <option value="youtube">{{ translate('Youtube')}}</option>
                                                                <option value="dailymotion">{{ translate('Dailymotion')}}</option>
                                                                <option value="vimeo">{{ translate('Vimeo')}}</option>
                                                            </select>
                                                            @error('video_provider')
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Video URL')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input type="text" class="form-control @error('video_link') is-invalid @enderror" name="video_link" placeholder="{{ translate('Video link')}}">
                                                        @error('video_link')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror  @error('video_link')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('Meta Tags')}}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Meta Title')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input type="text" name="meta_title[{{$default->code}}]" class="form-control @error('meta_title.'.$default->code) is-invalid @enderror" placeholder="{{ translate('Meta Title')}}">
                                                        @error('meta_title.'.$default->code)
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Description')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <textarea name="meta_description[{{$default->code}}]" rows="8" class="form-control @error('meta_description.'.$default->code) is-invalid @enderror"></textarea>
                                                        @error('meta_description.'.$default->code)
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2" for="signinSrEmail">{{ translate('Meta Image') }}</label>
                                                    <div class="col-md-10">
                                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                            </div>
                                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                            <input type="hidden" name="meta_img" class="selected-files">
                                                        </div>
                                                        @error('meta_img')
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        <script !src="">
                                                            $(document).ready(function (){
                                                                $('body').find('input[name="meta_img"]').parent().find('.file-amount').addClass('is-invalid')
                                                            });
                                                        </script>
                                                        @enderror
                                                        <div class="file-preview box sm">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'product_wise_shipping')
                                            <div class="form-box bg-white mt-4">
                                                <div class="form-box-title px-3 py-2">
                                                    {{ translate('Shipping') }}
                                                </div>
                                                <div class="form-box-content p-3">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label>{{ translate('Flat Rate') }}</label>
                                                        </div>
                                                        <div class="col-md-8 mb-3">
                                                            <input type="number" min="0" step="0.01" value="0" class="form-control @error('flat_shipping_cost') is-invalid @enderror" name="flat_shipping_cost" placeholder="{{ translate('Flat Rate Cost') }}">
                                                            @error('flat_shipping_cost')
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="switch" style="margin-top:5px;">
                                                                <input type="radio" name="shipping_type" value="flat_rate" checked>
                                                                <span class="slider round"></span>
                                                            </label>
                                                            @error('shipping_type')
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label>{{ translate('Free Shipping') }}</label>
                                                        </div>
                                                        <div class="col-md-8 mb-3">
                                                            <input type="number" min="0" step="0.01" value="0" class="form-control @error('free_shipping_cost') is-invalid @enderror" name="free_shipping_cost" value="0" disabled placeholder="{{ translate('Flat Rate Cost') }}">
                                                            @error('free_shipping_cost')
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="switch" style="margin-top:5px;">
                                                                <input type="radio" name="shipping_type" value="free" checked>
                                                                <span class="slider round"></span>
                                                            </label>
                                                            @error('shipping_type')
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @foreach (\App\Language::where('code', '!=', config('translatable.DEFAULT_LANGUAGE'))->get() as $key => $language)
                                    <div class="tab-pane fade" id="pills-{{ $language->code }}" role="tabpanel" aria-labelledby="pills-home-tab">
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('General')}}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Product Name')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input type="text" class="form-control @error('name.'.$language->code) is-invalid @enderror" name="name[{{$language->code}}]" placeholder="{{ translate('Product Name')}}" >
                                                        @error('name.'.$language->code)
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Product Tag')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input type="text" class="form-control tagsInput @error('tags.'.$language->code.'*') is-invalid @enderror" name="tags[{{$language->code}}][]" placeholder="{{ translate('Type & hit enter') }}" data-role="tagsinput" >
                                                        <span>{{ __('Example: the uses of the product, category of expected buyers.') }}</span>

                                                        @error('tags.'.$language->code.'*')
                                                        <div class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</div>
                                                        <script !src="">
                                                            $(document).ready(function (){
                                                                $('input[name="tags[{{$language->code}}][]"]').parent().find('.bootstrap-tagsinput').addClass('form-control is-invalid')
                                                            });
                                                        </script>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('Description')}}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Description')}}</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <div class="mb-3">
                                                            <textarea class="editor @error('description.'.$language->code) is-invalid @enderror" name="description[{{$language->code}}]"></textarea>
                                                            @error('description.'.$language->code)
                                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-box bg-white mt-4">
                                            <div class="form-box-title px-3 py-2">
                                                {{ translate('Meta Tags')}}
                                            </div>
                                            <div class="form-box-content p-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Meta Title')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <input type="text" name="meta_title[{{$language->code}}]" class="form-control @error('meta_title.'.$language->code) is-invalid @enderror" placeholder="{{ translate('Meta Title')}}">
                                                        @error('meta_title.'.$language->code)
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>{{ translate('Description')}}</label>
                                                    </div>
                                                    <div class="col-md-10 mb-3">
                                                        <textarea name="meta_description[{{$language->code}}]" rows="8" class="form-control @error('meta_description.'.$language->code) is-invalid @enderror"></textarea>
                                                        @error('meta_description.'.$language->code)
                                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-box mt-4 text-right">
                                <button type="submit" class="btn btn-styled btn-base-1">{{  translate('Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="categorySelectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ translate('Select Category') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="target-category heading-6">
                        <span class="mr-3">{{ translate('Target Category')}}:</span>
                        <span>{{ translate('category')}} > {{ translate('subcategory')}} > {{ translate('Subsubcategory')}}</span>
                    </div>
                    <div class="row no-gutters modal-categories mt-4 mb-2">
                        <div class="col-4">
                            <div class="modal-category-box c-scrollbar">
                                <div class="sort-by-box">
                                    <form role="form" class="search-widget">
                                        <input class="form-control input-lg" type="text" placeholder="{{ __('Search Category') }}" onkeyup="filterListItems(this, 'categories')">
                                        <button type="button" class="btn-inner">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-category-list has-right-arrow">
                                    <ul id="categories">
                                        @foreach ($categories as $key => $category)
                                            <li onclick="get_subcategories_by_category(this, {{ $category->id }})">{{  __($category->name) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="modal-category-box c-scrollbar" id="subcategory_list">
                                <div class="sort-by-box">
                                    <form role="form" class="search-widget">
                                        <input class="form-control input-lg" type="text" placeholder="{{ translate('Search SubCategory') }}" onkeyup="filterListItems(this, 'subcategories')">
                                        <button type="button" class="btn-inner">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-category-list has-right-arrow">
                                    <ul id="subcategories">

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="modal-category-box c-scrollbar" id="subsubcategory_list">
                                <div class="sort-by-box">
                                    <form role="form" class="search-widget">
                                        <input class="form-control input-lg" type="text" placeholder="{{ translate('Search SubSubCategory') }}" onkeyup="filterListItems(this, 'subsubcategories')">
                                        <button type="button" class="btn-inner">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-category-list">
                                    <ul id="subsubcategories">

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('cancel')}}</button>
                    <button type="button" class="btn btn-primary" onclick="closeModal()">{{ translate('Confirm')}}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        var category_name = "";
        var subcategory_name = "";
        var subsubcategory_name = "";

        var category_id = null;
        var subcategory_id = null;
        var subsubcategory_id = null;

        $(document).ready(function(){
            $('input[name="unit_price"]').on('input', function() {
                let unit_price = parseInt($(this).val());
                let purchase_price = parseInt($('input[name="purchase_price"]').val());
                if ((unit_price != null && unit_price > 0) && (purchase_price != null && purchase_price > 0) ) {
                    if (purchase_price <= unit_price) {
                        auto_discount(unit_price, purchase_price);
                    }else {
                        $(this).val(0);
                        $('input[name="purchase_price"]').val(0);
                        $('input[name="discount"]').val(0);
                        showFrontendAlert('danger', '{{translate('Unit price must be great than Purchase price.')}}');
                    }
                }else {
                    $('input[name="discount"]').val(0);
                }
                netPriceRecieve(unit_price);
            });

            $('input[name="purchase_price"]').on('input', function() {
                let purchase_price = parseInt($(this).val());
                let unit_price = parseInt($('input[name="unit_price"]').val());
                if ((unit_price != null && unit_price > 0) && (purchase_price != null && purchase_price > 0) ) {
                    if (purchase_price <= unit_price) {
                        auto_discount(unit_price, purchase_price);
                    }else {
                        $(this).val(0);
                        $('input[name="unit_price"]').val(0);
                        $('input[name="discount"]').val(0);
                        showFrontendAlert('danger', '{{translate('Unit price must be great than Purchase price.')}}');
                    }
                }else {
                    $('input[name="discount"]').val(0);
                }
            });
            $(".form-validation").validate({
                ignore: "",
                rules: {
                    category_id: "required",
                    subcategory_id: "required",
                },
                messages: {
                    category_id: "Field category required\n",
                    subcategory_id: "Field sub category required\n",
                }
            });
            $('#subcategory_list').hide();
            $('#subsubcategory_list').hide();

            $('body').on('click', function (){
                validateFile($("input[name='pdf']") ,['pdf'], '', 2000000);
                validateFile($("input[type='file'][id!='file-6']") ,['png','jpeg', 'jpg', 'svg']);
            })
        });

        function list_item_highlight(el){
            $(el).parent().children().each(function(){
                $(this).removeClass('selected');
            });
            $(el).addClass('selected');
        }

        function get_subcategories_by_category(el, cat_id){
            list_item_highlight(el);
            category_id = cat_id;
            subcategory_id = null;
            subsubcategory_id = null;
            category_name = $(el).html();
            $('#subcategories').html(null);
            $('#subsubcategory_list').hide();
            $.post('{{ route('subcategories.get_subcategories_by_category') }}',{_token:'{{ csrf_token() }}', category_id:category_id}, function(data){
                for (var i = 0; i < data.length; i++) {
                    $('#subcategories').append('<li onclick="get_subsubcategories_by_subcategory(this, '+data[i].id+')">'+data[i].name+'</li>');
                }
                $('#subcategory_list').show();
            });
        }

        function get_subsubcategories_by_subcategory(el, subcat_id){
            list_item_highlight(el);
            subcategory_id = subcat_id;
            subsubcategory_id = null;
            subcategory_name = $(el).html();
            $('#subsubcategories').html(null);
            $.post('{{ route('subsubcategories.get_subsubcategories_by_subcategory') }}',{_token:'{{ csrf_token() }}', subcategory_id:subcategory_id}, function(data){
                for (var i = 0; i < data.length; i++) {
                    $('#subsubcategories').append('<li onclick="confirm_subsubcategory(this, '+data[i].id+')">'+data[i].name+'</li>');
                }
                $('#subsubcategory_list').show();
            });
        }

        function confirm_subsubcategory(el, subsubcat_id){
            list_item_highlight(el);
            subsubcategory_id = subsubcat_id;
            subsubcategory_name = $(el).html();
    	}

        function get_attributes_by_subsubcategory(subsubcategory_id){
    		$.post('{{ route('subsubcategories.get_attributes_by_subsubcategory') }}',{_token:'{{ csrf_token() }}', subsubcategory_id:subsubcategory_id}, function(data){
    		    $('#choice_attributes').html(null);
    		    for (var i = 0; i < data.length; i++) {
    		        $('#choice_attributes').append($('<option>', {
    		            value: data[i].id,
    		            text: data[i].name
    		        }));
    		    }
    		});
    	}

        function filterListItems(el, list){
            filter = el.value.toUpperCase();
            li = $('#'+list).children();
            for (i = 0; i < li.length; i++) {
                if ($(li[i]).html().toUpperCase().indexOf(filter) > -1) {
                    $(li[i]).show();
                } else {
                    $(li[i]).hide();
                }
            }
        }

        function closeModal(){
            if(category_id > 0 && subcategory_id > 0){
                $('#category_id').val(category_id);
                $('#subcategory_id').val(subcategory_id);
                $('#subsubcategory_id').val(subsubcategory_id);
                $('#product_category').html(category_name+'>'+subcategory_name+'>'+subsubcategory_name);
                $('#categorySelectModal').modal('hide');
            }
            else{
                alert('Please choose categories...');
                // console.log(category_id);
                // console.log(subcategory_id);
                // console.log(subsubcategory_id);
            }
        }

        //var i = 0;
        function add_more_customer_choice_option(i, name){
    		$('#customer_choice_options').append('<div class="row mb-3"><div class="col-8 col-md-3 order-1 order-md-0"><input type="hidden" name="choice_no[]" value="'+i+'"><input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="{{ translate('Choice Title') }}" readonly></div><div class="col-12 col-md-7 col-xl-8 order-3 order-md-0 mt-2 mt-md-0"><input type="text" class="form-control tagsInput" name="choice_options_'+i+'[]" placeholder="{{ translate('Enter choice values') }}" onchange="update_sku()"></div><div class="col-4 col-xl-1 col-md-2 order-2 order-md-0 text-right"><button type="button" onclick="delete_row(this)" class="btn btn-link btn-icon text-danger"><i class="fa fa-trash-o"></i></button></div></div>');
    		// i++;
            $('.tagsInput').tagsinput('items');
    	}

    	$('input[name="colors_active"]').on('change', function() {
    	    if(!$('input[name="colors_active"]').is(':checked')){
    			$('#colors').prop('disabled', true);
    		}
    		else{
    			$('#colors').prop('disabled', false);
    		}
    		update_sku();
    	});

    	$('#colors').on('change', function() {
    	    update_sku();
    	});

    	$('input[name="unit_price"]').on('keyup', function() {
    	    update_sku();
    	});

        $('input[name="name"]').on('keyup', function() {
    	    update_sku();
    	});

        $('#choice_attributes').on('change', function() {
    		$('#customer_choice_options').html(null);
    		$.each($("#choice_attributes option:selected"), function(){
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
    		update_sku();
    	});

        function auto_discount(unit_price, purchase_price) {
            let discount = parseFloat(((unit_price - purchase_price) * 100) / unit_price).toFixed(2);
            $('input[name="discount"]').val(discount);
        }
        function netPriceRecieve(price) {
            let net_price = Math.round((price * (100 - {{intval(\App\BusinessSetting::where('type', 'vendor_commission')->first()->value)}})) / 100);
            $('#net_price_recieve').val(net_price);
        }

    	function delete_row(em){
    		$(em).closest('.row').remove();
    		update_sku();
    	}

    	function update_sku(){
            $.ajax({
    		   type:"POST",
    		   url:'{{ route('products.sku_combination') }}',
    		   data:$('#choice_form').serialize(),
    		   success: function(data){
    			   $('#sku_combination').html(data);
    			   if (data.length > 1) {
    				   $('#quantity').hide();
                       $('#unit_price').hide();
                       $('#purchase_price').hide();
                       $('#discount').hide();
                       $('#discount').find('input[name="discount"]').val(0);
    			   }
    			   else {
    			   		$('#quantity').show();
                       $('#unit_price').show();
                       $('#purchase_price').show();
                       $('#discount').show();
    			   }
    		   }
    	   });
    	}

        var photo_id = 2;
        function add_more_slider_image(){
            var photoAdd =  '<div class="row">';
            photoAdd +=  '<div class="col-2">';
            photoAdd +=  '<button type="button" onclick="delete_this_row(this)" class="btn btn-link btn-icon text-danger"><i class="fa fa-trash-o"></i></button>';
            photoAdd +=  '</div>';
            photoAdd +=  '<div class="col-10">';
            photoAdd +=  '<input type="file" name="photos[]" id="photos-'+photo_id+'" class="custom-input-file custom-input-file--4" data-multiple-caption="{count} files selected" multiple accept="image/*" />';
            photoAdd +=  '<label for="photos-'+photo_id+'" class="mw-100 mb-3">';
            photoAdd +=  '<span></span>';
            photoAdd +=  '<strong>';
            photoAdd +=  '<i class="fa fa-upload"></i>';
            photoAdd +=  "{{ translate('Choose image')}}";
            photoAdd +=  '</strong>';
            photoAdd +=  '</label>';
            photoAdd +=  '</div>';
            photoAdd +=  '</div>';
            $('#product-images').append(photoAdd);

            photo_id++;
            imageInputInitialize();
        }
        function delete_this_row(em){
            $(em).closest('.row').remove();
        }

        // $('body').on('change',"input[type='file'][id!='file-6']", function (){
        //     console.log('test')
        //     validateFile($(this) ,['png','jpeg', 'jpg', 'svg']);
        // })

    </script>
@endsection
