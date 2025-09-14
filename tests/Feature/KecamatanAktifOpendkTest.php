<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Opendk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class KecamatanAktifOpendkTest extends TestCase
{
    use WithFaker;

    /**
     * Test halaman kecamatan aktif OpenDK dapat diakses oleh user yang sudah login.
     *
     * @return void
     */
    public function test_kecamatan_aktif_opendk_page_can_be_accessed_by_authenticated_user()
    {
        // Buat user untuk testing
        $user = User::factory()->create();

        // Login sebagai user
        $this->actingAs($user);

        // Akses halaman kecamatan aktif opendk
        $response = $this->get('/opendk/kecamatan-aktif');

        // Assert bahwa halaman dapat diakses dengan status 200
        $response->assertStatus(200);

        // Assert bahwa view yang benar digunakan
        $response->assertViewIs('opendk.kecamatan_aktif');

        // Assert bahwa variabel fillters ada di view
        $response->assertViewHas('fillters');

        // Assert bahwa halaman mengandung judul yang benar
        $response->assertSee('Kecamatan Aktif OpenDK');

        // Assert bahwa tabel DataTable ada
        $response->assertSee('table-kecamatan-aktif');
    }

    /**
     * Test halaman kecamatan aktif OpenDK dapat diakses tanpa login.
     *
     * @return void
     */
    public function test_kecamatan_aktif_opendk_page_can_be_accessed_without_authentication()
    {
        // Akses halaman tanpa login
        $response = $this->get('/opendk/kecamatan-aktif');

        // Assert bahwa halaman dapat diakses dengan status 200
        $response->assertStatus(200);

        // Assert bahwa view yang benar digunakan
        $response->assertViewIs('opendk.kecamatan_aktif');

        // Assert bahwa halaman mengandung judul yang benar
        $response->assertSee('Kecamatan Aktif OpenDK');
    }

    /**
     * Test AJAX request untuk data kecamatan aktif OpenDK.
     *
     * @return void
     */
    public function test_kecamatan_aktif_opendk_ajax_request_returns_json()
    {
        // Buat user untuk testing
        $user = User::factory()->create();

        // Login sebagai user
        $this->actingAs($user);

        // Lakukan AJAX request
        $response = $this->get('/opendk/kecamatan-aktif', [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        // Assert bahwa response adalah JSON
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        // Assert struktur JSON response dari DataTables
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data'
        ]);
    }

    /**
     * Test halaman kecamatan aktif dengan filter parameter.
     *
     * @return void
     */
    public function test_kecamatan_aktif_opendk_page_with_filters()
    {
        // Buat user untuk testing
        $user = User::factory()->create();

        // Login sebagai user
        $this->actingAs($user);

        // Akses halaman dengan parameter filter
        $response = $this->get('/opendk/kecamatan-aktif?kode_provinsi=32&akses_opendk=1');

        // Assert bahwa halaman dapat diakses
        $response->assertStatus(200);

        // Assert bahwa filter diterapkan
        $response->assertViewHas('fillters', function ($fillters) {
            return $fillters['kode_provinsi'] === '32' &&
                $fillters['akses_opendk'] === '1';
        });
    }

    /**
     * Test bahwa halaman menampilkan struktur tabel yang benar.
     *
     * @return void
     */
    public function test_kecamatan_aktif_opendk_page_displays_correct_table_structure()
    {
        // Buat user untuk testing
        $user = User::factory()->create();

        // Login sebagai user
        $this->actingAs($user);

        // Akses halaman kecamatan aktif opendk
        $response = $this->get('/opendk/kecamatan-aktif');

        // Assert bahwa halaman mengandung header tabel yang benar
        $response->assertSee('Nama Kecamatan');
        $response->assertSee('Jumlah Desa');
        $response->assertSee('Akses Publik Selama 30 Hari');
        $response->assertSee('Akses Admin Selama 30 Hari');
        $response->assertSee('Jumlah Artikel');
        $response->assertSee('Akses Terakhir');

        // Assert bahwa JavaScript untuk DataTable ada
        $response->assertSee('table-kecamatan-aktif');
        $response->assertSee('DataTable');
    }

    /**
     * Test bahwa halaman tidak merusak data di database.
     *
     * @return void
     */
    public function test_kecamatan_aktif_opendk_page_does_not_modify_database()
    {
        // Buat user untuk testing
        $user = User::factory()->create();

        // Login sebagai user
        $this->actingAs($user);

        // Hitung jumlah record sebelum akses halaman
        $opendkCountBefore = Opendk::count();

        // Akses halaman kecamatan aktif opendk
        $response = $this->get('/opendk/kecamatan-aktif');

        // Hitung jumlah record setelah akses halaman
        $opendkCountAfter = Opendk::count();

        // Assert bahwa tidak ada perubahan data
        $this->assertEquals($opendkCountBefore, $opendkCountAfter);

        // Assert bahwa halaman berhasil diakses
        $response->assertStatus(200);
    }
}
