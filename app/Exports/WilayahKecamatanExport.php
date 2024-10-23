<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WilayahKecamatanExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                'kode_kecamatan' => $item->kode_kecamatan,
                'nama_provinsi' => $item->nama_provinsi,
                'nama_kabupaten' => $item->nama_kabupaten,
                'nama_kecamatan' => $item->nama_kecamatan,
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
            'NAMA KECAMATAN',
        ];
    }
}
