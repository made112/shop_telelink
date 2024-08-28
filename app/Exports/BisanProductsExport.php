<?php

namespace App\Exports;

use App\OrderDetail;
use App\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BisanProductsExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    public function query()
    {
        return OrderDetail::query();
//        return Product::query();
    }

//    public function map($product): array
//    {
//        if ($product) {
//            $qty = 0;
//            $cus_info = '';
//            if ($product->added_by === 'seller') {
//                foreach ($product->orderDetails as $key => $orderDetail) {
//                    if ($orderDetail){
//                    $qty += intval($orderDetail->quantity);
//                    $cus_info .= 'Name Customer: ' . $orderDetail->order->user['name']. PHP_EOL;
//                    $cus_info .= 'Address: ' . json_decode($orderDetail->order->shipping_address)->address. PHP_EOL;
//                    $cus_info .= 'Quantity: ' . $orderDetail->quantity . PHP_EOL .PHP_EOL;
//                    }
//                }
//                return [
//                    $product->id,
//                    $product->name,
//                    $cus_info,
//                    strval($qty)
//                ];
//            }
//            return [];
//        }
//    }

    public function map($orderDetail): array
    {
        if ($orderDetail) {
//            $qty = 0;
//            $cus_info = '';
            if ($orderDetail->product['added_by'] === 'seller') {
//                $qty += intval($orderDetail->quantity);
//                $cus_info .= 'Name Customer: ' . $orderDetail->order->user['name']. PHP_EOL;
//                $cus_info .= 'Address: ' . json_decode($orderDetail->order->shipping_address)->address. PHP_EOL;
//                $cus_info .= 'Quantity: ' . $orderDetail->quantity . PHP_EOL .PHP_EOL;
                return [
                    $orderDetail->product->id,
                    $orderDetail->product->name,
                    $orderDetail->order->user['name'],
                    json_decode($orderDetail->order->shipping_address)->address,
                    $orderDetail->quantity
                ];
            }
            return [];
        }
    }
    public function headings(): array
    {
        return [
            'Code',
            'Product Name',
            'Customer Name',
            'Customer Address',
            'Quantity'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(20);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                $event->sheet->getColumnDimension('C')->setAutoSize(true);
            },
        ];
    }
}
