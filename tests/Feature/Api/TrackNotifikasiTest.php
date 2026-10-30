<?php

namespace Tests\Feature\Api;

use App\Models\Desa;
use App\Models\Notifikasi;
use App\Models\NotifikasiDesa;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class TrackNotifikasiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_non_aktifkan_menandai_semua_notifikasi_tanpa_duplikat()
    {
        $desa = Desa::factory()->create();
        $notifikasi = collect(range(1, 3))->map(fn () => Notifikasi::factory()->create([
            'kode' => Str::random(20),
            'aktif' => 1,
        ]));

        NotifikasiDesa::nonAktifkan(collect($notifikasi->map(fn ($n) => (object) ['id' => $n->id])), $desa->id);
        NotifikasiDesa::nonAktifkan(collect($notifikasi->map(fn ($n) => (object) ['id' => $n->id])), $desa->id);

        $this->assertSame(3, NotifikasiDesa::where('id_desa', $desa->id)->count());
        $this->assertSame(0, NotifikasiDesa::where('id_desa', $desa->id)->where('status', '!=', 0)->count());
    }

    public function test_non_aktifkan_tanpa_notifikasi_tidak_membuat_apa_apa()
    {
        $desa = Desa::factory()->create();

        NotifikasiDesa::nonAktifkan(collect(), $desa->id);

        $this->assertSame(0, NotifikasiDesa::where('id_desa', $desa->id)->count());
    }
}
