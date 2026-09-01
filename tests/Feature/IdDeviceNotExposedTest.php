<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\TrackKeloladesa;
use App\Models\TrackMobile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IdDeviceNotExposedTest extends TestCase
{
    use DatabaseTransactions;

    private function makeDesa()
    {
        return Desa::factory()->create([
            'kode_desa' => '1101012001',
            'nama_desa' => 'Desa Test',
            'nama_kecamatan' => 'Kecamatan Test',
            'nama_kabupaten' => 'Kabupaten Test',
            'nama_provinsi' => 'Provinsi Test',
        ]);
    }

    public function test_keloladesa_install_baru_does_not_expose_id_device()
    {
        $desa = $this->makeDesa();

        TrackKeloladesa::create([
            'id_device' => 'device-rahasia-123',
            'kode_desa' => $desa->kode_desa,
            'tgl_akses' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $response = $this->get('/web/keloladesa/install_baru', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'draw',
            'recordsTotal',
            'recordsFiltered',
        ]);

        $payload = $response->json();

        $this->assertNotEmpty($payload['data']);
        $this->assertArrayNotHasKey('id_device', $payload['data'][0]);
        $this->assertArrayNotHasKey('id', $payload['data'][0]);
        $this->assertDatabaseHas('track_keloladesa', ['id_device' => 'device-rahasia-123']);
    }

    public function test_layanandesa_install_baru_does_not_expose_id()
    {
        $desa = $this->makeDesa();

        TrackMobile::create([
            'id' => 'device-rahasia-456',
            'kode_desa' => $desa->kode_desa,
            'tgl_akses' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $response = $this->get('/web/layanandesa/install_baru', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'draw',
            'recordsTotal',
            'recordsFiltered',
        ]);

        $payload = $response->json();

        $this->assertNotEmpty($payload['data']);
        $this->assertArrayNotHasKey('id', $payload['data'][0]);
    }

    public function test_datatable_pengguna_keloladesa_does_not_expose_id_device()
    {
        $desa = $this->makeDesa();

        TrackKeloladesa::create([
            'id_device' => 'device-rahasia-789',
            'kode_desa' => $desa->kode_desa,
            'tgl_akses' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $response = $this->get('/datatables/pengguna-keloladesa', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $payload = $response->json();

        $this->assertNotEmpty($payload['data']);
        $this->assertArrayNotHasKey('id_device', $payload['data'][0]);
    }

    public function test_datatable_pengguna_layanandesa_does_not_expose_id()
    {
        $desa = $this->makeDesa();

        TrackMobile::create([
            'id' => 'device-rahasia-101',
            'kode_desa' => $desa->kode_desa,
            'tgl_akses' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $response = $this->get('/datatables/pengguna-layanandesa', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $payload = $response->json();

        $this->assertNotEmpty($payload['data']);
        $this->assertArrayNotHasKey('id', $payload['data'][0]);
    }

    public function test_endpoints_are_public_without_authentication()
    {
        $desa = $this->makeDesa();

        TrackKeloladesa::create([
            'id_device' => 'device-rahasia-202',
            'kode_desa' => $desa->kode_desa,
            'tgl_akses' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $response = $this->get('/web/keloladesa/install_baru', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);

        $payload = $response->json();
        $this->assertNotEmpty($payload['data']);
        $this->assertArrayNotHasKey('id_device', $payload['data'][0]);
    }
}
