<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SebutanDesaService
{
    /**
     * Cache key for sebutan desa list.
     */
    private const CACHE_KEY = 'sebutan_desa_list';
    
    /**
     * Cache duration in hours.
     */
    private const CACHE_DURATION_HOURS = 24;

    /**
     * Get all unique sebutan desa values from database.
     *
     * @return array<string>
     */
    public function getSebutanDesaList(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHours(self::CACHE_DURATION_HOURS), function () {
            return \App\Models\Desa::select('sebutan_desa')
                ->whereNotNull('sebutan_desa')
                ->where('sebutan_desa', '!=', '')
                ->distinct()
                ->orderBy('sebutan_desa')
                ->pluck('sebutan_desa')
                ->toArray();
        });
    }

    /**
     * Clear the sebutan desa cache.
     *
     * @return bool
     */
    public function clearCache(): bool
    {
        return Cache::forget(self::CACHE_KEY);
    }
}
