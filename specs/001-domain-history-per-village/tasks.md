# Tasks: Domain History per Village

**Input**: Design documents from `/specs/001-domain-history-per-village/`
**Prerequisites**: plan.md, spec.md, data-model.md, research.md, quickstart.md

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project setup and preparation

- [x] T001 Review existing codebase patterns for Desa model, tracking controllers, and helper functions
- [x] T002 Create service directory structure if needed

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T003 [P] Create DomainHistory model in app/Models/DomainHistory.php
- [x] T004 [P] Add domainHistories() relationship to Desa model in app/Models/Desa.php
- [x] T005 [P] Create migration for domain_histories table in database/migrations/xxxx_create_domain_histories_table.php
- [x] T006 Create DomainHistoryService for domain change logic in app/Services/DomainHistoryService.php
- [x] T007 Create data migration script for existing desa domains in database/migrations/xxxx_migrate_existing_domains.php

**Checkpoint**: Foundation ready - user story implementation can now begin

---

## Phase 3: User Story 1 - Track Domain Changes Over Time (Priority: P1) 🎯 MVP

**Goal**: Track all domains used by a village over time, storing old domains in history when they change

**Independent Test**: Simulate crawler sending domain for village with kode_desa, then different domain later for same kode_desa - verify both appear in history with only latest marked active

### Implementation for User Story 1

- [x] T008 [P] [US1] Add domain field handling to Desa model if not present (ensure domain and domain_updated_at columns exist or add migration)
- [x] T009 [US1] Implement recordDomainChange method in DomainHistoryService with transaction logic (deactivate old, activate new)
- [x] T010 [US1] Implement getDomainHistory method in DomainHistoryService
- [x] T011 [P] [US1] Create DomainHistoryController for viewing history in app/Http/Controllers/DomainHistoryController.php
- [x] T012 [US1] Add routes for viewing domain history in routes/web.php
- [x] T013 [P] [US1] Create view for domain history listing in resources/views/desa/domain-history/index.blade.php

**Checkpoint**: User Story 1 should be fully functional - can track and view domain history

---

## Phase 4: User Story 2 - Prevent Duplicate Village Entries Due to Domain Changes (Priority: P1)

**Goal**: Match villages by kode_desa as authoritative key to prevent duplicate entries when domain changes

**Independent Test**: Send crawler data with same kode_desa but different domain - verify no new village created, existing village updated with history

### Implementation for User Story 2

- [x] T014 [US2] Update tracking/import logic to prioritize matching by kode_desa over domain
- [x] T015 [US2] Modify relevant API controllers/requests to use DomainHistoryService when domain changes
- [x] T016 [US2] Add logic to handle domain without kode_desa (fallback to domain lookup with proper logging)
- [x] T017 [US2] Ensure transactions are used for all domain change operations

**Checkpoint**: User Stories 1 and 2 both work - no duplicates on domain change, history tracked

---

## Phase 5: User Story 3 - View and Manage Domain History (Priority: P2)

**Goal**: Provide UI for viewing and managing domain history with admin controls

**Independent Test**: Access village domain history page, add manual entry, change active domain

### Implementation for User Story 3

- [x] T018 [P] [US3] Add domain history action button to Desa listing in resources/views/desa/index.blade.php
- [x] T019 [P] [US3] Add "Domain saat ini" column to Desa listing
- [x] T020 [US3] Add methods for manual domain management in DomainHistoryController (add, set-active)
- [x] T021 [US3] Add routes for manual management in routes/web.php
- [x] T022 [P] [US3] Create modal/form for adding manual domain entry
- [x] T023 [US3] Implement authorization checks (admin/superadmin only for modifications)
- [x] T024 [US3] Add validation using fixDomainName() for manual entries

**Checkpoint**: All user stories implemented

---

## Phase 6: Data Migration & Consolidation

**Purpose**: Migrate existing data

- [x] T025 Run/create data migration to populate domain_histories from existing desa.domain
- [x] T026 Create script to detect and consolidate duplicate desa entries by kode_desa
- [x] T027 Sync desa.domain with active domain history for all records
- [x] T028 Add dry-run capability to migration scripts

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [x] T029 [P] Add indexes to domain_histories table (kode_desa, domain_normalized, desa_id)
- [x] T030 [P] Add error handling and logging for domain change operations
- [x] T031 [P] Write unit tests for DomainHistory model and DomainHistoryService
- [x] T032 [P] Write feature tests for domain change scenarios
- [x] T033 Add E2E test scenarios for domain history
- [x] T034 Run code style checks with PHP CS Fixer
- [x] T035 Update documentation/README if needed

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3-5)**: All depend on Foundational phase completion
- **Data Migration (Phase 6)**: Depends on Foundational phase
- **Polish (Phase 7)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational - No dependencies on other stories
- **User Story 2 (P1)**: Can start after Foundational - Integrates with tracking logic, builds on US1 concepts
- **User Story 3 (P2)**: Can start after Foundational - UI for US1/US2 functionality

### Parallel Opportunities

- T003, T004, T005 can run in parallel (different files)
- T008, T011, T013 can run in parallel within US1
- T018, T019, T022 can run in parallel within US3
- T029, T030, T031, T032 can run in parallel

---

## Implementation Strategy

### MVP First (User Story 1 + 2)
1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 1
4. Complete Phase 4: User Story 2
5. **STOP and VALIDATE**: Test domain change tracking without duplicates
6. Deploy/demo if ready

### Incremental Delivery
1. Foundation ready → Models, migrations, service
2. Add User Story 1 → Can track history
3. Add User Story 2 → Prevents duplicates
4. Add User Story 3 → Full admin UI
5. Each story adds value without breaking previous stories

---

## Implementation Notes

### File yang dibuat / diubah

**Baru:**
- `app/Models/DomainHistory.php` — model Eloquent dengan relasi `belongsTo(Desa)`.
- `app/Services/DomainHistoryService.php` — logika bisnis domain (transaksi, deaktivasi domain lama, sinkronisasi `desa.domain`).
- `app/Http/Controllers/DomainHistoryController.php` — halaman riwayat, CSV, catat manual, set active, daftar domain yatim.
- `app/Console/Commands/MigrasiDomainHistoryCommand.php` — `pantau:migrasi-domain-history` dengan `--dry-run` dan `--konsolidasi`.
- `database/migrations/2026_10_22_064738_create_domain_histories_table.php`
- `database/migrations/2026_10_22_064800_add_domain_to_desa_table.php` (idempoten via `Schema::hasColumn`)
- `database/migrations/2026_10_22_064900_migrate_existing_domains.php`
- `resources/views/desa/domain-history/index.blade.php`
- `resources/views/desa/domain-history/orphan.blade.php`
- `tests/Unit/Services/DomainHistoryServiceTest.php`
- `tests/Feature/Api/TrackDomainHistoryTest.php`
- `tests/Feature/DomainHistoryControllerTest.php`
- `tests/Feature/DomainHistoryFilterTest.php`
- `tests/e2e/domain-history.spec.js`
- `DOMAIN_HISTORY.md`

**Diubah:**
- `app/Models/Desa.php` — relasi `domainHistories()`, `currentDomain()`, kolom domain pada `scopeSemuaDesaAuth`, filter `kode_desa` / `domain` / `jumlah_domain` / `punya_riwayat` pada `scopeFillter`.
- `app/Http/Controllers/Api/TrackController.php` — pencatatan riwayat domain untuk domain hosting.
- `app/Http/Controllers/DashboardController.php` — meneruskan filter domain baru ke scope.
- `resources/views/website/partial/tabel_opensid_detail.blade.php` — kolom "Domain Saat Ini", aksi "Riwayat Domain", filter domain.
- `routes/web.php` — 5 route riwayat domain.

### Catatan penyimpangan dari rencana awal

- **OpenDK / OpenKab tidak disentuh.** `TrackOpendkController` dan `TrackOpenkabController` dikembalikan ke kondisi awal; hanya `TrackController` (OpenSID desa) yang memakai `DomainHistoryService`, sesuai konfirmasi pengguna.
- **Authorization memakai gate `is-admin`, bukan middleware `role:Administrator`.** Project ini tidak memiliki alias middleware `role`; gate `is-admin` sudah tersedia di `AppServiceProvider`.
- **Migration data ditulis dengan SQL bulk** (`INSERT ... SELECT`) karena iterasi per-row pada ±22.000 desa melewati batas waktu eksekusi.
- **Halaman riwayat berada di layout `layouts.index`** dengan view `desa.domain-history.*`, bukan di area admin `admin.wilayah.desa` (area tersebut mengelola tabel `Region`, bukan tabel `desa`).
- **Kolom domain hanya tampil untuk pengguna terautentikasi** agar tidak membocorkan data domain ke pengunjung publik; scope `semuaDesaPublik` tetap tidak menyertakan kolom domain.

### Hasil verifikasi

- Migration berhasil dijalankan pada data existing: 22.268 baris `domain_histories` terb migration, 0 desa dengan domain aktif ganda, 0 riwayat yatim.
- `./vendor/bin/phpunit` → **226 tests, 8894 assertions, OK**.
- `php-cs-fixer --dry-run` → tidak ada pelanggaran baru pada file yang dibuat/diubah (13 file tersisa adalah pelanggaran lama sebelum branch ini dibuat).
