<?php

namespace App\Imports;

use App\Models\Suku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class SukuImport implements ToModel, WithBatchInserts, WithUpserts, WithHeadingRow
{
    private $regionMap = [];

    public function __construct()
    {
        ini_set('memory_limit', '-1');
        $this->regionMap = \App\Models\Region::selectRaw('id, UPPER(region_name) as region_name')->where('parent_code', 0)->pluck('id', 'region_name')->toArray();
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
        return ['tbl_region_id', 'name'];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if the region name exists in the map
        $namaProvinsi = trim(strtoupper(str_replace('Provinsi', '', $row['nama_provinsi'])));
        $regionId = $this->regionMap[$namaProvinsi] ?? null;
        if (! $regionId) {
            return null; // Skip if region ID is not found
        }

        return new Suku([
            'tbl_region_id' => $regionId,
            'name' => $row['nama_suku'],
        ]);
    }
}
