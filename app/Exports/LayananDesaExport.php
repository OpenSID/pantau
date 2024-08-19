<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LayananDesaExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                'tgl_terpantau' => $item->tgl_akses,
                'nama_desa' => $item->desa->nama_desa,
                'nama_kecamatan' => $item->desa->nama_kecamatan,
                'nama_kabupaten' => $item->desa->nama_kabupaten,
                'nama_provinsi' => $item->desa->nama_provinsi,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Id',
            'Tgl Terpantau',
            'Desa',
            'Kecamatan',
            'Kabupaten',
            'Provinsi'
        ];
    }
}