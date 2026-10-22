const { test, expect } = require('@playwright/test');
const { execSync } = require('child_process');

/**
 * E2E untuk fitur Riwayat Domain per Desa.
 *
 * Skenario:
 * 1. Crawler mengirim domain lama untuk sebuah kode_desa.
 * 2. Crawler mengirim domain baru untuk kode_desa yang sama.
 * 3. Daftar desa tetap menampilkan 1 entri desa.
 * 4. Halaman riwayat domain menampilkan 2 domain dengan hanya 1 aktif.
 */
test.describe('Riwayat Domain Desa', () => {
  const kodeDesa = '99.88.77.0001';

  test.beforeAll(() => {
    try {
      execSync(`php artisan tinker --execute="
        \\App\\Models\\DomainHistory::where('kode_desa', '${kodeDesa}')->delete();
        $desa = \\App\\Models\\Desa::where('kode_desa', '${kodeDesa}')->first();
        if ($desa) {
          \\App\\Models\\DomainHistory::where('desa_id', $desa->id)->delete();
          $desa->domain = null;
          $desa->domain_updated_at = null;
          $desa->save();
        }
      "`, { stdio: 'pipe' });
    } catch (e) {
      console.error('Gagal menyiapkan data E2E riwayat domain:', e.message);
    }
  });

  test('halaman riwayat domain menampilkan domain lama dan baru', async ({ page }) => {
    // Kirim domain lama lalu domain baru untuk kode_desa yang sama.
    const kirim = async (domain) => {
      const output = execSync(`php artisan tinker --execute="
        $service = app(\\App\\Services\\DomainHistoryService::class);
        $service->recordDomainChange('${kodeDesa}', '${domain}', 'crawler');
        echo 'OK';
      "`, { stdio: 'pipe' }).toString();

      expect(output).toContain('OK');
    };

    await kirim('desa-lama-e2e.example.id');
    await kirim('desa-baru-e2e.example.id');

    // Ambil id desa untukgeda navigasi.
    const desaId = execSync(`php artisan tinker --execute="
      $desa = \\App\\Models\\Desa::where('kode_desa', '${kodeDesa}')->first();
      echo $desa ? $desa->id : '';
    "`, { stdio: 'pipe' }).toString().trim();

    expect(desaId).not.toBe('');

    await page.goto(`/desa/${desaId}/domain-history`);

    // Kedua domain harus terlihat.
    await expect(page.locator('body')).toContainText('desa-lama-e2e.example.id');
    await expect(page.locator('body')).toContainText('desa-baru-e2e.example.id');

    // Hanya satu domain yang berstatus Aktif.
    const badgeAktif = page.locator('.badge-success', { hasText: 'Aktif' });
    await expect(badgeAktif).toHaveCount(1);
  });

  test('daftar desa menampilkan kolom domain saat ini', async ({ page }) => {
    await page.goto('/web/opensid');

    await page.waitForSelector('#table-semua-desa', { timeout: 30000 });

    // Header kolom domain hanya tampil untuk pengguna terautentikasi.
    await expect(page.locator('#table-semua-desa thead th', { hasText: 'Domain Saat Ini' })).toHaveCount(1);

    // Tombol aksi riwayat domain tersedia pada baris tabel.
    await expect(page.locator('#table-semua-desa a[title="Riwayat Domain"]').first()).toBeVisible();
  });

  test('daftar desa tidak duplikat untuk kode_desa yang sama', async ({ page }) => {
    const jumlah = execSync(`php artisan tinker --execute="
      echo \\App\\Models\\Desa::where('kode_desa', '${kodeDesa}')->count();
    "`, { stdio: 'pipe' }).toString().trim();

    expect(Number(jumlah)).toBeLessThanOrEqual(1);
  });
});
