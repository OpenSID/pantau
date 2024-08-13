<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DesaExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                'nama_desa' => $item->nama_desa,
                'nama_kecamatan' => $item->nama_kecamatan,
                'nama_kabupaten' => $item->nama_kabupaten,
                'nama_provinsi' => $item->nama_provinsi,
                'url_hosting' => $item->url_hosting,
                'versi_lokal' => $item->versi_lokal,
                'versi_hosting' => $item->versi_hosting,
                'modul_tte' => ($item->modul_tte == 1) ? 'Aktif' : 'Tidak Aktif',
                'jml_surat_tte' => $item->jml_surat_tte,
                'tgl_akses' => $item->tgl_akses,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Desa',
            'Kecamatan',
            'Kabupaten',
            'Provinsi',
            'Web',
            'Versi Offline',
            'Versi Online',
            'Modul TTE',
            'Surat ter-TTE',
            'Akses Terakhir',
        ];
    }
}
