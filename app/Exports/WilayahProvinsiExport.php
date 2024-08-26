<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WilayahProvinsiExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function($item, $index) {
            return [
                'no' => $index + 1,  // Menambahkan nomor urut berdasarkan index
                'kode_provinsi' => $item->kode_provinsi,
                'nama_provinsi' => $item->nama_provinsi,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE WILAYAH',
            'NAMA PROVINSI',
        ];
    }
}
