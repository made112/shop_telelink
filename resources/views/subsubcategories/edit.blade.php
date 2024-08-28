@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Sub Subcategory Information')}}</h3>
        </div>
        <ul class="nav nav-pills px-5" id="pills-tab" role="tablist">
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
        <br>
        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('subsubcategories.update', $subsubcategory->id) }}" method="POST" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PATCH">
            @csrf
            <div class="tab-content" id="pills-tabContent">
				@if ($default)
				<div class="tab-pane fade <?php if(config('translatable.DEFAULT_LANGUAGE') == $default->code) echo 'in active'; ?>" id="pills-{{ $default->code }}" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="name">{{translate('Name')}}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name[{{$default->code}}]" class="form-control @error('name.'. $default->code) is-invalid @enderror"  value="{{$subsubcategory->getTranslation('name', $default->code)}}">
                                @error('name.'. $default->code)
                                <div class="text text-danger"
                                     style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="name">{{translate('Category')}}</label>
                            <div class="col-sm-9">
                                <select name="category_id" id="category_id" class="form-control demo-select2" >
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{__($category->name)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="name">{{translate('Subcategory')}}</label>
                            <div class="col-sm-9">
                                <select name="sub_category_id" id="sub_category_id" class="form-control @error('category_id') is-invalid @enderror demo-select2" >

                                </select>
                                @error('category_id')
                                <div class="text text-danger"
                                     style="margin-top: 3px;font-size: 10px;">{{translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{translate('Meta Title')}}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('meta_title.'. $default->code) is-invalid @enderror" name="meta_title[{{$default->code}}]" value="{{$subsubcategory->getTranslation('meta_title', $default->code)}}" placeholder="{{translate('Meta Title')}}">
                                @error('meta_title.'. $default->code)
                                <div class="text text-danger"
                                     style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{translate('Description')}}</label>
                            <div class="col-sm-9">
                                <textarea name="meta_description[{{$default->code}}]" rows="8" class="form-control @error('meta_description.'. $language->code) is-invalid @enderror">{{$subsubcategory->getTranslation('meta_description', $default->code)}}</textarea>
                                @error('meta_description.'. $default->code)
                                <div class="text text-danger"
                                     style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="name">{{translate('Slug')}}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{translate('Slug')}}" id="slug" name="slug" value="{{ $subsubcategory->slug }}" class="form-control @error('meta_description.'. $default->code) is-invalid @enderror">

                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @foreach (\App\Language::where('code', '!=', config('translatable.DEFAULT_LANGUAGE'))->get() as $key => $language)
				<div class="tab-pane fade" id="pills-{{ $language->code }}" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="name">{{translate('Name')}}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name[{{$language->code}}]" class="form-control @error('name.'. $language->code) is-invalid @enderror"  value="{{$subsubcategory->getTranslation('name', $language->code)}}">
                                @error('name.'. $language->code)
                                <div class="text text-danger"
                                     style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{translate('Meta Title')}}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('meta_title.'. $language->code) is-invalid @enderror" name="meta_title[{{$language->code}}]" value="{{ $subsubcategory->getTranslation('meta_title', $language->code) }}" placeholder="{{translate('Meta Title')}}">
                                @error('meta_title.'. $language->code)
                                <div class="text text-danger"
                                     style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{translate('Description')}}</label>
                            <div class="col-sm-9">
                                <textarea name="meta_description[{{$language->code}}]" rows="8" class="form-control @error('meta_description.'. $language->code) is-invalid @enderror">{{ $subsubcategory->getTranslation('meta_description', $language->code) }}</textarea>
                                @error('meta_description.'. $language->code)
                                <div class="text text-danger"
                                     style="margin-top: 3px;font-size: 10px;">{{ translate($message) }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
				</div>
				@endforeach
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

    function get_subcategories_by_category(){
        var category_id = $('#category_id').val();
        $.post('{{ route('subcategories.get_subcategories_by_category') }}',{_token:'{{ csrf_token() }}', category_id:category_id}, function(data){
            $('#sub_category_id').html(null);
            for (var i = 0; i < data.length; i++) {
                $('#sub_category_id').append($('<option>', {
                    value: data[i].id,
                    text: data[i].name
                }));
                $('.demo-select2').select2();
            }
        });
    }

    $('.demo-select2').select2();

    $(document).ready(function(){

        $("#category_id > option").each(function() {
            if(this.value == '{{$subsubcategory->subcategory->category_id}}'){
                $("#category_id").val(this.value).change();
            }
        });

        get_subcategories_by_category();
    });

    $('#category_id').on('change', function() {
        get_subcategories_by_category();
    });

</script>

@endsection
@endif
