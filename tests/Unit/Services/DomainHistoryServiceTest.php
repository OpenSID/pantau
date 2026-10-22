<?php

namespace Tests\Unit\Services;

use App\Models\Desa;
use App\Models\DomainHistory;
use App\Services\DomainHistoryService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DomainHistoryServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected DomainHistoryService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DomainHistoryService();
    }

    public function test_domain_baru_dicatat_sebagai_aktif_saat_desa_baru_dibuat()
    {
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.0001']);

        $history = $this->service->recordDomainChange($desa->kode_desa, 'https://www.Desa-Baru.example.id/path', 'crawler');

        $this->assertNotNull($history);
        $this->assertTrue($history->is_active);
        $this->assertSame('desa-baru.example.id', $history->domain_normalized);
        $this->assertSame($desa->id, $history->desa_id);
        $this->assertSame('crawler', $history->source);
    }

    public function test_domain_berubah_memindahkan_domain_lama_ke_riwayat()
    {
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.0002']);

        $lama = $this->service->recordDomainChange($desa->kode_desa, 'desa-lama.example.id', 'crawler');

        $baru = $this->service->recordDomainChange($desa->kode_desa, 'desa-baru.example.id', 'crawler');

        $lama->refresh();

        $this->assertFalse($lama->is_active);
        $this->assertTrue($baru->is_active);
        $this->assertSame('desa-baru.example.id', $baru->domain_normalized);
        $this->assertSame('desa-baru.example.id', $desa->refresh()->domain);
    }

    public function test_hanya_satu_domain_aktif_per_desa()
    {
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.0003']);

        $this->service->recordDomainChange($desa->kode_desa, 'satu.example.id', 'crawler');
        $this->service->recordDomainChange($desa->kode_desa, 'dua.example.id', 'crawler');
        $this->service->recordDomainChange($desa->kode_desa, 'tiga.example.id', 'crawler');

        $aktif = DomainHistory::where('desa_id', $desa->id)->where('is_active', true)->count();

        $this->assertSame(1, $aktif);
        $this->assertSame('tiga.example.id', $desa->refresh()->domain);
    }

    public function test_domain_yang_sama_tidak_membuat_entri_riwayat_baru()
    {
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.0004']);

        $this->service->recordDomainChange($desa->kode_desa, 'sama.example.id', 'crawler');
        $this->service->recordDomainChange($desa->kode_desa, 'https://www.sama.example.id', 'crawler');

        $this->assertSame(1, DomainHistory::where('desa_id', $desa->id)->count());
    }

    public function test_kode_desa_tidak_ditemukan_disimpan_dengan_desa_id_null()
    {
        $history = $this->service->recordDomainChange('99.99.99.9999', 'yatim.example.id', 'crawler');

        $this->assertNotNull($history);
        $this->assertNull($history->desa_id);
        $this->assertSame('99.99.99.9999', $history->kode_desa);
        $this->assertFalse($history->is_active);
    }

    public function test_domain_kosong_tidak_membuat_riwayat()
    {
        $this->assertNull($this->service->recordDomainChange('11.22.33.0005', '', 'crawler'));
    }

    public function test_set_active_memindahkan_status_ke_domain_yang_dipilih()
    {
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.0006']);

        $this->service->recordDomainChange($desa->kode_desa, 'lama.example.id', 'crawler');
        $baru = $this->service->recordDomainChange($desa->kode_desa, 'baru.example.id', 'crawler');

        $this->assertTrue($this->service->setActive($desa, $baru));

        $this->assertSame(1, DomainHistory::where('desa_id', $desa->id)->where('is_active', true)->count());
        $this->assertSame('baru.example.id', $desa->refresh()->domain);
    }

    public function test_get_domain_history_mengembalikan_riwayat_terbaru_dulu()
    {
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.0007']);

        $this->service->recordDomainChange($desa->kode_desa, 'satu.example.id', 'crawler');
        $this->service->recordDomainChange($desa->kode_desa, 'dua.example.id', 'crawler');

        $histories = $this->service->getDomainHistory($desa);

        $this->assertCount(2, $histories);
        $this->assertSame('dua.example.id', $histories->first()->domain_normalized);
    }
}
