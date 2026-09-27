<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WilayahBoundaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'kode' => $this->kode,
            'level' => $this->level,
            'level_name' => $this->getLevelName(),
            'lat' => $this->lat,
            'lng' => $this->lng,
            'centroid' => $this->centroid,
            'path' => $this->path,
            'status' => $this->status,
            'region' => $this->whenLoaded('region', function () {
                return [
                    'region_code' => $this->region->region_code,
                    'region_name' => $this->region->region_name,
                    'new_region_name' => $this->region->new_region_name,
                    'parent_code' => $this->region->parent_code,
                ];
            }),
            'created_at' => $this->when(isset($this->created_at), $this->created_at),
            'updated_at' => $this->when(isset($this->updated_at), $this->updated_at),
        ];
    }

    /**
     * Get human-readable level name.
     *
     * @return string
     */
    private function getLevelName(): string
    {
        $names = [
            'prov' => 'Provinsi',
            'kab' => 'Kabupaten/Kota',
            'kec' => 'Kecamatan',
            'kel' => 'Kelurahan/Desa',
        ];

        return $names[$this->level] ?? ucfirst($this->level);
    }
}
