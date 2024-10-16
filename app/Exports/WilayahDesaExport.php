<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WilayahDesaExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                'kode_desa' => $item->kode_desa,
                'nama_provinsi' => $item->nama_provinsi,
                'nama_kabupaten' => $item->nama_kabupaten,
                'nama_kecamatan' => $item->nama_kecamatan,
                'nama_desa' => $item->nama_desa,
                'nama_desa_baru' => $item->nama_desa_baru,
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
            'NAMA DESA',
            'NAMA DESA UBAHAN',
        ];
    }
}
