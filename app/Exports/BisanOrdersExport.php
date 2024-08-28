<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use App\OrderDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BisanOrdersExport extends Controller implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    public function query()
    {
        return orderDetail::query()->with('seller');
    }

    public function map($orderDetail): array
    {
        if ($orderDetail->seller){
            if($orderDetail and $orderDetail->product){
                if($orderDetail->product['added_by'] === 'seller' ) {
                    return [
                        $orderDetail->seller['name'],
                        $orderDetail->order->user['name'],
                        json_decode($orderDetail->order->shipping_address)->address,
                        $orderDetail->order->user['phone'],
                        __($orderDetail->product['name']),
                        $orderDetail->quantity
                    ];
                }
            }
        }
        return [];
    }

    public function headings(): array
    {
        return [
            'Seller Name',
            'Customer Name',
            'Customer Address',
            'Customer Phone',
            'Product Name',
            'Quantity',

        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(20);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }
}
