@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ translate('Shipping For Cities') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('cities.shipping_type') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <div class="col-lg-3">
                                <label class="control-label">{{ translate('Jerusalem') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="jerusalem" value="{{ \App\BusinessSetting::where('type', 'jerusalem')->first()->value }}" placeholder="{{ translate('Jerusalem') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-3">
                                <label class="control-label">{{ translate('West Bank') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="west_bank" value="{{ \App\BusinessSetting::where('type', 'west_bank')->first()->value }}" placeholder="{{ translate('West Bank') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-3">
                                <label class="control-label">{{ translate('Occupied Interior') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="occupied_interior" value="{{ \App\BusinessSetting::where('type', 'occupied_interior')->first()->value }}" placeholder="{{ translate('Occupied Interior') }}" required>
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
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ translate('Free Shipping For Cities') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('cities.free_shipping') }}" method="POST">
                        @csrf
                        @php
                        $free_shipping = \App\BusinessSetting::where('type', 'free_shipping')->first()->value;
                        @endphp
                        <div class="form-group">
                            <div class="col-lg-3">
                                <label class="control-label">{{ translate('Free Shipping Cost') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="free_shipping" value="{{ $free_shipping }}" placeholder="{{ translate('Free Shipping') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{ translate('Save') }}</button>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <div class="col-12">
                                <span class="text-danger"><b>{{ translate('Note: The free shipping cost for this section to the cities of the West Bank and Jerusalem if the customer purchases more than '). format_price($free_shipping) }} .</b></span>
                            </div>
                        </div>
                    </form>
                </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <a onclick="city_modal()" class="btn btn-rounded btn-info pull-right">{{translate('Add New City')}}</a>
        </div>
    </div>
    <br>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Cities')}}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th>{{translate('Name')}}</th>
                        <th>{{translate('Name Arabic')}}</th>
                        <th>{{translate('Show/Hide')}}</th>
                        <th>{{translate('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cities as $key => $city)
                        <tr>
                            <td>{{ ($key+1) + ($cities->currentPage() - 1)*$cities->perPage() }}</td>
                            <td>{{ $city->name }}</td>
                            <td>{{ $city->nameAr }}</td>
                            <td><label class="switch">
                                    <input onchange="update_status(this)" value="{{ $city->id }}" type="checkbox" <?php if($city->status == 1) echo "checked";?> >
                                    <span class="slider round"></span></label></td>
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                        {{translate('Actions')}} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a onclick="edit_city_modal('{{$city->id}}');">{{translate('Edit')}}</a></li>
                                        <li><a onclick="confirm_modal('{{route('cities.destroy', $city->id)}}');">{{translate('Delete')}}</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $cities->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_city_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">

            </div>
        </div>
    </div>

    <div class="modal fade" id="city_modal_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        function city_modal(){
            $.get('{{ route('cities.create') }}',function(data){
                $('#modal-content').html(data);
                $('#add_city_modal').modal('show');
            });
        }

        function update_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('cities.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    showAlert('success', 'City status updated successfully');
                }
                else{
                    showAlert('danger', 'Something went wrong');
                }
            });
        }

        function edit_city_modal(id){
            $.post('{{ route('cities.editCity') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#city_modal_edit .modal-content').html(data);
                $('#city_modal_edit').modal('show', {backdrop: 'static'});
            });
        }
    </script>
@endsection
@endif
