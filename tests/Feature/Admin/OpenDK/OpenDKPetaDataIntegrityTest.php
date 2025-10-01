<?php

namespace Tests\Feature\Admin\OpenDK;

use App\Models\User;
use App\Models\Opendk;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OpenDKPetaDataIntegrityTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test bahwa test tidak menghapus data yang ada
     */
    public function test_data_integrity_terjaga()
    {
        // Hitung jumlah data OpenDK sebelum test
        $countBefore = Opendk::count();

        // Buat user untuk testing dengan DatabaseTransactions
        $user = User::factory()->create([
            'email' => 'test_integrity@example.com',
            'name' => 'Test Integrity User'
        ]);

        // Akses halaman peta
        $response = $this->actingAs($user)->get('/opendk/peta');
        $response->assertStatus(200);

        // Akses API peta
        $apiResponse = $this->actingAs($user)
            ->getJson('/opendk/peta', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);
        $apiResponse->assertStatus(200);

        // Pastikan jumlah data tidak berubah setelah test
        $countAfter = Opendk::count();
        $this->assertEquals($countBefore, $countAfter, 'Jumlah data OpenDK berubah setelah test');

        // Test akan otomatis rollback karena DatabaseTransactions
    }

    /**
     * Test bahwa filter tidak mengubah data di database
     */
    public function test_filter_tidak_mengubah_data()
    {
        $countBefore = Opendk::count();

        $user = User::factory()->create([
            'email' => 'test_filter_integrity@example.com',
            'name' => 'Test Filter Integrity'
        ]);

        // Test berbagai filter
        $filters = [
            ['kode_provinsi' => '32'],
            ['kode_kabupaten' => '3201'],
            ['period' => '30'],
            ['period' => '90'],
        ];

        foreach ($filters as $filter) {
            $response = $this->actingAs($user)
                ->getJson('/opendk/peta?' . http_build_query($filter), [
                    'HTTP_X-Requested-With' => 'XMLHttpRequest'
                ]);

            $response->assertStatus(200);
        }

        // Pastikan data tidak berubah
        $countAfter = Opendk::count();
        $this->assertEquals($countBefore, $countAfter, 'Filter mengubah data di database');
    }

    /**
     * Test bahwa query hanya membaca, tidak menulis
     */
    public function test_query_hanya_read_only()
    {
        $user = User::factory()->create([
            'email' => 'test_readonly@example.com',
            'name' => 'Test ReadOnly'
        ]);

        // Ambil snapshot data sebelum test (menggunakan primary key yang benar)
        $dataBefore = Opendk::select('kode_kecamatan', 'created_at', 'updated_at')->get()->toArray();

        // Akses API beberapa kali dengan filter berbeda
        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)
                ->getJson('/opendk/peta?period=' . ($i * 30), [
                    'HTTP_X-Requested-With' => 'XMLHttpRequest'
                ])
                ->assertStatus(200);
        }

        // Bandingkan data setelah test
        $dataAfter = Opendk::select('kode_kecamatan', 'created_at', 'updated_at')->get()->toArray();

        $this->assertEquals($dataBefore, $dataAfter, 'Data di database berubah setelah operasi read');
    }

    /**
     * Test performa query untuk memastikan tidak ada operasi berat yang tidak perlu
     */
    public function test_performa_query_acceptable()
    {
        $user = User::factory()->create([
            'email' => 'test_performance@example.com',
            'name' => 'Test Performance'
        ]);

        $startTime = microtime(true);

        $response = $this->actingAs($user)
            ->getJson('/opendk/peta', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(200);

        // Test tidak boleh lebih dari 10 detik (sesuaikan dengan kebutuhan)
        $this->assertLessThan(10, $executionTime, 'Query peta terlalu lambat: ' . $executionTime . ' detik');
    }
}
