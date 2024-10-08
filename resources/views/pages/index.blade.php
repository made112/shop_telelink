@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions)))
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{ route('pages.create')}}" class="btn btn-rounded btn-info pull-right">{{translate('Add New Page')}}</a>
        </div>
    </div>

    <br>

    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Custom Pages')}}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered demo-dt-basic" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th>{{translate('Title')}}</th>
                        <th>{{translate('Slug')}}</th>
                        <th width="10%">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $key => $page)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$page->title}}</td>
                            <td>{{ route('custom-pages.show_custom_page', $page->slug) }}</td>
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                        {{translate('Actions')}} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="{{route('pages.edit', $page->slug)}}">{{translate('Edit')}}</a></li>
                                        <li><a onclick="confirm_modal('{{route('pages.destroy', $page->id)}}');">{{translate('Delete')}}</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection
@endif
