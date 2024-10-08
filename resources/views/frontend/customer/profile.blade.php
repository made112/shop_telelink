@extends('frontend.layouts.app')

@section('content')

    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @if(Auth::user()->user_type == 'seller')
                        @include('frontend.inc.seller_side_nav')
                    @elseif(Auth::user()->user_type == 'customer')
                        @include('frontend.inc.customer_side_nav')
                    @endif
                </div>

                <div class="col-lg-9">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-12">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{ translate('Manage Profile') }}
                                    </h2>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{ translate('Home') }}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{ translate('Dashboard') }}</a></li>
                                            <li class="active"><a href="{{ route('profile') }}">{{ translate('Manage Profile') }}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="form-validation" id="customer_profile_update" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <div class="card bg-white mt-4">
                                <div class="card-header px-3 py-2 mb-0 h6">
                                    {{ translate('Basic info') }}
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Your Name') }}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input required type="text" class="form-control" placeholder="{{ translate('Your Name') }}" name="name" value="{{ Auth::user()->name }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Your Phone')}}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control" placeholder="{{ translate('Your Phone')}}" name="phone" value="{{ Auth::user()->phone }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Photo') }}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="file" name="photo" id="file-3" class="custom-input-file custom-input-file--4" data-multiple-caption="{count} files selected" accept="image/*" />
                                            <label for="file-3" class="mw-100">
                                                <span></span>
                                                <strong>
                                                    <i class="fa fa-upload"></i>
                                                    {{ translate('Choose image') }}
                                                </strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Your Password') }}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="password" class="form-control" placeholder="{{ translate('New Password') }}" name="new_password">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Confirm Password') }}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="password" class="form-control" placeholder="{{ translate('Confirm Password') }}" name="confirm_password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-styled btn-base-1">{{ translate('Update Profile') }}</button>
                            </div>

                            <div class="card bg-white mt-4">
                                <div class="card-header mb-0 h6 px-3 py-2">
                                    {{ translate('Addresses') }}
                                </div>
                                <div class="card-body p-3">
                                    <div class="row gutters-10">
                                        @foreach (Auth::user()->addresses as $key => $address)
                                            <div class="col-lg-6">
                                                <div class="border p-3 pr-5 rounded mb-3 position-relative">
                                                    <div>
                                                        <span class="alpha-6">{{ translate('Address') }}:</span>
                                                        <span class="strong-600 ml-2">{{ $address->address }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="alpha-6">{{ translate('Postal Code') }}:</span>
                                                        <span class="strong-600 ml-2">{{ $address->postal_code }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="alpha-6">{{ translate('City') }}:</span>
                                                        <span class="strong-600 ml-2">{{ getNameCity($address->city) }}</span>
                                                    </div>
{{--                                                    <div>--}}
{{--                                                        <span class="alpha-6">{{ translate('Country') }}:</span>--}}
{{--                                                        <span class="strong-600 ml-2">{{ $address->country }}</span>--}}
{{--                                                    </div>--}}
                                                    <div>
                                                        <span class="alpha-6">{{ translate('Phone') }}:</span>
                                                        <span class="strong-600 ml-2">{{ $address->phone }}</span>
                                                    </div>
                                                    @if ($address->set_default)
                                                        <div class="position-absolute right-0 bottom-0 pr-2 pb-3">
                                                            <span class="badge badge-primary bg-base-1">{{ translate('Default') }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="dropdown position-absolute right-0 top-0">
                                                        <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                                            <i class="la la-ellipsis-v"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                            @if (!$address->set_default)
                                                                <a class="dropdown-item" href="{{ route('addresses.set_default', $address->id) }}">{{ translate('Make This Default') }}</a>
                                                            @endif
                                                            {{-- <a class="dropdown-item" href="">Edit</a> --}}
                                                            <a class="dropdown-item" href="{{ route('addresses.destroy', $address->id) }}">{{ translate('Delete') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-lg-6 mx-auto" onclick="add_new_address()">
                                            <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                                                <i class="la la-plus la-2x"></i>
                                                <div class="alpha-7">{{ translate('Add New Address') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>

                        <form action="{{ route('user.change.email') }}" method="POST">
                            @csrf
                            <div class="card bg-white mt-4">
                                <div class="card-header mb-0 h6 px-3 py-2">
                                    {{ translate('Change your email') }}
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Your Email') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="input-group mb-3">
                                              <input
                                                  type="email"
                                                  class="form-control"
                                                  placeholder="{{ translate('Your Email')}}"
                                                  name="email"
                                                  value=
                                                  "{{ Auth::user()->email }}"
                                              />
                                              <div class="input-group-append">
                                                 <button type="button" class="btn btn-outline-secondary new-email-verification">
                                                     <span class="d-none loading">
                                                         <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                         Sending Email...
                                                     </span>
                                                     <span class="default">{{__('Verify')}}</span>
                                                 </button>
                                              </div>
                                            </div>
                                            <button class="btn btn-styled btn-base-1" type="submit">{{__('Update Email')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="new-address-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('City') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <select class="form-control mb-3 selectpicker" data-placeholder="{{translate('Select your city')}}" name="city" required>
                                            @foreach (\App\City::where('status', 1)->get() as $key => $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Address') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <textarea class="form-control textarea-autogrow mb-3" placeholder="{{ translate('Your Address') }}" rows="1" name="address" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Postal code')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('Your Postal Code')}}" name="postal_code" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Phone')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="tel" id="phone-code" pattern="[0-9]{9}" required class="pl-100 w-100 form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Mobile Number') }}" name="phone">
                                    <input type="hidden" name="country_code" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-base-1">{{  translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function (){
        validateFile($('#customer_profile_update').find("input[name='photo']") ,['png','jpeg', 'jpg', 'svg']);
    })

    $('#customer_profile_update').submit(function (e){
        e.preventDefault();
        var new_password = $(this).find("input[name='new_password']").val();
        var confirm_password = $(this).find("input[name='confirm_password']").val();
        if(new_password != '' || confirm_password != '') {
            if(new_password !== confirm_password) {
                showFrontendAlert('danger', '{{translate('Sorry! Password does not match.')}}')
                return ;
            }
        }
        var formData = new FormData(this);
        $.ajax({
            url: '{{ route('customer.profile.update') }}',
            type: "POST",
            dataType: "JSON",
            cache:false,
            contentType: false,
            processData: false,
            data: formData,
            success: function (data){
                if(data.status){
                    showFrontendAlert('success', data.data);
                    setTimeout(function (){
                        window.location.reload();
                    }, 2000)
                }else {
                    showFrontendAlert('danger', data.data);
                }
            },
            error: function (err) {
                showFrontendAlert('danger', '{{translate('Sorry! Something went wrong.')}}');
            }
        });
    });

    function add_new_address(){
        $('#new-address-modal').modal('show');
    }

    $('.new-email-verification').on('click', function() {
        $(this).find('.loading').removeClass('d-none');
        $(this).find('.default').addClass('d-none');
        var email = $("input[name=email]").val();

        $.post('{{ route('user.new.verify') }}', {_token:'{{ csrf_token() }}', email: email}, function(data){
            data = JSON.parse(data);
            $('.default').removeClass('d-none');
            $('.loading').addClass('d-none');
            if(data.status == 2)
                showFrontendAlert('warning', data.message);
            else if(data.status == 1)
                showFrontendAlert('success', data.message);
            else
                showFrontendAlert('danger', data.message);
        });
    });
</script>
<script type="text/javascript">
    var input = document.querySelector("#phone-code");
    var iti = intlTelInput(input, {
        separateDialCode: true,
    });

    var country = iti.getSelectedCountryData();
    console.log(country);
    $('input[name=country_code]').val(country.dialCode);

    input.addEventListener("countrychange", function() {
        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);
    });
</script>
@endsection
