<?php

namespace App\Services;

use App\Models\Desa;
use Illuminate\Support\Facades\Cache;

class TemaService
{
    /**
     * Cache key for sebutan desa list.
     */
    private const CACHE_KEY = 'tema_list';

    /**
     * Cache duration in hours.
     */
    private const CACHE_DURATION_HOURS = 24;

    /**
     * Get all unique sebutan desa values from database.
     *
     * @return array<string>
     */
    public function getList(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addDay(), function () {
            $data = Desa::select('tema')->whereNotNull('tema')
                ->distinct()
                ->pluck('tema', 'tema')
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
