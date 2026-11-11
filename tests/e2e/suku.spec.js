const { test, expect } = require('@playwright/test');
const E2ETestHelper = require('../utils/e2e-helper');
const { execSync } = require('child_process');

test.describe('Suku Edit Tests', () => {
  let sukuId = 1;

  test.beforeAll(() => {
    try {
      const output = execSync(`php artisan tinker --execute="$region = \\App\\Models\\Region::firstOrCreate(['region_code' => '99'], ['region_name' => 'Provinsi Dummy', 'parent_code' => 0]); $adat = \\App\\Models\\WilayahAdat::firstOrCreate(['name' => 'Adat E2E Test', 'tbl_region_id' => $region->id]); $suku = \\App\\Models\\Suku::firstOrCreate(['name' => 'Suku E2E Test', 'tbl_region_id' => $region->id, 'adat_id' => $adat->id]); echo 'SUKUID:' . $suku->id;"`);
      const outputStr = output.toString();
      console.log('Tinker output:', outputStr);
      const match = outputStr.match(/SUKUID:(\d+)/);
      if (match) sukuId = match[1];
    } catch (e) {
      console.error('Failed to seed Suku data:', e.message);
      if (e.stdout) console.error('Stdout:', e.stdout.toString());
      if (e.stderr) console.error('Stderr:', e.stderr.toString());
    }
  });

  test.beforeEach(async ({ page }) => {
    page.setDefaultTimeout(30000);
    page.setDefaultNavigationTimeout(30000);
    E2ETestHelper.setupPageLogging(page);

    // Intercept all requests for debugging and mocking
    await page.route('**', route => {
      const url = route.request().url();
      
      if (url.includes('list_wilayah')) {
        console.log('[MOCK] Intercepted list_wilayah:', url);
        route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            results: [
              { id: 1, kode_prov: '11', nama_prov: 'ACEH' },
              { id: 2, kode_prov: '12', nama_prov: 'SUMATERA UTARA' }
            ],
            pagination: { more: false }
          })
        });
      } else if (url.includes('adat')) {
        console.log('[MOCK] Intercepted adat:', url);
        route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify(url.match(/adat\/\d+/) ? { id: 1, name: 'Adat E2E Test' } : {
            results: [
              { id: 1, name: 'Adat E2E Test' }
            ],
            pagination: { more: false }
          })
        });
      } else {
        route.continue();
      }
    });
  });

  test('should allow selecting province on suku edit page', async ({ page }) => {
    // Navigate directly to the suku edit page that we just created
    await page.goto(`/suku/${sukuId}/edit`);
    await E2ETestHelper.waitForPageReady(page);

    // Verify kita berada di halaman edit
    const currentUrl = await E2ETestHelper.getCurrentUrl(page);
    expect(currentUrl).toMatch(/\/suku\/\d+\/edit/);

    // 3. Buka dropdown provinsi (Select2)
    await page.locator('span[aria-labelledby="select2-list_provinsi-container"]').click();

    // 4. Pastikan input pencarian Select2 muncul
    const searchInput = page.locator('.select2-search__field');
    await expect(searchInput).toBeVisible();

    // 5. Mulai ketik pencarian untuk trigger AJAX request
    await searchInput.pressSequentially('aceh', { delay: 100 });

    // 6. Tunggu hasil pencarian dari mock muncul (jangan strict pake role karena Select2 bisa beda versi)
    const options = page.locator('.select2-results__option').filter({ hasText: /ACEH/i });
    
    // Tunggu opsi ACEH muncul
    await expect(options.first()).toBeVisible({ timeout: 10000 });
    
    const firstOptionText = await options.first().textContent();
    expect(firstOptionText).toContain('ACEH');

    // 7. Pilih opsi tersebut
    await options.first().click();

    // 8. Verifikasi bahwa dropdown sekarang menampilkan teks yang dipilih
    const selectedText = await page.locator('#select2-list_provinsi-container').textContent();
    expect(selectedText).toContain('ACEH');

    // 9. Pastikan value pada select asli juga ikut berubah dan valid (kode_prov)
    const selectValue = await page.locator('select#list_provinsi').inputValue();
    expect(selectValue).toBe('11'); // Sesuai dengan kode_prov dari data mock kita
  });
});
