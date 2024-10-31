<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WilayahKabupatenExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                'kode_kabupaten' => $item->kode_kabupaten,
                'nama_provinsi' => $item->nama_provinsi,
                'nama_kabupaten' => $item->nama_kabupaten,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE WILAYAH',
            'NAMA PROVINSI',
            'NAMA KABUPATEN',
        ];
    }
}
