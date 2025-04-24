<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SukuExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item, $index) {
            return [
                'no' => $index + 1,  // Menambahkan nomor urut berdasarkan index
                'nama_suku' => $item->name,
                'kode_wilayah' => $item->region->region_code,
                'nama_provinsi' => $item->region->region_name,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA SUKU',
            'KODE WILAYAH',
            'NAMA PROVINSI',
        ];
    }
}
