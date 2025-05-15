<?php

namespace App\Imports;

use App\Models\Marga;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class MargaImport implements ToModel, WithBatchInserts, WithUpserts, WithHeadingRow
{
    private $regionMap = [];
    private $ethnicMap = [];
    public function __construct()
    {
        ini_set('memory_limit', '-1');
        $this->regionMap = \App\Models\Region::selectRaw('id, UPPER(region_name) as region_name')->where('parent_code',0)->pluck('id', 'region_name')->toArray();
        $this->ethnicMap = \App\Models\Suku::selectRaw('id, UPPER(name) as name, tbl_region_id')->get()->groupBy('tbl_region_id')->map(function ($item) {
            return $item->pluck('id', 'name')->toArray();
        })->toArray();
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
        return ['ethnic_group_id', 'name'];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if the region name exists in the map
        $namaProvinsi = trim(strtoupper(str_replace('Provinsi','',$row['nama_provinsi'])));
        $regionId = $this->regionMap[$namaProvinsi] ?? null;
        if(!$regionId) {
            return null; // Skip if region ID is not found
        }
        if(!isset($this->ethnicMap[$regionId])) {
            return null;
        }

        $ethnicId = $this->ethnicMap[$regionId][trim(strtoupper($row['nama_suku']))] ?? null;
        if(!$ethnicId) {
            return null; // Skip
        }

        return new Marga([
            'ethnic_group_id' => $ethnicId,
            'name' => $row['nama_marga'],
        ]);
    }
}
