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
                'kontak' => ($item->kontak['nama'] ?? '') . ' ' . ($item->kontak['hp'] ?? ''),
                'url_hosting' => $item->url_hosting,
                'versi_lokal' => $item->versi_lokal,
                'versi_hosting' => $item->versi_hosting,
                'modul_tte' => ($item->modul_tte == 1) ? 'Aktif' : 'Tidak Aktif',
                'jml_surat_tte' => $item->jml_surat_tte,
                'jml_penduduk' => $item->jml_penduduk,
                'jml_artikel' => $item->jml_artikel,
                'jml_surat_keluar' => $item->jml_surat_keluar,
                'jml_bantuan' => $item->jml_bantuan,
                'jml_mandiri' => $item->jml_mandiri,
                'jml_pengguna' => $item->jml_pengguna,
                'jml_unsur_peta' => $item->jml_unsur_peta,
                'jml_persil' => $item->jml_persil,
                'jml_dokumen' => $item->jml_dokumen,
                'jml_keluarga' => $item->jml_keluarga,
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
            'Kontak',
            'Web',
            'Versi Offline',
            'Versi Online',
            'Modul TTE',
            'Surat ter-TTE',
            'Penduduk',
            'Artikel',
            'Surat Keluar',
            'Program Bantuan',
            'Pengguna Mandiri',
            'Pengguna',
            'Unsur Peta',
            'Persil',
            'Dokumen',
            'Keluarga',
            'Akses Terakhir',
        ];
    }
}
