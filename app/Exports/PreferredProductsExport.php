<?php

namespace App\Exports;

use App\product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PreferredProductsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return filter_products(Product::where('published', 1)->orderBy('num_of_sale', 'desc'))->limit(20)->get(['name', 'num_of_sale']);
    }

    public function map($row): array
    {
        return [
            $row->getTranslation('name', 'en'),
            $row->num_of_sale
        ];
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Num Of Sale',
        ];
    }
}
