@extends('layouts.webview')

@section('content')
    <div id="page-content">
        <section class="py-4">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 mx-auto">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center py-4 mb-4">
                                    <i class="la la-check-circle la-3x text-success mb-3"></i>
                                    <h1 class="h3 mb-3">{{ translate('This Order Cancelled')}}</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection



