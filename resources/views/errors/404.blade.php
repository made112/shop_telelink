@extends('frontend.layouts.app')

@section('content')
    <section class="text-center py-6 my-5">
        <img src="{{ static_asset('img/404.svg') }}" class="mw-100 mx-auto mb-5" height="300">
        <h1 class="fw-700">{{ translate('Page Not Found!') }}</h1>
        <p class="fs-16 opacity-60">{{ translate('The page you are looking for has not been found on our server.') }}</p>
    </section>
@endsection
