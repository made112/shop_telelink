@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions)))
@section('content')


<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title text-center">{{ translate('Language Info') }}</h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" action="{{ route('languages.update', $language->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <div class="col-lg-3">
                        <label class="control-label">{{ translate('Name') }}</label>
                    </div>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" name="name" placeholder="{{ translate('Name') }}" value="{{ $language->name }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3">
                        <label class="control-label">{{ translate('Code') }}</label>
                    </div>
                    <div class="col-lg-6">
                        <select class="country-flag-select" name="code">
                            @foreach(\File::files(base_path('public/frontend/images/icons/flags')) as $path)
                                <option value="{{ pathinfo($path)['filename'] }}" data-flag="{{ my_asset('frontend/images/icons/flags/'.pathinfo($path)['filename'].'.png') }}" @if($language->code == pathinfo($path)['filename']) selected @endif> {{ strtoupper(pathinfo($path)['filename']) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-12 text-right">
                        <button class="btn btn-purple" type="submit">{{ translate('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@endif
