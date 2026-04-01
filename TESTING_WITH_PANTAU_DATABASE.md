# Testing dengan Database Pantau

## Overview

Project ini menggunakan database `pantau` yang sudah ada untuk testing, bukan database testing yang kosong. Ini memastikan bahwa test memiliki akses ke data region/desa yang sudah ada.

## Konfigurasi

### File Konfigurasi

- `phpunit.testing.xml` - Konfigurasi PHPUnit yang menggunakan database `pantau`
- Menggunakan trait `DatabaseTransactions` untuk rollback otomatis setelah setiap test

### Database

Test menggunakan database `pantau` yang sama dengan development/production. Namun, karena menggunakan `DatabaseTransactions`, semua perubahan yang dibuat selama test akan di-rollback otomatis.

## Menjalankan Test

### Semua Test
```bash
composer test
# atau
php artisan test --configuration=phpunit.testing.xml
```

### Test Spesifik
```bash
composer test:filter TrackControllerTest
# atau
php artisan test --configuration=phpunit.testing.xml --filter TrackControllerTest
```

### Test dengan Class/Method Name
```bash
php artisan test --configuration=phpunit.testing.xml --filter can_update_existing_desa_data
```

## Pola Test

### Menggunakan Data Desa yang Sudah Ada

```php
/** @test */
public function can_update_existing_desa_data()
{
    // Ambil desa yang sudah ada dari tabel region
    $desa = Desa::whereNotNull('kode_desa')->first();
    $this->assertNotNull($desa, 'No desa found in region table');

    $requestData = [
        'nama_desa' => $desa->nama_desa,
        'kode_desa' => $desa->kode_desa,
        // ... data lainnya
    ];

    $response = $this->postJsonWithToken('/api/track/desa', $requestData);
    // ...
}
```

### Menggunakan Data Random dari Tabel

```php
/** @test */
public function can_track_openkab_data_successfully()
{
    $kodeWilayah = Wilayah::inRandomOrder()->first();
    $requestData = [
        'kode_kab' => $kodeWilayah->kode_kab,
        // ... data lainnya
    ];
    // ...
}
```

## Penting!

1. **Jangan gunakan `RefreshDatabase`** - Trait ini akan menghapus semua data dan menjalankan migration ulang, yang akan menghapus data region/desa.

2. **Gunakan `DatabaseTransactions`** - Trait ini menjalankan test dalam transaksi dan rollback setelah selesai, sehingga data production tidak berubah.

3. **Pastikan database `pantau` sudah di-seed** - Test memerlukan data dari tabel `region` dan `desa` yang sudah ada.

4. **Hindari test yang bergantung pada state** - Karena menggunakan database yang sama, pastikan test tidak bergantung pada state dari test sebelumnya.

## Troubleshooting

### Error: "No desa found in region table"
Pastikan database `pantau` sudah memiliki data dari `region.sql` dan `desa.sql`.

### Error: Database connection failed
Periksa kredensial database di `phpunit.testing.xml` sesuai dengan setup lokal Anda.

### Test gagal karena data berubah
Pastikan test menggunakan `DatabaseTransactions` untuk rollback otomatis.
