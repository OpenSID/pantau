<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\DomainHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class DomainHistoryControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::define('is-admin', function ($user) {
            return true;
        });
    }

    public function test_admin_bisa_melihat_riwayat_domain()
    {
        $user = User::factory()->create();
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.1001']);

        DomainHistory::create([
            'desa_id' => $desa->id,
            'kode_desa' => $desa->kode_desa,
            'domain' => 'desa-lama.example.id',
            'domain_normalized' => 'desa-lama.example.id',
            'is_active' => false,
            'source' => 'crawler',
            'obtained_at' => now()->subMonth(),
        ]);

        $response = $this->actingAs($user)->get(route('desa.domain-history', $desa));

        $response->assertStatus(200);
        $response->assertSee('desa-lama.example.id');
        $response->assertSee('Riwayat Domain');
    }

    public function test_admin_bisa_mencatat_domain_baru()
    {
        $user = User::factory()->create();
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.1002']);

        $response = $this->actingAs($user)->post(route('desa.domain-history.store', $desa), [
            'domain' => 'https://www.Domain-Manual.example.id',
            'note' => 'Dicatat manual oleh admin',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('domain_histories', [
            'desa_id' => $desa->id,
            'domain_normalized' => 'domain-manual.example.id',
            'is_active' => true,
            'source' => 'manual',
        ]);

        $this->assertSame('domain-manual.example.id', $desa->refresh()->domain);
    }

    public function test_admin_bisa_set_active_domain_lama()
    {
        $user = User::factory()->create();
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.1003']);

        $lama = DomainHistory::create([
            'desa_id' => $desa->id,
            'kode_desa' => $desa->kode_desa,
            'domain' => 'lama.example.id',
            'domain_normalized' => 'lama.example.id',
            'is_active' => false,
            'source' => 'crawler',
            'obtained_at' => now()->subMonth(),
        ]);

        $baru = DomainHistory::create([
            'desa_id' => $desa->id,
            'kode_desa' => $desa->kode_desa,
            'domain' => 'baru.example.id',
            'domain_normalized' => 'baru.example.id',
            'is_active' => true,
            'source' => 'crawler',
            'obtained_at' => now(),
        ]);

        $response = $this->actingAs($user)->patch(route('desa.domain-history.set-active', [$desa, $lama]));

        $response->assertRedirect();

        $this->assertTrue($lama->refresh()->is_active);
        $this->assertFalse($baru->refresh()->is_active);
        $this->assertSame('lama.example.id', $desa->refresh()->domain);
    }

    public function test_tamu_tidak_bisa_melihat_riwayat_domain()
    {
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.1004']);

        $this->get(route('desa.domain-history', $desa))->assertRedirect(route('login'));
    }

    public function test_unduh_csv_riwayat_domain()
    {
        $user = User::factory()->create();
        $desa = Desa::factory()->create(['kode_desa' => '11.22.33.1005']);

        DomainHistory::create([
            'desa_id' => $desa->id,
            'kode_desa' => $desa->kode_desa,
            'domain' => 'csv.example.id',
            'domain_normalized' => 'csv.example.id',
            'is_active' => true,
            'source' => 'crawler',
            'obtained_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('desa.domain-history.download', $desa));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}
