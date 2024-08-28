@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('18', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Conversations')}}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered demo-dt-basic" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Date') }}</th>
                    <th>{{translate('Title')}}</th>
                    <th>{{translate('Product')}}</th>
                    <th>{{translate('Sender')}}</th>
                    <th>{{translate('Receiver')}}</th>
                    <th width="15%">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($conversations as $key => $conversation)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{ $conversation->created_at }}</td>
                        <td>{{ $conversation->title }}</td>
                        <td>
                            <a href="{{ $conversation->product && isset($conversation->product->slug) ? route('product', $conversation->product->slug) : '#' }}"
                               target="_blank">{{ isset($conversation->product->name) ? $conversation->product->name : translate('Not Found') }}</a>
                        </td>
                        <td>
                            @if ($conversation->sender != null)
                                {{ $conversation->sender->name }}
                                @if ($conversation->receiver_viewed == 0)
                                    <span class="pull-right badge badge-info">{{ translate('New') }}</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($conversation->receiver != null)
                                {{ $conversation->receiver->name }}
                                @if ($conversation->sender_viewed == 0)
                                    <span class="pull-right badge badge-info">{{ translate('New') }}</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            {{--                            <div class="btn-group dropdown">--}}
                            {{--                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">--}}
                            {{--                                    {{translate('Actions')}} <i class="dropdown-caret"></i>--}}
                            {{--                                </button>--}}
                            {{--                                <ul class="dropdown-menu dropdown-menu-right">--}}
                            {{--                                    <li><a href="{{route('conversations.admin_show', encrypt($conversation->id))}}">{{translate('View')}}</a></li>--}}
                            {{--                                    <li><a onclick="confirm_modal('{{route('conversations.destroy', encrypt($conversation->id))}}');">{{translate('Delete')}}</a></li>--}}
                            {{--                                </ul>--}}
                            {{--                            </div>--}}
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                               href="{{route('conversations.admin_show', encrypt($conversation->id))}}"
                               title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a onclick="confirm_modal('{{route('conversations.destroy', encrypt($conversation->id))}}');"
                               class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                               data-href="{{route('conversations.destroy', encrypt($conversation->id))}}"
                               title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>

@endsection
@endif
