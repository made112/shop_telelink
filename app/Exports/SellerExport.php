<?php

namespace App\Exports;

use App\Seller;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SellerExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    public function query()
    {
        return Seller::query();
    }

    public function map($seller): array
    {
        if ($seller->user != null) {
            $status = '';
            if ($seller->verification_status == 1) {
                $status = 'Verified';
            } elseif ($seller->verification_info != null) {
                $status = 'Requested';
            }else {
                $status = 'Not Verified';
            }
            return [
                $seller->user->name,
                $seller->user->email,
                $seller->user->shop->name,
                $status,
            ];
        }
        return [];

    }

    public function headings(): array
    {
        return [
            'Seller Name',
            'Email',
            'Shop Name',
            'Status'
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
