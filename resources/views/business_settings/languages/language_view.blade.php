@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

    <div class="row">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $language->name }}</h3>
            </div>
            <form class="form-horizontal" action="{{ route('languages.key_value_store') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $language->id }}">
                <div class="panel-body">
                    <table class="table table-striped table-bordered" id="tranlation-table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{translate('Key')}}</th>
                                <th>{{translate('Value')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach (openJSONFile('en') as $key => $value)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td class="key">{{ $key }}</td>
                                    <td>
                                        <input type="text" class="form-control value" style="width:100%" name="key[{{ $key }}]" @isset(openJSONFile($language->code)[$key])
                                            value="{{ openJSONFile($language->code)[$key] }}"
                                        @endisset>
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer text-right">
                    <button type="button" class="btn btn-purple" onclick="copyTranslation()">{{ translate('Copy Translations') }}</button>
    				<button type="submit" class="btn btn-purple">{{ translate('Save') }}</button>
    			</div>
            </form>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        //translate in one click
        function copyTranslation() {
            $('#tranlation-table > tbody  > tr').each(function (index, tr) {
                $(tr).find('.value').val($(tr).find('.key').text());
            });
        }
    </script>
@endsection
@endif
