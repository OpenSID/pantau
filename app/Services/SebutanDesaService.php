<?php

namespace App\Services;

use App\Models\Desa;
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
        return Cache::remember('sebutan_desa_list', now()->addDay(), function () {
            $data = Desa::select('sebutan_desa')->whereNotNull('sebutan_desa')
                ->distinct()
                ->pluck('sebutan_desa', 'sebutan_desa')
                ->toArray();

            // Sanitize setiap value untuk mencegah XSS
            return array_map(function ($value) {
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }, $data);
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
