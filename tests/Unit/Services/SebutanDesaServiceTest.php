<?php

namespace Tests\Unit\Services;

use App\Models\Desa;
use App\Services\SebutanDesaService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SebutanDesaServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_sebutan_desa_list_returns_cached_data()
    {
        // Put data in cache first
        Cache::put('sebutan_desa_list', ['Desa', 'Kelurahan'], now()->addDay());

        $service = new SebutanDesaService();
        $result = $service->getSebutanDesaList();

        $this->assertEquals(['Desa', 'Kelurahan'], $result);
    }

    public function test_get_sebutan_desa_list_queries_database_on_cache_miss()
    {
        // Get existing desa records from database
        $desaRecords = Desa::take(3)->get();

        // Ensure we have at least 3 records
        $this->assertGreaterThanOrEqual(3, $desaRecords->count(), 'Need at least 3 desa records for this test');

        // Update sebutan_desa values on existing records
        $desaRecords[0]->update(['sebutan_desa' => 'Desa']);
        $desaRecords[1]->update(['sebutan_desa' => 'Kelurahan']);
        $desaRecords[2]->update(['sebutan_desa' => 'Desa']); // duplicate

        // Clear cache to ensure fresh data is fetched
        $service = new SebutanDesaService();
        $service->clearCache();

        // Get sebutan desa list (should query database on cache miss)
        $result = $service->getSebutanDesaList();

        // Assert that the service returns data from database
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertContains('Desa', $result);
        $this->assertContains('Kelurahan', $result);
    }
}