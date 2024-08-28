@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
<div>
    <h1 class="page-header text-overflow">{{translate('Add New Product')}}</h1>
</div>
@php
    $keywords = \App\SeoSetting::first()->keyword;
    if ($keywords) {
     $keywords = implode(', ',array_slice(explode(',',$keywords), -6));
    }
@endphp
<div class="row">
	<div class="col-lg-8 col-lg-offset-2">
		<form class="form form-horizontal mar-top form-validation" action="{{route('products.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
			@csrf
			<ul class="nav nav-pills" id="pills-tab" role="tablist">
				@php
					$default = \App\Language::where('code', config('translatable.DEFAULT_LANGUAGE'))->first();
				@endphp
				<li class="nav-item  <?php if(config('translatable.DEFAULT_LANGUAGE') == $default->code) echo 'active'; ?>">
					<a class="nav-link text-capitalize" id="pills-contact-tab" data-toggle="pill" href="#pills-{{ $default->code }}" role="tab" aria-controls="pills-contact" aria-selected="<?php if(env('DEFAULT_LANGUAGE') == $default->code) echo 'true'; else echo 'false'; ?>">{{translate('Default')}} {{ $default->name }}</a>
				</li>
				@foreach (\App\Language::where('code', '!=', config('translatable.DEFAULT_LANGUAGE'))->get() as $key => $language)
					<li class="nav-item">
						<a class="nav-link text-capitalize" id="pills-contact-tab" data-toggle="pill" href="#pills-{{ $language->code }}" role="tab" aria-controls="pills-contact" aria-selected="false">{{ $language->name }}</a>
					</li>
				@endforeach
			</ul>
			<input type="hidden" name="added_by" value="admin">
			<br>
			<div class="tab-content" id="pills-tabContent">
				@if ($default)
				<div class="tab-pane fade <?php if(config('translatable.DEFAULT_LANGUAGE') == $default->code) echo 'in active'; ?>" id="pills-{{ $default->code }}" role="tabpanel" aria-labelledby="pills-home-tab">
					<div class="panel">
						<div class="panel-heading bord-btm">
							<h3 class="panel-title">{{__('Product Information')}}</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Product Name')}} <span class="required-star">*</span></label>
								<div class="col-lg-7">
									<input required type="text" class="form-control @error('name.'.$default->code) is-invalid @enderror" name="name[{{$default->code}}]" placeholder="{{__('Product Name')}}" onchange="update_sku()" >
                                    @error('name.'.$default->code)
                                    <div class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</div>
                                    @enderror
								</div>
							</div>
							<div class="form-group" id="category">
								<label class="col-lg-2 control-label">{{__('Category')}} <span class="required-star">*</span></label>
								<div class="col-lg-7">
									<select required class="form-control demo-select2-placeholder @error('category_id') is-invalid @enderror" name="category_id" id="category_id" >
										@foreach($categories as $category)
											<option value="{{$category->id}}">{{__($category->name)}}</option>
										@endforeach
									</select>
                                    @error('category_id')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
							<div class="form-group" id="subcategory">
								<label class="col-lg-2 control-label">{{__('Subcategory')}} <span class="required-star">*</span></label>
								<div class="col-lg-7">
									<select required class="form-control demo-select2-placeholder" name="subcategory_id" id="subcategory_id" >

									</select>
                                    @error('subcategory_id')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
							<div class="form-group" id="subsubcategory">
								<label class="col-lg-2 control-label">{{__('Sub Subcategory')}}</label>
								<div class="col-lg-7">
									<select class="form-control demo-select2-placeholder" name="subsubcategory_id" id="subsubcategory_id">

									</select>
                                    @error('subsubcategory_id')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
                            <div class="form-group" id="quantity">
                                <label class="col-lg-2 control-label">{{__('Quantity')}} <span class="required-star">*</span></label>
                                <div class="col-lg-7">
                                    <input required type="number" min="0" value="0" step="1" placeholder="{{__('Quantity')}}" name="current_stock" class="form-control @error('current_stock') is-invalid @enderror" >
                                    @error('current_stock')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
                                </div>
                            </div>
                            @php
                                $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                            @endphp
                            @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{__('Refundable')}} <span class="required-star">*</span></label>
                                    <div class="col-lg-7">
                                        <label class="switch" style="margin-top:5px;">
                                            <input required type="checkbox" name="refundable" checked>
                                            @error('refundable')
                                            <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                            @enderror

                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{__('Tags')}} <span class="required-star">*</span></label>
                                <div class="col-lg-7 mb-3 @error('tags.'.$default->code.'*')is-invalid @enderror">
                                    <input required type="text" class="form-control" name="tags[{{$default->code}}][]" placeholder="{{__('Type to add a tag')}}" data-role="tagsinput">
                                    @if($keywords) <div class="alert alert-info">{{ translate('Keywords: ').$keywords }}</div> @endif
                                    @error('tags.'.$default->code.'*')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    <script !src="">
                                        $(document).ready(function (){
                                            $('input[name="tags[{{$default->code}}][]"]').parent().find('.bootstrap-tagsinput').addClass('form-control is-invalid')
                                        });
                                    </script>
                                    @enderror

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="signinSrEmail">{{translate('Gallery Images')}} <span class="required-star">*</span></label>
                                <div class="col-md-8">
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
                                            $('body').find('input[name="photos"]').parent().find('.file-amount').addClass('form-control is-invalid')
                                        });
                                    </script>
                                    @enderror
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="signinSrEmail">{{translate('Thumbnail')}} <small>(290x300)</small> <span class="required-star">*</span></label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" required name="thumbnail_img" class="selected-files">
                                    </div>
                                    @error('thumbnail_img')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    <script !src="">
                                        $(document).ready(function (){
                                            $('body').find('input[name="thumbnail_img"]').parent().find('.file-amount').addClass('form-control is-invalid')
                                        });
                                    </script>
                                    @enderror
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="unit_price">
                                <label class="col-lg-2 control-label">{{__('Unit price')}} <span class="required-star">*</span></label>
                                <div class="col-lg-7">
                                    <input required type="number" min="0" value="0" step="0.01" placeholder="{{__('Unit price')}}" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" >
                                    @error('unit_price')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
                                </div>
                            </div>
                                <div class="form-group" id="purchase_price">
                                <label class="col-lg-2 control-label">{{__('Purchase price')}} <span class="required-star">*</span></label>
                                <div class="col-lg-7">
                                    <input required type="number" min="0" value="0" step="0.01" placeholder="{{__('Purchase price')}}" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" >
                                    @error('purchase_price')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group" id="discount">
                                <label class="col-lg-2 control-label">{{__('Discount')}} <span class="required-star">*</span></label>
                                <div class="col-lg-7">
                                    <input required type="number" min="0" value="0" step="0.01" placeholder="{{__('Discount')}}" name="discount" class="form-control @error('discount') is-invalid @enderror" >
                                    @error('discount')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror

                                </div>
                                <div class="col-lg-1">
                                    <select required class="demo-select2 @error('discount_type') is-invalid @enderror" name="discount_type">
                                        <option value="percent">{{__('Percent')}}</option>
                                        <option value="amount">{{__('Flat')}}</option>
                                    </select>
                                    @error('discount_type')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
                                </div>
                            </div>
						</div>
					</div>
                    <div class="panel">
                        <div class="panel-heading bord-btm">
                            <h3 class="panel-title">{{__('Product Description')}}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{__('Description')}} <span class="required-star">*</span></label>
                                <div class="col-lg-9">
                                    <textarea required class="form-control editor @error('description.'.$default->code) is-invalid @enderror" name="description[{{$default->code}}]"></textarea>
                                    @error('description.'.$default->code)
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading bord-btm">
                            <h3 class="panel-title">{{__('Product Variation')}}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-lg-2">
                                    <input type="text" class="form-control" value="{{__('Colors')}}" disabled>
                                </div>
                                <div class="col-lg-7">
                                    <select class="form-control color-var-select" name="colors[]" id="colors" multiple disabled>
                                        @foreach (\App\Color::orderBy('name', 'asc')->get() as $key => $color)
                                            <option value="{{ $color->code }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label class="switch" style="margin-top:5px;">
                                        <input value="1" type="checkbox" name="colors_active">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-2">
                                    <input type="text" class="form-control" value="{{__('Attributes')}}" disabled>
                                </div>
                                <div class="col-lg-7">
                                    <select name="choice_attributes[]" id="choice_attributes" class="form-control demo-select2" multiple data-placeholder="{{__('Choose Attributes')}}">
                                        @foreach (\App\Attribute::all() as $key => $attribute)
                                            <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <p>{{__('Choose the attributes of this product and then input values of each attribute')}}</p>
                                <br>
                            </div>

                            <div class="customer_choice_options" id="customer_choice_options">

                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading bord-btm">
                            <h3 class="panel-title">{{__('Product price + stock')}}</h3>
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{__('Tax')}}</label>
                                <div class="col-lg-7">
                                    <input type="number" min="0" value="0" step="0.01" placeholder="{{__('Tax')}}" name="tax" class="form-control @error('tax') is-invalid @enderror" >
                                    @error('tax')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-1">
                                    <select class="demo-select2 @error('tax_type') is-invalid @enderror" name="tax_type">
                                        <option value="amount">{{__('Flat')}}</option>
                                        <option value="percent">{{__('Percent')}}</option>
                                    </select>
                                    @error('tax_type')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="sku_combination" id="sku_combination">

                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading bord-btm">
                            <h3 class="panel-title">{{__('Additional options')}}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group" id="brand">
                                <label class="col-lg-2 control-label">{{__('Brand')}}</label>
                                <div class="col-lg-7">
                                    <select class="form-control demo-select2-placeholder" name="brand_id" id="brand_id">
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
                            <div class="form-group" id="units">
                                <label class="col-lg-2 control-label">{{__('Unit')}}</label>
                                <div class="col-lg-7">
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
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{ translate('Minimum Qty.')}}</label>
                                <div class="col-lg-7">
                                    <input type="number" class="form-control @error('min_qty') is-invalid @enderror" name="min_qty" value="1" min="1" >
                                    @error('min_qty')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
                                </div>
                            </div>

                            @php
                                $pos_addon = \App\Addon::where('unique_identifier', 'pos_system')->first();
                            @endphp
                            @if ($pos_addon != null && $pos_addon->activated == 1)
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{__('Barcode')}}</label>
                                    <div class="col-lg-7">
                                        <input type="text" class="form-control @error('barcode') is-invalid @enderror" name="barcode" placeholder="{{ ('Barcode') }}">
                                        @error('barcode')
                                        <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="col-md-2 col-form-label" for="signinSrEmail">{{translate('PDF Specification')}}</label>
                                <div class="col-md-7">
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
                        </div>
                    </div>
                    <div class="panel">
						<div class="panel-heading bord-btm">
							<h3 class="panel-title">{{__('Product Videos')}}</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Video Provider')}}</label>
								<div class="col-lg-7">
									<select class="form-control @error('video_provider') is-invalid @enderror demo-select2-placeholder" name="video_provider" id="video_provider">
										<option value="youtube">{{__('Youtube')}}</option>
										<option value="dailymotion">{{__('Dailymotion')}}</option>
										<option value="vimeo">{{__('Vimeo')}}</option>
									</select>
                                    @error('video_provider')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Video Link')}}</label>
								<div class="col-lg-7">
									<input type="text" class="form-control @error('video_link') is-invalid @enderror" name="video_link" placeholder="{{__('Video Link')}}">
                                    @error('video_link')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
						</div>
					</div>
					@if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'product_wise_shipping')
						<div class="panel">
							<div class="panel-heading bord-btm">
								<h3 class="panel-title">{{__('Product Shipping Cost')}}</h3>
							</div>
							<div class="panel-body">
								<div class="row bord-btm">
									<div class="col-md-2">
										<div class="panel-heading">
											<h3 class="panel-title">{{__('Free Shipping')}}</h3>
										</div>
									</div>
									<div class="col-md-10">
										<div class="form-group">
											<label class="col-lg-2 control-label">{{__('Status')}}</label>
											<div class="col-lg-7">
												<label class="switch" style="margin-top:5px;">
													<input class="form-control @error('shipping_type') is-invalid @enderror" type="radio" name="shipping_type" value="free" checked>
                                                    @error('shipping_type')
                                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                    @enderror
													<span class="slider round"></span>
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-2">
										<div class="panel-heading">
											<h3 class="panel-title">{{__('Flat Rate')}}</h3>
										</div>
									</div>
									<div class="col-md-10">
										<div class="form-group">
											<label class="col-lg-2 control-label">{{__('Status')}}</label>
											<div class="col-lg-7">
												<label class="switch" style="margin-top:5px;">
													<input type="radio" name="shipping_type" value="flat_rate" checked>
                                                    @error('shipping_type')
                                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                    @enderror
													<span class="slider round"></span>
												</label>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-2 control-label">{{__('Shipping cost')}}</label>
											<div class="col-lg-7">
												<input type="number" min="0" value="0" step="0.01" placeholder="{{__('Shipping cost')}}" name="flat_shipping_cost" class="form-control @error('flat_shipping_cost') is-invalid @enderror" >
                                                @error('flat_shipping_cost')
                                                <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                                @enderror

											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endif
					<div class="panel">
						<div class="panel-heading bord-btm">
							<h3 class="panel-title">{{__('SEO Meta Tags')}}</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Meta Title')}}</label>
								<div class="col-lg-7">
									<input type="text" class="form-control @error('meta_title.'.$default->code) is-invalid @enderror" name="meta_title[{{$default->code}}]" placeholder="{{__('Meta Title')}}">
                                    @error('meta_title.'.$language->code)
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Description')}}</label>
								<div class="col-lg-7">
									<textarea name="meta_description[{{$default->code}}]" rows="8" class="form-control @error('meta_description.'.$default->code) is-invalid @enderror"></textarea>
                                    @error('meta_description.'.$language->code)
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
                            <div class="form-group">
                                <label class="col-md-2 col-form-label" for="signinSrEmail">{{ translate('Meta Image') }}</label>
                                <div class="col-md-8">
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
                                            $('body').find('input[name="meta_img"]').parent().find('.file-amount').addClass('form-control is-invalid')
                                        });
                                    </script>
                                    @enderror
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
				</div>
				@endif
				@foreach (\App\Language::where('code', '!=', config('translatable.DEFAULT_LANGUAGE'))->get() as $key => $language)
				<div class="tab-pane fade" id="pills-{{ $language->code }}" role="tabpanel" aria-labelledby="pills-home-tab">
					<div class="panel">
						<div class="panel-heading bord-btm">
							<h3 class="panel-title">{{__('Product Information')}}</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Product Name')}}</label>
								<div class="col-lg-7">
									<input type="text" class="form-control @error('name.'.$language->code) is-invalid @enderror" name="name[{{$language->code}}]" placeholder="{{__('Product Name')}}" onchange="update_sku()" >
                                    @error('name.'.$language->code)
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Tags')}}</label>
								<div class="col-lg-7">
									<input type="text" class="form-control @error('tags.'.$language->code.'*') is-invalid @enderror " name="tags[{{$language->code}}][]" placeholder="{{__('Type to add a tag')}}" data-role="tagsinput">
                                    @if($keywords) <div class="alert alert-info">{{ translate('Keywords: ').$keywords }}</div> @endif
                                    @error('tags.'.$language->code.'*')
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
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
					<div class="panel">
						<div class="panel-heading bord-btm">
							<h3 class="panel-title">{{__('Product Description')}}</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Description')}}</label>
								<div class="col-lg-9">
									<textarea class="form-control editor @error('description.'.$language->code) is-invalid @enderror" name="description[{{$language->code}}]"></textarea>
                                    @error('description.'.$language->code)
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
						</div>
					</div>

					<div class="panel">
						<div class="panel-heading bord-btm">
							<h3 class="panel-title">{{__('SEO Meta Tags')}}</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Meta Title')}}</label>
								<div class="col-lg-7">
									<input type="text" class="form-control @error('meta_title.'.$language->code) is-invalid @enderror" name="meta_title[{{$language->code}}]" placeholder="{{__('Meta Title')}}">
                                    @error('meta_title.'.$language->code)
                                    <span class="text-danger" style="margin-top: 3px; font-size: 10px">{{ translate($message) }}</span>
                                    @enderror
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Description')}}</label>
								<div class="col-lg-7">
									<textarea name="meta_description[{{$language->code}}]" rows="8" class="form-control @error('meta_description.'.$language->code) is-invalid @enderror">

                                    </textarea>
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
			<div class="mar-all text-right">
				<button type="submit" name="button" class="btn btn-info">{{translate('Add New Product')}}</button>
			</div>
		</form>
	</div>
</div>


@endsection

@section('script')

<script type="text/javascript">
	function add_more_customer_choice_option(i, name){
		$('#customer_choice_options').append('<div class="form-group"><div class="col-lg-2"><input type="hidden" name="choice_no[]" value="'+i+'"><input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="Choice Title" readonly></div><div class="col-lg-7"><input type="text" class="form-control" name="choice_options_'+i+'[]" placeholder="Enter choice values" data-role="tagsinput" onchange="update_sku()"></div></div>');

		$("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
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

	function delete_row(em){
		$(em).closest('.form-group').remove();
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
                   $('input[name="unit_price"]').removeAttr('required');
                   $('input[name="purchase_price"]').removeAttr('required');
                   $('input[name="discount"]').removeAttr('required');
				   $('#purchase_price').hide();
				   $('#discount').hide();
				   $('#discount').find('input[name="discount"]').val(0);
			   }
			   else {
                   $('input[name="unit_price"]').attr('required', 'required');
                   $('input[name="purchase_price"]').attr('required', 'required');
                   $('input[name="discount"]').attr('required', 'required');
                   $('#quantity').show();
                   $('#unit_price').show();
                   $('#purchase_price').show();
                   $('#discount').show();
			   }
		   }
	   });
	}

	function get_subcategories_by_category(){
		var category_id = $('#category_id').val();
		$.post('{{ route('subcategories.get_subcategories_by_category') }}',{_token:'{{ csrf_token() }}', category_id:category_id}, function(data){
		    $('#subcategory_id').html(null);
		    for (var i = 0; i < data.length; i++) {
		        $('#subcategory_id').append($('<option>', {
		            value: data[i].id,
		            text: data[i].name
		        }));
		        $('.demo-select2').select2();
		    }
		    get_subsubcategories_by_subcategory();
		});
	}

	function get_subsubcategories_by_subcategory(){
		var subcategory_id = $('#subcategory_id').val();
		$.post('{{ route('subsubcategories.get_subsubcategories_by_subcategory') }}',{_token:'{{ csrf_token() }}', subcategory_id:subcategory_id}, function(data){
		    $('#subsubcategory_id').html(null);
			$('#subsubcategory_id').append($('<option>', {
				value: null,
				text: null
			}));
		    for (var i = 0; i < data.length; i++) {
		        $('#subsubcategory_id').append($('<option>', {
		            value: data[i].id,
		            text: data[i].name
		        }));
		        $('.demo-select2').select2();
		    }
		    //get_brands_by_subsubcategory();
			//get_attributes_by_subsubcategory();
		});
	}

	function get_brands_by_subsubcategory(){
		var subsubcategory_id = $('#subsubcategory_id').val();
		$.post('{{ route('subsubcategories.get_brands_by_subsubcategory') }}',{_token:'{{ csrf_token() }}', subsubcategory_id:subsubcategory_id}, function(data){
		    $('#brand_id').html(null);
		    for (var i = 0; i < data.length; i++) {
		        $('#brand_id').append($('<option>', {
		            value: data[i].id,
		            text: data[i].name
		        }));
		        $('.demo-select2').select2();
		    }
		});
	}

	function get_attributes_by_subsubcategory(){
		var subsubcategory_id = $('#subsubcategory_id').val();
		$.post('{{ route('subsubcategories.get_attributes_by_subsubcategory') }}',{_token:'{{ csrf_token() }}', subsubcategory_id:subsubcategory_id}, function(data){
		    $('#choice_attributes').html(null);
		    for (var i = 0; i < data.length; i++) {
		        $('#choice_attributes').append($('<option>', {
		            value: data[i].id,
		            text: data[i].name
		        }));
		    }
			$('.demo-select2').select2();
		});
	}

	$(document).ready(function(){
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
	    get_subcategories_by_category();
		$("#photos").spartanMultiImagePicker({
			fieldName:        'photos[]',
			maxCount:         10,
			rowHeight:        '200px',
			groupClassName:   'col-md-4 col-sm-4 col-xs-6',
			maxFileSize:      '',
			dropFileLabel : "Drop Here",
			onExtensionErr : function(index, file){
				console.log(index, file,  'extension err');
				alert('Please only input png or jpg type file')
			},
			onSizeErr : function(index, file){
				console.log(index, file,  'file size too big');
				alert('File size too big');
			}
		});
		$("#thumbnail_img").spartanMultiImagePicker({
			fieldName:        'thumbnail_img',
			maxCount:         1,
			rowHeight:        '200px',
			groupClassName:   'col-md-4 col-sm-4 col-xs-6',
			maxFileSize:      '',
			dropFileLabel : "Drop Here",
			onExtensionErr : function(index, file){
				console.log(index, file,  'extension err');
				alert('Please only input png or jpg type file')
			},
			onSizeErr : function(index, file){
				console.log(index, file,  'file size too big');
				alert('File size too big');
			}
		});
		$("#featured_img").spartanMultiImagePicker({
			fieldName:        'featured_img',
			maxCount:         1,
			rowHeight:        '200px',
			groupClassName:   'col-md-4 col-sm-4 col-xs-6',
			maxFileSize:      '',
			dropFileLabel : "Drop Here",
			onExtensionErr : function(index, file){
				console.log(index, file,  'extension err');
				alert('Please only input png or jpg type file')
			},
			onSizeErr : function(index, file){
				console.log(index, file,  'file size too big');
				alert('File size too big');
			}
		});
		$("#flash_deal_img").spartanMultiImagePicker({
			fieldName:        'flash_deal_img',
			maxCount:         1,
			rowHeight:        '200px',
			groupClassName:   'col-md-4 col-sm-4 col-xs-6',
			maxFileSize:      '',
			dropFileLabel : "Drop Here",
			onExtensionErr : function(index, file){
				console.log(index, file,  'extension err');
				alert('Please only input png or jpg type file')
			},
			onSizeErr : function(index, file){
				console.log(index, file,  'file size too big');
				alert('File size too big');
			}
		});
		$("#meta_photo").spartanMultiImagePicker({
			fieldName:        'meta_img',
			maxCount:         1,
			rowHeight:        '200px',
			groupClassName:   'col-md-4 col-sm-4 col-xs-6',
			maxFileSize:      '',
			dropFileLabel : "Drop Here",
			onExtensionErr : function(index, file){
				console.log(index, file,  'extension err');
				alert('Please only input png or jpg type file')
			},
			onSizeErr : function(index, file){
				console.log(index, file,  'file size too big');
				alert('File size too big');
			}
		});
	});

	$('#category_id').on('change', function() {
	    get_subcategories_by_category();
	});

	$('#subcategory_id').on('change', function() {
	    get_subsubcategories_by_subcategory();
	});

	$('#subsubcategory_id').on('change', function() {
	    // get_brands_by_subsubcategory();
		//get_attributes_by_subsubcategory();
	});

	$('#choice_attributes').on('change', function() {
		$('#customer_choice_options').html(null);
		$.each($("#choice_attributes option:selected"), function(){
			//console.log($(this).val());
            add_more_customer_choice_option($(this).val(), $(this).text());
        });
		update_sku();
	});


    function auto_discount(unit_price, purchase_price) {
        let discount = parseFloat(((unit_price - purchase_price) * 100) / unit_price).toFixed(2);
        $('input[name="discount"]').val(discount);
    }

    $(document).ready(function (){
        $('input[name="unit_price"]').on('input',function() {
            let unit_price = parseInt($(this).val());
            let purchase_price = parseInt($('input[name="purchase_price"]').val());
            if ((unit_price != null && unit_price > 0) && (purchase_price != null && purchase_price > 0) ) {
                if (purchase_price <= unit_price) {
                    auto_discount(unit_price, purchase_price);
                }else {
                    $(this).val(0);
                    $('input[name="purchase_price"]').val(0);
                    $('input[name="discount"]').val(0);
                    showAlert('danger', '{{translate('Unit price must be great than Purchase price.')}}');
                }
            }else {
                $('input[name="discount"]').val(0);
            }
        });

        $('input[name="purchase_price"]').on('input',function() {
            let purchase_price = parseInt($(this).val());
            let unit_price = parseInt($('input[name="unit_price"]').val());
            if ((unit_price != null && unit_price > 0) && (purchase_price != null && purchase_price > 0) ) {
                if (purchase_price <= unit_price) {
                    auto_discount(unit_price, purchase_price);
                }else {
                    $(this).val(0);
                    $('input[name="unit_price"]').val(0);
                    $('input[name="discount"]').val(0);
                    showAlert('danger', '{{translate('Unit price must be great than Purchase price.')}}');
                }
            }else {
                $('input[name="discount"]').val(0);
            }
        });
        $('body').find('.bootstrap-tagsinput').addClass('form-control')
    });

</script>

@endsection
@endif
