<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OpenDKExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                'nama_kecamatan' => $item->nama_kecamatan,
                'nama_kabupaten' => $item->nama_kabupaten,
                'nama_provinsi' => $item->nama_provinsi,
                'url' => $item->url,
                'versi' => $item->versi,
                'format_updated_at' => $item->format_updated_at,
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
            'Web',
            'Versi',
            'Akses Terakhir',
        ];
    }
}