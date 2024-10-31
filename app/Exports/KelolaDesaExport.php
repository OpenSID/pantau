<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KelolaDesaExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                'id_device' => $item->id_device,
                'tgl_akses' => $item->tgl_akses,
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
            'Provinsi',
        ];
    }
}
