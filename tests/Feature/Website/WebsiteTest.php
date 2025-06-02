<?php

namespace Tests\Feature\Website;

use Tests\TestCase;

class WebsiteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_halaman_utama()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Pengguna Aktif Aplikasi OpenDesa');
        $response->assertSeeText('Peta Pengguna OpenDesa');
        $response->assertSeeText('PantauSID');
        $response->assertSeeText('Peta Pengguna OpenDesa');
        $response->assertSeeTextInOrder([
            'OpenKab',
            'OpenDK',
            'OpenSID',
            'KelolaDesa',
            'LayananDesa',
        ]);
    }

    public function test_halaman_openkab()
    {
        $response = $this->get('/web/openkab');

        $response->assertStatus(200);
        $response->assertSeeText('Info Rilis Terbaru');
        $response->assertSeeText('Pengguna OpenKab');
        $response->assertSeeText('Pengguna OpenSID Terpasang Seluruh Kabupaten');
        $response->assertDontSeeText('Pengguna OpenDK');
        $response->assertDontSeeText('Pengguna LayananDesa');
    }

    public function test_halaman_opendk()
    {
        $response = $this->get('/web/opendk');

        $response->assertStatus(200);
        $response->assertSeeText('Info Rilis Terbaru');
        $response->assertSeeText('Daftar Pengguna OpenDK 7 Hari Terakhir');
        $response->assertSeeText('Daftar Versi Aplikasi OpenDK');
        $response->assertSeeText('OpenDK Terpasang Berdasarkan Bulan');
        $response->assertSeeText('OpenDK Terpasang Berdasarkan Provinsi');
        $response->assertDontSeeText('Pengguna OpenKab');
        $response->assertDontSeeText('Pengguna OpenSID');
    }

    public function test_halaman_opensid()
    {
        $response = $this->get('/web/opensid');

        $response->assertStatus(200);
        $response->assertSeeText('Info Rilis Terbaru');
        $response->assertSeeText('Sebaran Pengguna Baru OpenSID');
        $response->assertSeeText('Daftar Desa Baru Install');
        $response->assertSeeText('OpenSID Terpasang Berdasarkan Bulan');
        $response->assertSeeText('OpenSID Terpasang Berdasarkan Provinsi');
        $response->assertSeeText('Daftar Pengguna OpenSID 7 Hari Terakhir');
        $response->assertSeeText('Versi Yang Terpasang Di Desa OpenSID');
        $response->assertDontSeeText('Pengguna OpenKab');
        $response->assertDontSeeText('Pengguna OpenDk');
    }

    public function test_halaman_layanandesa()
    {
        $response = $this->get('/web/layanandesa');

        $response->assertStatus(200);
        $response->assertSeeText('Info Rilis Terbaru');
        $response->assertSeeText('Daftar Desa Baru Install');
        $response->assertSeeText('Daftar Pengguna Baru LayananDesa 7 Hari Terakhir');
        $response->assertSeeText('Daftar Versi Aplikasi LayananDesa');
        $response->assertDontSeeText('Pengguna OpenKab');
        $response->assertDontSeeText('Pengguna OpenSID');
    }

    public function test_halaman_keloladesa()
    {
        $response = $this->get('/web/keloladesa');

        $response->assertStatus(200);
        $response->assertSeeText('Info Rilis Terbaru');
        $response->assertSeeText('Daftar Desa Baru Install');
        $response->assertSeeText('Daftar Pengguna KelolaDesa 7 Hari Terakhir');
        $response->assertSeeText('Daftar Versi Aplikasi KelolaDesa');
        $response->assertDontSeeText('Pengguna OpenKab');
        $response->assertDontSeeText('Pengguna OpenSID');
    }
}
