<?php

namespace App\Models\Traits;

use App\Models\Scopes\RegionAccessScope;

trait HasRegionAccess
{
    /**
     * The "booted" method of the model.
     */
    protected static function bootHasRegionAccess(): void
    {
        static::addGlobalScope(new RegionAccessScope);
    }

    /**
     * Scope a query to filter by user's region access.
     * This can be used manually when the global scope is disabled.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\User|null  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUserRegionAccess($query, $user = null)
    {
        if (! $user) {
            $user = auth()->user();
        }

        if (! $user || ! $user->hasRole('Admin Wilayah')) {
            return $query;
        }

        $regionAccess = $user->userRegionAccess;

        if (! $regionAccess) {
            return $query->whereRaw('1 = 0');
        }

        if ($regionAccess->kode_kabupaten) {
            return $query->where('kode_kabupaten', $regionAccess->kode_kabupaten);
        } elseif ($regionAccess->kode_provinsi) {
            return $query->where('kode_provinsi', $regionAccess->kode_provinsi);
        }

        return $query->whereRaw('1 = 0');
    }
}
