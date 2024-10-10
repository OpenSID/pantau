<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OpenDKExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                'url' => $item->url,
                'versi' => $item->versi,
                'format_updated_at' => $item->format_updated_at,
                'Batas Wilayah Utara' => ($item->batas_wilayah == null) ? '' : $item->batas_wilayah['bts_wil_utara'] ?? '',
                'Batas Wilayah Barat' => ($item->batas_wilayah == null) ? '' : $item->batas_wilayah['bts_wil_barat'] ?? '',
                'Batas Wilayah Timur' => ($item->batas_wilayah == null) ? '' : $item->batas_wilayah['bts_wil_timur'] ?? '',
                'Batas Wilayah Selatan' => ($item->batas_wilayah == null) ? '' : $item->batas_wilayah['bts_wil_selatan'] ?? '',
                'Jumlah Desa' => $item->jml_desa,
                'Jumlah Desa Tersinkronisasi' => $item->jumlahdesa_sinkronisasi,
                'Jumlah Penduduk' => $item->jumlah_penduduk,
                'Jumlah KK' => $item->jumlah_keluarga,
                'Jumlah Program Bantuan' => $item->jumlah_bantuan,
                'Alamat Kantor' => $item->alamat,
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
            'Batas Wilayah Utara',
            'Batas Wilayah Barat',
            'Batas Wilayah Timur',
            'Batas Wilayah Selatan',
            'Jumlah Desa',
            'Jumlah Desa Tersinkronisasi',
            'Jumlah Penduduk',
            'Jumlah KK',
            'Jumlah Program Bantuan',
            'Alamat Kantor',
        ];
    }
}
