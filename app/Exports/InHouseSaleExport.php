<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InHouseSaleExport implements  FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    public function query()
    {
        return Product::query();
    }

    public function map($product): array
    {
        return [
            $product->name,
            strval($product->num_of_sale)
        ];
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Num Of Sale'
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
