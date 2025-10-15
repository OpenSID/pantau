# E2E Testing dengan Playwright

## Overview

Sistem e2e testing ini menggunakan Playwright dengan authentication state yang dibagikan antar test. Ini memungkinkan login hanya sekali di awal, kemudian semua test menggunakan session yang sama.

## Struktur Testing

### 1. Setup Authentication (setup.spec.js)

-   Melakukan login sekali di awal
-   Menyimpan authentication state ke file
-   Dijalankan sebelum semua test lainnya

### 2. Login Tests (login.spec.js)

-   Test halaman login tanpa authentication state
-   Validasi form login
-   Test credential yang salah
-   Test validasi required fields

### 3. Dashboard Tests (dashboard.spec.js)

-   Test halaman dashboard dengan authentication state
-   Navigasi dashboard
-   Responsive layout
-   Session maintenance

### 4. Authentication Flow Tests (auth-flow.spec.js)

-   Test maintenance authentication di berbagai halaman
-   Test akses protected resources
-   Test logout functionality

## Konfigurasi

### File Konfigurasi Utama:

-   `playwright.config.js` - Konfigurasi Playwright
-   `tests/e2e-config-loader.js` - Loader konfigurasi
-   `.env.e2e` - Environment variables untuk testing

### Authentication State:

Authentication state disimpan di: `test-results/storage-state/auth.json`

## Menjalankan Test

### Semua Test:

```bash
./run-e2e-tests.sh
```

### Atau manual:

```bash
npx playwright test
```

### Test Specific File:

```bash
npx playwright test tests/e2e/login.spec.js
```

### Debug Mode:

```bash
npx playwright test --debug
```

### Headed Mode:

```bash
npx playwright test --headed
```

## Kredensial Default

Default credentials berdasarkan seeder:

-   Email: `eddie.ridwan@gmail.com`
-   Password: `password`

Bisa diubah di file `.env.e2e`:

```
E2E_ADMIN_EMAIL=eddie.ridwan@gmail.com
E2E_ADMIN_PASSWORD=Admin100%
```

## Fitur Testing

### 1. Single Login

-   Login hanya dilakukan sekali di setup
-   Authentication state dibagikan ke semua test
-   Tidak perlu login berulang-ulang

### 2. Page Object Pattern

-   Helper class di `tests/utils/e2e-helper.js`
-   Reusable methods untuk common actions
-   Centralized error handling

### 3. Robust Selectors

-   Multiple selector strategies
-   Fallback options untuk elemen yang berbeda
-   Compatible dengan AdminLTE theme

### 4. Error Handling

-   Automatic screenshot on failure
-   Console error logging
-   Request failure tracking

## Troubleshooting

### Test Gagal Login:

1. Pastikan server Laravel berjalan di `http://localhost:8000`
2. Cek kredensial di `.env.e2e`
3. Pastikan user ada di database

### Authentication State Issue:

1. Hapus `test-results/storage-state/auth.json`
2. Jalankan ulang test

### Timeout Issues:

1. Increase timeout di `playwright.config.js`
2. Check network connectivity
3. Pastikan server response time wajar

## Reports

### View HTML Report:

```bash
npx playwright show-report
```

### JSON Report:

`test-results/results.json`

## Best Practices

1. **Single Responsibility**: Setiap test file fokus pada satu area
2. **Shared State**: Gunakan authentication state untuk efisiensi
3. **Robust Selectors**: Gunakan multiple selector strategies
4. **Error Handling**: Screenshot dan logging untuk debugging
5. **Clean Up**: Reset state jika diperlukan antar test
