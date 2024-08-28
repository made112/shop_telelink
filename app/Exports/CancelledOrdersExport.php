<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CancelledOrdersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::whereHas('orderDetails', function ($q) {
            $q->where('delivery_status', 'cancelled');
        })->get();
    }

    public function map($row): array
    {
        $username = null;
        $payment = 'Paid';
        $status = $row->orderDetails->first()->delivery_status;
        if ($row->user_id != null){
            $username = $row->user->name;
        } else {
            $username = "Guest $row->guest_id";
        }

        if ($row->orderDetails->first()->payment_status != 'paid') {
            $payment = 'Unpaid';
        }

        return [
            $row->code,
            strval(count($row->orderDetails)),
            $username,
            single_price($row->orderDetails->sum('price') + $row->orderDetails->sum('tax')),
            ucfirst(str_replace('_', ' ', $status)),
            ucfirst(str_replace('_', ' ', $row->payment_type)),
            $payment
        ];
    }

    public function headings(): array
    {
        return [
            'Order Code',
            'Num. of Products',
            'Customer',
            'Ammount',
            'Delivery Status',
            'Payment Method',
            'Payment Status'
        ];
    }
}
