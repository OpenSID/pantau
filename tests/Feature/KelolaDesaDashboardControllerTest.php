<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\TrackKeloladesa;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KelolaDesaDashboardControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_displays_dashboard_correctly()
    {
        // Create test data
        $desa = Desa::factory()->create();        

        $response = $this->get('/web/keloladesa');

        $response->assertStatus(200);
        $response->assertViewIs('website.keloladesa.index');
        $response->assertViewHas([            
            'fillters',  
            'versi_terakhir',
            'info_rilis',
            'total_versi',
            'pengguna_versi_terakhir'
        ]);
    }

    public function test_index_with_filters()
    {
        $response = $this->get('/web/keloladesa?kode_provinsi=32&kode_kabupaten=3201&kode_kecamatan=320101');

        $response->assertStatus(200);
        $response->assertViewHas('fillters', [
            'kode_provinsi' => '32',
            'kode_kabupaten' => '3201',
            'kode_kecamatan' => '320101'
        ]);
    }

    public function test_detail_page_displays_correctly()
    {
        $response = $this->get('/web/keloladesa/detail');

        $response->assertStatus(200);
        $response->assertViewIs('website.keloladesa.detail');
    }

    public function test_versi_page_displays_correctly()
    {
        $response = $this->get('/web/keloladesa/versi');

        $response->assertStatus(200);
        $response->assertViewIs('website.keloladesa.versi_lengkap');
        $response->assertViewHas('fillters');
    }

    public function test_versi_ajax_returns_datatable_data()
    {
        // Create test data
        TrackKeloladesa::factory()->create(['versi' => '2507.0.0']);
        TrackKeloladesa::factory()->create(['versi' => '2403.0.0']);

        $response = $this->get('/web/keloladesa/versi', [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'draw',
            'recordsTotal',
            'recordsFiltered'
        ]);
    }

    public function test_versi_detail_page_displays_correctly()
    {
        $response = $this->get('/web/keloladesa/versi/detail');

        $response->assertStatus(200);
        $response->assertViewIs('website.keloladesa.versi_detail');
        $response->assertViewHas('fillters');
    }

    public function test_versi_detail_ajax_returns_datatable_data()
    {
        // Create test data
        $desa = Desa::factory()->create();
        TrackKeloladesa::factory()->create([
            'kode_desa' => $desa->kode_desa,
            'versi' => '2507.0.0'
        ]);

        $response = $this->get('/web/keloladesa/versi/detail?versi=2507.0.0', [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'draw',
            'recordsTotal',
            'recordsFiltered'
        ]);
    }

    public function test_install_baru_ajax_returns_datatable_data()
    {
        // Create test data
        $desa = Desa::factory()->create();
        TrackKeloladesa::factory()->create([
            'kode_desa' => $desa->kode_desa,
            'created_at' => Carbon::now()
        ]);

        $response = $this->get('/web/keloladesa/install_baru', [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'draw',
            'recordsTotal',
            'recordsFiltered'
        ]);
    }

    public function test_peta_ajax_returns_geojson_data()
    {
        // Create test data with valid coordinates
        $desa = Desa::factory()->create([
            'lat' => '-6.2088',
            'lng' => '106.8456',
            'nama_desa' => 'Test Desa',
            'sebutan_desa' => 'Desa',
            'nama_kecamatan' => 'Test Kecamatan',
            'nama_kabupaten' => 'Test Kabupaten',
            'nama_provinsi' => 'Test Provinsi',
            'alamat_kantor' => 'Jl. Test No. 123'
        ]);
        TrackKeloladesa::factory()->create(['kode_desa' => $desa->kode_desa]);

        $response = $this->get('/web/keloladesa/peta', [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'type',
            'features' => [
                '*' => [
                    'type',
                    'geometry' => [
                        'type',
                        'coordinates'
                    ],
                    'properties',
                    'id'
                ]
            ]
        ]);

        $data = $response->json();
        $this->assertEquals('FeatureCollection', $data['type']);
    }

    public function test_peta_with_period_filter()
    {
        // Create test data
        $desa = Desa::factory()->create([
            'lat' => '-6.2088',
            'lng' => '106.8456'
        ]);
        TrackKeloladesa::factory()->create([
            'kode_desa' => $desa->kode_desa,
            'created_at' => Carbon::now()
        ]);

        $period = Carbon::now()->format('Y-m-d') . ' - ' . Carbon::now()->format('Y-m-d');

        $response = $this->get('/web/keloladesa/peta?period=' . urlencode($period), [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'type',
            'features'
        ]);
    }
}
