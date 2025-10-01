<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KecamatanExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                'nama_kecamatan' => $item->nama_kecamatan,
                'nama_kabupaten' => $item->nama_kabupaten,
                'nama_provinsi' => $item->nama_provinsi,
                'total_desa' => $item->total_desa,
                'offline' => $item->offline ?: 0,
                'online' => $item->online ?: 0
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Kecamatan',
            'Kabupaten',
            'Provinsi',
            'Total Desa',
            'Server Offline',
            'Server Online',
        ];
    }

    // format angka untuk kolom total_desa, offline, online
    public function columnFormats(): array
    {
        return [
            'D' => '#,##0',
            'E' => '#,##0',
            'F' => '#,##0',
        ];
    }
}
