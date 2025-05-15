<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SukuExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item, $index) {
            $jumlahMarga = $item->marga_count ?? 0;
            return [
                'no' => $index + 1,  // Menambahkan nomor urut berdasarkan index
                'nama_suku' => $item->name,
                'kode_wilayah' => $item->region->region_code,
                'nama_provinsi' => $item->region->region_name,
                'marga_count' => $jumlahMarga > 0 ? $jumlahMarga : '0',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA SUKU',
            'KODE WILAYAH',
            'NAMA PROVINSI',
            'JUMLAH MARGA',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER, // Format kolom E sebagai angka
        ];
    }
}
