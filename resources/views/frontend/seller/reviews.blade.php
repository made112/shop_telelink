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
                                        {{ translate('Product Reviews')}}
                                    </h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{ translate('Home')}}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{ translate('Dashboard')}}</a></li>
                                            <li class="active"><a href="{{ route('reviews.seller') }}">{{ translate('Product Reviews')}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Order history table -->
                        <div class="card no-border mt-4">
                            <div>
                                <table class="table table-sm table-responsive-md">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ translate('Product')}}</th>
                                            <th>{{ translate('Customer')}}</th>
                                            <th>{{ translate('Rating')}}</th>
                                            <th>{{ translate('Comment')}}</th>
                                            <th>{{ translate('Published')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!count($reviews) > 0)
                                            @foreach ($reviews as $key => $value)
                                                @php
                                                    $review = \App\Review::find($value->id);
                                                @endphp
                                                @if($review != null && $review->product != null && $review->user != null)
                                                    <tr>
                                                        <td>
                                                            {{ $key+1 }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('product', $review->product->slug) }}" target="_blank">{{  translate($review->product->name) }}</a>
                                                        </td>
                                                        <td>{{ $review->user->name }} ({{ $review->user->email }})</td>
                                                        <td>
                                                            <div class="star-rating star-rating-sm mt-1">
                                                                @for ($i=0; $i < floor($review->rating); $i++)
                                                                    <i class="fa fa-star active"></i>
                                                                @endfor
                                                                @for ($i=0; $i < ceil(5-$review->rating); $i++)
                                                                    <i class="fa fa-star
                                                                        @if($i==0 && ($review->rating - floor($review->rating)) > 0 && ($review->rating - floor($review->rating)) <= 0.5)
                                                                            half
                                                                        @elseif($i==0 && (ceil($review->rating) - $review->rating) > 0 && (ceil($review->rating) - $review->rating) <= 0.5)
                                                                            active
                                                                        @endif">
                                                                    </i>
                                                                @endfor
                                                            </div>
                                                        </td>
                                                        <td>{{ $review->comment }}</td>
                                                        <td>
                                                            @if ($review->status == 1)
                                                                <span class="badge badge-success">{{  translate('Published') }}</span>
                                                            @else
                                                                <span class="badge badge-danger">{{  translate('Unpublished') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center pt-5 h4" colspan="100%">
                                                    <i class="la la-meh-o d-block heading-1 alpha-5"></i>
                                                    <span class="d-block">{{ __('No review found.') }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="pagination-wrapper py-4">
                            <ul class="pagination justify-content-end">
                                {{ $reviews->links() }}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
