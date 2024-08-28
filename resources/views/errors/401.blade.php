@extends('frontend.layouts.app')

@section('content')
    <section class="text-center py-6 my-5">
        <img src="{{ static_asset('img/nothing.svg') }}" class="mw-100 mx-auto mb-5" height="300">
        <h1 class="fw-700">{{ translate('Unauthorize!') }}</h1>
        <p class="fs-16 opacity-60">{{ translate('The page you are looking for has been unauthorized to access.') }}</p>
    </section>
@endsection
