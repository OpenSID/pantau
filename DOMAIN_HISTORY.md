# Riwayat Domain Desa (Domain History)

Fitur ini menambah pencatatan **riwayat domain per desa** dan menjadikan `kode_desa`
sebagai kunci otoritatif untuk pencocokan desa pada proses pelacakan (crawler) OpenSID.

Tujuan utama:

- Menghindari duplikasi entri desa ketika sebuah desa mengganti domain.
- Menyimpan seluruh domain yang pernah dipakai beserta waktu pencatatannya.
- Menampilkan hanya satu domain "aktif/terkini" untuk setiap desa.

---

## 1. Perubahan Skema Database

### Tabel baru: `domain_histories`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint unsigned | Primary key |
| `desa_id` | bigint unsigned, nullable | FK ke `desa.id`, `ON DELETE SET NULL` |
| `kode_desa` | varchar, **indexed** | Kode desa (kunci pemetaan otoritatif) |
| `domain` | varchar | Domain yang tercatat (mentah dari sumber) |
| `domain_normalized` | varchar, **indexed** | Hasil `fixDomainName()` |
| `is_active` | boolean, default `false` | `true` bila ini domain terkini |
| `source` | varchar, nullable | `crawler` / `manual` / `import` |
| `obtained_at` | timestamp, nullable | Waktu domain terdeteksi |
| `note` | text, nullable | Catatan admin/ambiguitas |
| `created_by` | bigint unsigned, nullable | User ID untuk pencatatan manual |
| `created_at`, `updated_at` | timestamp | |

Index: `kode_desa`, `domain_normalized`, `desa_id`.

> **Catatan MySQL:** MySQL tidak mendukung *partial index*, sehinggaconstraint
> "hanya satu domain aktif per desa" **dijaga oleh aplikasi** (seluruh operasi
> perubahan `is_active` dijalankan dalam satu transaksi DB). Bila deployment
> memakai PostgreSQL, constraint ini dapat diperkuat dengan
> `UNIQUE (desa_id) WHERE is_active`.

### Tabel `desa` (penambahan kolom)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `domain` | varchar, nullable | Domain aktif saat ini (sinkron dengan `domain_histories.is_active = true`) |
| `domain_updated_at` | timestamp, nullable | Waktu domain terakhir diperbarui |

---

## 2. Alur Bisnis

Ketika domain baru masuk melalui `POST /api/track/desa` (OpenSID):

1. Domain dinormalisasi dengan helper `fixDomainName()` (lowercase, buang
   `http(s)://`, buang `www.`, ambil bagian sebelum `/`).
2. Dicari desa berdasarkan `kode_desa` (**kunci otoritatif**).
3. Jika desa ditemukan:
   - Domain baru **sama** dengan domain aktif → riwayat diperbarui
     (`obtained_at`), tanpa membuat entri baru.
   - Domain baru **berbeda** → dalam **satu transaksi**:
     1. Semua `domain_histories` aktif untuk desa tersebut di-set `is_active = false`.
     2. Entri baru dibuat dengan `is_active = true` dan `obtained_at = now()`.
     3. `desa.domain` dan `desa.domain_updated_at` diperbarui.
4. Jika desa **tidak ditemukan** → riwayat disimpan dengan `desa_id = NULL`
   sehingga dapat ditinjau admin (lihat halaman *domain yang belum terpetakan*).

Domain lokal (`localhost`, `192.168.*`, `127.*`, `10.*`) **tidak** dicatat sebagai
riwayat domain hosting.

---

## 3. Endpoint

| Method | URI | Keterangan |
|--------|-----|------------|
| GET | `/desa/{desa}/domain-history` | Daftar riwayat domain desa (butuh login) |
| GET | `/desa/{desa}/domain-history/download` | Unduh riwayat dalam format CSV |
| GET | `/desa/domain-history-yatim` | Daftar riwayat domain yang belum terpetakan ke desa |
| POST | `/desa/{desa}/domain-history` | Catat domain baru secara manual (admin) |
| PATCH | `/desa/{desa}/domain-history/{history}/set-active` | Paksa domain tertentu menjadi aktif (admin) |

Aksi perubahan (POST/PATCH) menggunakan gate `is-admin` (role **Administrator**).

---

## 4. Antarmuka Pengguna

**Daftar Desa** (halaman OpenSID, tabel *semua desa*):

- Kolom baru **“Domain Saat Ini”** menampilkan `desa.domain`, disertai badge
  jumlah domain bila desa pernah berganti domain.
- Kolom aksi **“Riwayat Domain”** (ikon kronologi) menuju halaman riwayat.
- Filter tambahan (hanya untuk pengguna terautentikasi):
  - **Kode Desa** — pencocokan sebagian (contains).
  - **Domain** — pencocokan sebagian pada domain aktif maupun riwayat.
  - **Jumlah Domain** — desa dengan lebih dari 1 domain (pernah berganti domain).
  - **Riwayat Domain** — memiliki riwayat / tidak memiliki riwayat.

**Halaman Riwayat Domain** (`/desa/{desa}/domain-history`):

- Tabel riwayat: domain, domain normalized, status aktif, source, waktu diperoleh, catatan.
- Tombol **Set Active** untuk memaksa domain lama menjadi domain aktif.
- Tombol **Catat Domain Baru** (modal) untuk pencatatan manual oleh admin.
- Tautan ke daftar domain yang belum terpetakan ke desa.

---

## 5. Migrasi Data

### Backup

```bash
# Backup database terlebih dahulu
php artisan pantau:backup-database-storage   # atau mekanisme backup yang berlaku di server
```

### Migration

```bash
php artisan migrate
```

Migration `2026_10_22_064900_migrate_existing_domains` akan:

1. Membuat baris `domain_histories` untuk setiap `desa` yang memiliki `url_hosting`,
   dengan `is_active = true` dan `obtained_at` dari `domain_updated_at`/`updated_at`.
2. Menyinkronkan `desa.domain` dengan domain aktif pada `domain_histories`.

### Perintah konsolidasi (dengan dry-run)

```bash
# Dry-run: hanya menampilkan rencana, tidak menyimpan perubahan
php artisan pantau:migrasi-domain-history --dry-run --konsolidasi

# Jalankan konsolidasi (menggabungkan desa duplikat berdasarkan kode_desa)
php artisan pantau:migrasi-domain-history --konsolidasi
```

Output perintah menampilkan ringkasan:

```
Ringkasan:
  Desa terdaftar                  : 25963
  Riwayat domain                  : 22268
  Domain aktif                    : 22268
  Riwayat tanpa desa (yatim)      : 0
  Desa dengan domain aktif ganda  : 0
```

> Selalu jalankan `--dry-run` lebih dulu dan pastikan backup database tersedia
> sebelum menjalankan konsolidasi.

---

## 6. Testing

```bash
# Unit test service domain history
./vendor/bin/phpunit --filter DomainHistoryServiceTest

# Feature test controller, filter, dan tracking
./vendor/bin/phpunit --filter DomainHistory
./vendor/bin/phpunit --filter TrackDomainHistoryTest

# E2E (Playwright)
npm run test:e2e -- domain-history.spec.js
```

---

## 7. Out of Scope

- Perubahan domain pada aplikasi lain (OpenDK, OpenKab) — tidak disentuh;
  fitur ini hanya memproses data OpenSID desa.
- Perubahan `kode_desa` oleh sistem eksternal (perlu mapping/merge manual).
- Integrasi pihak ketiga baru.
