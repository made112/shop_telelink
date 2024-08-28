<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    public function query()
    {
        return Product::query();
    }

    public function map($product): array
    {
        $qty = 0;
        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                $qty += $stock->qty;
            }
        }
        else {
            $qty = $product->current_stock;
        }
        return [
            $product->id,
            $product->name,
            strval($qty)
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Stock',
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
