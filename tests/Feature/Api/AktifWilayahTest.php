<?php

namespace Tests\Feature\Api;

use App\Models\Desa;
use App\Models\Opendk;
use App\Models\TrackKeloladesa;
use App\Models\TrackMobile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AktifWilayahTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup data for OpenSID
        // Gunakan manual jika factory bermasalah
        Desa::create([
            'nama_desa' => 'Desa Test',
            'kode_desa' => '1101012001',
            'kode_provinsi' => '11',
            'kode_kabupaten' => '1101',
            'kode_kecamatan' => '110101',
            'tgl_akses_lokal' => Carbon::now()->subDays(2),
            'versi_lokal' => '23.01',
        ]);

        // Setup data for Layanan Desa (TrackMobile)
        TrackMobile::create([
            'id' => 'device1',
            'kode_desa' => '1101012001',
            'tgl_akses' => Carbon::now()->subDays(2),
            'versi' => '1.0.0',
        ]);

        // Setup data for Kelola Desa (TrackKeloladesa)
        TrackKeloladesa::create([
            'id_device' => 'device2',
            'kode_desa' => '1101012001',
            'tgl_akses' => Carbon::now()->subDays(2),
            'versi' => '1.0.0',
        ]);

        // Setup data for OpenDK (Opendk)
        Opendk::create([
            'kode_kecamatan' => '110101',
            'nama_kecamatan' => 'Kecamatan Test',
            'kode_kabupaten' => '1101',
            'nama_kabupaten' => 'Kabupaten Test',
            'kode_provinsi' => '11',
            'nama_provinsi' => 'Provinsi Test',
            'updated_at' => Carbon::now()->subDays(2),
            'versi' => '1.0.0',
        ]);
    }

    /** @test */
    public function opensid_aktif_filter_period()
    {
        $start = Carbon::now()->subDays(5)->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d');
        $period = "$start - $end";

        // Pastikan route ini ada dan benar
        $response = $this->getJson("/api/web/desa-aktif-opensid?period=$period");

        $response->assertStatus(200);
        $this->assertArrayHasKey('aktif', $response->json());
    }

    /** @test */
    public function layanandesa_aktif_filter_period()
    {
        $start = Carbon::now()->subDays(5)->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d');
        $period = "$start - $end";

        $response = $this->getJson("/api/web/aktif-layanandesa?period=$period");

        $response->assertStatus(200);
        $this->assertArrayHasKey('aktif', $response->json());
        $this->assertGreaterThanOrEqual(1, $response->json('aktif'));
    }

    /** @test */
    public function keloladesa_aktif_filter_period()
    {
        $start = Carbon::now()->subDays(5)->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d');
        $period = "$start - $end";

        $response = $this->getJson("/api/web/aktif-keloladesa?period=$period");

        $response->assertStatus(200);
        $this->assertArrayHasKey('aktif', $response->json());
        $this->assertGreaterThanOrEqual(1, $response->json('aktif'));
    }

    /** @test */
    public function opendk_aktif_filter_period()
    {
        $start = Carbon::now()->subDays(5)->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d');
        $period = "$start - $end";

        $response = $this->getJson("/api/web/aktif-opendk?period=$period");

        $response->assertStatus(200);
        $this->assertArrayHasKey('aktif', $response->json());
        $this->assertGreaterThanOrEqual(1, $response->json('aktif'));
    }
}
