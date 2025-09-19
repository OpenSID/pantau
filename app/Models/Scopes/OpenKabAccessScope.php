<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OpenKabAccessScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Only apply scope if user has "Admin Wilayah" role
        if (!$user->hasRole('Admin Wilayah')) {
            return;
        }

        // Get user's region access
        $regionAccess = $user->userRegionAccess;

        if (!$regionAccess) {
            // If no region access is defined for Admin Wilayah, restrict to no results
            $builder->whereRaw('1 = 0');
            return;
        }
        // Apply kabupaten restriction (without table prefix to avoid issues with complex queries)
        if ($regionAccess->kode_kabupaten) {
            $builder->where('kode_kab', $regionAccess->kode_kabupaten);
        } elseif ($regionAccess->kode_provinsi) {
            // If only provinsi is set, restrict to that provinsi
            $builder->where('kode_prov', $regionAccess->kode_provinsi);
        } else {
            // If no specific region is set, restrict to no results
            $builder->whereRaw('1 = 0');
        }
    }

    /**
     * Extend the query builder with macros.
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withoutOpenKabScope', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
