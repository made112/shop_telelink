<?php

namespace App\Exports;

use App\Seller;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SellerSaleExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    public function query()
    {
        return Seller::query();
    }

    public function map($seller): array
    {
        if ($seller->user != null) {
            $num_of_sale = 0;
            foreach ($seller->user->products as $key => $product) {
                $num_of_sale += $product->num_of_sale;
            }
            return [
                $seller->user->name,
                $seller->user->shop->name,
                strval($num_of_sale),
                single_price(\App\OrderDetail::where('seller_id', $seller->user->id)->sum('price'))
            ];
        }
        return [];

    }

    public function headings(): array
    {
        return [
            'Seller Name',
            'Shop Name',
            'Number of Product Sale',
            'Order Amount'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }

}
