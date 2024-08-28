@extends('layouts.app')
@if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions)))
@section('content')

<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('sponsored_products.create')}}" class="btn btn-rounded btn-info pull-right">{{translate('Add New Sponsored Products')}}</a>
    </div>
</div>
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.4&appId=241110544128";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<br>

<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading bord-btm clearfix pad-all h-100">
        <h3 class="panel-title pull-left pad-no">{{translate('Sponsored Products')}}</h3>
        <div class="pull-right clearfix">
            <form class="" id="sort_sponsored_products" action="" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel-body">
        <table class="table res-table table-responsive mar-no" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Title')}}</th>
                    <th>{{translate('Sub Title')}}</th>
                    <th>{{ translate('Banner') }}</th>
                    <th>{{ translate('Start Date') }}</th>
                    <th>{{ translate('End Date') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th>{{ translate('Featured') }}</th>
                    <th>{{ translate('Share Facebook') }}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sponsored_products as $key => $sponsored_product)
                    <tr>
                        <td>{{ ($key+1) + ($sponsored_products->currentPage() - 1)*$sponsored_products->perPage() }}</td>
                        <td>{{$sponsored_product->title}}</td>
                        <td>{{$sponsored_product->sub_title}}</td>
                        <td><img class="img-md" src="{{ my_asset($sponsored_product->banner) }}" alt="banner"></td>
                        <td>{{ date('d-m-Y', $sponsored_product->start_date) }}</td>
                        <td>{{ date('d-m-Y', $sponsored_product->end_date) }}</td>
                        <td><label class="switch">
                            <input onchange="update_flash_deal_status(this)" value="{{ $sponsored_product->id }}" type="checkbox" <?php if($sponsored_product->status == 1) echo "checked";?> >
                            <span class="slider round"></span></label></td>
                        <td><label class="switch">
                            <input onchange="update_flash_deal_feature(this)" value="{{ $sponsored_product->id }}" type="checkbox" <?php if($sponsored_product->featured == 1) echo "checked";?> >
                            <span class="slider round"></span></label></td>
                        <td>
                            <div class="fb-share-button" data-href="{{ route('product', $sponsored_product->product->slug) }}" data-layout="button_count"></div>
                        </td>
                        <td>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('sponsored_products.edit', encrypt($sponsored_product->id))}}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" onclick="confirm_modal('{{route('sponsored_products.destroy', $sponsored_product->id)}}');" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('sponsored_products.destroy', $sponsored_product->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $sponsored_products->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
    <script type="text/javascript">
        function update_flash_deal_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('sponsored_products.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    location.reload();
                }
                else{
                    showAlert('danger', 'Something went wrong');
                }
            });
        }
        function update_flash_deal_feature(el){
            if(el.checked){
                var featured = 1;
            }
            else{
                var featured = 0;
            }
            $.post('{{ route('sponsored_products.update_featured') }}', {_token:'{{ csrf_token() }}', id:el.value, featured:featured}, function(data){
                if(data == 1){
                    location.reload();
                }
                else{
                    showAlert('danger', 'Something went wrong');
                }
            });
        }
    </script>
@endsection
@endif
