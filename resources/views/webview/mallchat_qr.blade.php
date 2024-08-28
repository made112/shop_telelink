@extends('layouts.webview')

@section('content')
    @php
        try {

            $qr_code = \App\ManualPaymentMethod::where('type','=', 'qr_code')->where('heading','=', 'maalchat')->first();
         } catch (\Exception $e) {
            dd($e);
         }
    @endphp
    <div id="page-content">
        <div class="d-flex justify-content-center align-items-center flex-column">
            {!! $qr_code->description !!}
            <div class="mt-5">
                <input type="hidden" name="order_id" value="{{$order_id}}">
                <a href="{{route('maalchat.cancel')}}" class="btn btn-danger" name="cancel">Cancel</a>
                <a href="{{route('maalchat.done', $order_id)}}" class="btn btn-primary" name="done">Confirm</a>
            </div>
        </div>
    </div>
@endsection
<style>
    #payment_modal {
        z-index: 2000 !important;
    }
</style>
<div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title strong-600 heading-5">{{ translate('Make Payment')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="payment_modal_body"></div>
        </div>
    </div>
</div>
@section('script')
    <script type="text/javascript">
        function show_make_payment_modal(order_id) {
            $.post('{{ route('checkout.make_payment') }}', {
                _token: '{{ csrf_token() }}',
                order_id: order_id
            }, function (data) {
                $('#payment_modal_body').html(data);
                $('#payment_modal').modal('show');
                $('input[name=order_id]').val(order_id);
            });
        }
    </script>
@endsection

