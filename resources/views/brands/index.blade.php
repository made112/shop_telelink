@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('brands.create')}}" class="btn btn-rounded btn-info pull-right">{{translate('Add New Brand')}}</a>
    </div>
</div>

<br>

<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading bord-btm clearfix pad-all h-100">
        <h3 class="panel-title pull-left pad-no">{{translate('Brands')}}</h3>
        <div class="pull-right clearfix">
            <form class="" id="sort_brands" action="" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Logo')}}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($brands as $key => $brand)
                    <tr>
                        <td>{{ ($key+1) + ($brands->currentPage() - 1)*$brands->perPage() }}</td>
                        <td>{{$brand->name}}</td>
                        <td><img loading="lazy"  class="img-md" src="{{ my_asset($brand->logo) }}" alt="Logo"></td>
                        <td>
{{--                            <div class="btn-group dropdown">--}}
{{--                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">--}}
{{--                                    {{translate('Actions')}} <i class="dropdown-caret"></i>--}}
{{--                                </button>--}}
{{--                                <ul class="dropdown-menu dropdown-menu-right">--}}
{{--                                    <li><a href="{{route('brands.edit', encrypt($brand->id))}}">{{translate('Edit')}}</a></li>--}}
{{--                                    <li><a onclick="confirm_modal('{{route('brands.destroy', $brand->id)}}');">{{translate('Delete')}}</a></li>--}}
{{--                                </ul>--}}
{{--                            </div>--}}

                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('brands.edit', encrypt($brand->id))}}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" onclick="confirm_modal('{{route('brands.destroy', $brand->id)}}');" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('brands.destroy', $brand->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $brands->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
    <script type="text/javascript">
        function sort_brands(el){
            $('#sort_brands').submit();
        }
    </script>
@endsection
@endif
