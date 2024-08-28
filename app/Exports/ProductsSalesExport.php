<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsSalesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::orderBy('code', 'desc')->select(['id', 'code', 'payment_status', 'user_id', 'grand_total'])->get();
    }

    public function map($row): array
    {
        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
        $username = null;

        if ($row->user != null) $username = $row->user->name;
        else $username = "Guest ( $row->guest_id)";

        $status = 'Delivered';
        foreach ($row->orderDetails as $key => $saleDetail) {
            if($saleDetail->delivery_status != 'delivered'){
                $status = 'Pending';
            }
        }
        $payment_status = 'Paid';
        if ($row->payment_status !== 'paid') {
            $payment_status = 'Unpaid';
        }

        $refund = 'Refund';
        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if (count($row->refund_requests) > 0) {
                $refund = count($row->refund_requests). ' Refund';
            } else {
                $refund = 'No Refund';
            }
        }

        return [
            $row->code,
            strval(count($row->orderDetails)),
            $username,
            single_price($row->grand_total),
            $status,
            $payment_status,
            $refund
        ];
    }

    public function headings(): array
    {
        return [
            'Order Code',
            'Num Of Products',
            'Customer',
            'Ammount',
            'Delivery Status',
            'Payment Status',
            'Refund'
        ];
    }
}
