<?php

namespace App\Imports;

use App\Models\PekerjaanPmi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class PekerjaanPmiImport implements ToModel, WithBatchInserts, WithUpserts, WithHeadingRow
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new PekerjaanPmi([
            'nama' => $row['nama_pekerjaan'] ?? $row['nama'],
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function uniqueBy()
    {
        return 'nama';
    }
}
