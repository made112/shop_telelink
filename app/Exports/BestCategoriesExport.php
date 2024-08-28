<?php

namespace App\Exports;

use App\category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BestCategoriesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $categories = Category::where('featured', 1)->get();
        $categories = collect($categories)->map(function ($category) {
            return [
                'name' => $category->name,
                'num_of_sale' => $category->products->sum('num_of_sale')
            ];
        })->sortByDesc('num_of_sale');
        return $categories;
    }
    public function map($row): array
    {
        return [
            $row['name'],
        strval($row['num_of_sale'])
        ];
    }

    public function headings(): array
    {
        return [
            'Categorty Name',
            'Num Of Sale',
        ];
    }
}
