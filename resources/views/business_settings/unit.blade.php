@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="row">
        <div class="col-sm-12">
            <a onclick="unit_modal()" class="btn btn-rounded btn-info pull-right">{{translate('Add New Unit')}}</a>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{translate('All Units')}}</h3>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered demo-dt-basic" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{translate('Unit name')}}</th>
                        <th>{{translate('Unit symbol')}}</th>
                        <th>{{translate('Unit code')}}</th>
                        <th>{{translate('Status')}}</th>
                        <th width="10%">{{translate('Options')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($units as $key => $unit)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$unit->name}}</td>
                            <td>{{$unit->symbol}}</td>
                            <td>{{$unit->code}}</td>
                            <td>
                                <label class="switch">
                                    <input onchange="update_unit_status(this)" value="{{ $unit->id }}" type="checkbox" <?php if($unit->status == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                        {{translate('Actions')}} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a onclick="edit_unit_modal('{{$unit->id}}');">{{translate('Edit')}}</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade" id="add_unit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">

            </div>
        </div>
    </div>

    <div class="modal fade" id="unit_modal_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">

            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        //Updates default currencies
        {{-- function updateCurrency(i){--}}
        {{--     var exchange_rate = $('#exchange_rate_'+i).val();--}}
        {{--     if($('#status_'+i).is(':checked')){--}}
        {{--         var status = 1;--}}
        {{--     }--}}
        {{--     else{--}}
        {{--         var status = 0;--}}
        {{--     }--}}
        {{--     $.post('{{ route('unit.update') }}', {_token:'{{ csrf_token() }}', id:i, exchange_rate:exchange_rate, status:status}, function(data){--}}
        {{--        location.reload();--}}
        {{--     });--}}
        {{-- }--}}
         //Updates your unit
         function updateYourUnit(i){
             var name = $('#name_'+i).val();
             var symbol = $('#symbol_'+i).val();
             var code = $('#code_'+i).val();
             if($('#status_'+i).is(':checked')){
                 var status = 1;
             }
             else{
                 var status = 0;
             }
             $.post('{{ route('your_unit.update') }}', {_token:'{{ csrf_token() }}', id:i, name:name, symbol:symbol, code:code, status:status}, function(data){
                 location.reload();
             });
         }

        function unit_modal(){
            $.get('{{ route('unit.create') }}',function(data){
                $('#modal-content').html(data);
                $('#add_unit_modal').modal('show');
            });
        }

        function update_unit_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('unit.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                console.log(data);
                if(data == 1){
                    showAlert('success', 'Unit Status updated successfully');
                }
                else{
                    showAlert('danger', 'Something went wrong');
                }
            });
        }

        function edit_unit_modal(id){
            $.post('{{ route('unit.edit') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#unit_modal_edit .modal-content').html(data);
                $('#unit_modal_edit').modal('show', {backdrop: 'static'});
            });
        }
    </script>
@endsection
@endif
