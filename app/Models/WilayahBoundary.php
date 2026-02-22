<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahBoundary extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wilayah_boundaries';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'kode';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if timestamps should be updated.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode',
        'level',
        'lat',
        'lng',
        'path',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lat' => 'double',
        'lng' => 'double',
        'path' => 'array',
        'status' => 'integer',
    ];

    /**
     * Get the region that owns this boundary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function region()
    {
        return $this->hasOne(Region::class, 'region_code', 'kode');
    }

    /**
     * Scope a query to filter by level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $level
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope a query to filter by provinsi level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProvinsi($query)
    {
        return $query->where('level', 'prov');
    }

    /**
     * Scope a query to filter by kabupaten level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKabupaten($query)
    {
        return $query->where('level', 'kab');
    }

    /**
     * Scope a query to filter by kecamatan level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKecamatan($query)
    {
        return $query->where('level', 'kec');
    }

    /**
     * Scope a query to filter by kelurahan/desa level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKelurahan($query)
    {
        return $query->where('level', 'kel');
    }

    /**
     * Scope a query to filter by active status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to search by kode or region name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('region', function ($q) use ($search) {
            $q->where('region_name', 'like', "%{$search}%")
              ->orWhere('region_code', 'like', "%{$search}%");
        });
    }

    /**
     * Get the boundary as a GeoJSON Feature.
     *
     * @return array
     */
    public function toGeoJSONFeature(): array
    {
        return [
            'type' => 'Feature',
            'properties' => [
                'kode' => $this->kode,
                'level' => $this->level,
                'name' => $this->region?->region_name ?? null,
                'new_name' => $this->region?->new_region_name ?? null,
                'lat' => $this->lat,
                'lng' => $this->lng,
            ],
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => $this->path ?? null,
            ],
        ];
    }

    /**
     * Get the centroid coordinates.
     *
     * @return array|null
     */
    public function getCentroidAttribute(): ?array
    {
        if ($this->lat && $this->lng) {
            return [$this->lng, $this->lat];
        }

        return null;
    }
}
