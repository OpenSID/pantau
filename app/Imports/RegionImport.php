<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Region;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class RegionImport implements ToModel, WithBatchInserts, WithUpserts, WithHeadingRow
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
    }

    public function batchSize(): int
    {
        return 500;
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'region_code';
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Region([
            'region_code' => $row['kode'],
            'region_name' => $row['nama'],
            'parent_code' => parent_code($row['kode']),
            'updated_by'  => User::first()->id,
            'updated_at'  => now(),
        ]);
    }
}
