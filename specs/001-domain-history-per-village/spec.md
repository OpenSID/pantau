# Feature Specification: Domain History per Village

**Feature Branch**: `001-domain-history-per-village`  
**Created**: 2026-10-22  
**Status**: Draft  
**Input**: User description: "Ringkasan singkat: Buat fitur pencatatan riwayat domain per desa dan ubah pendataan supaya pencocokan desa berdasarkan kode_desa (kode desa adalah kunci otoritatif). Jika sebuah desa ganti domain, domain lama disimpan di tabel riwayat dan hanya 1 domain 'aktif/terkini' yang ditampilkan untuk setiap desa — tidak ada duplikasi entri desa hanya karena domain berbeda."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Track Domain Changes Over Time (Priority: P1)

As an administrator, I need to track all domains that have been used by a village over time so I can see the history when domains change.

**Why this priority**: Core functionality - without domain history, we cannot meet the requirement of storing previous domains when they change.

**Independent Test**: Can be fully tested by simulating a crawler sending a domain for a village with known kode_desa, then sending a different domain later for the same kode_desa and verifying that both appear in history with only the latest marked as active.

**Acceptance Scenarios**:

1. **Given** a village exists with kode_desa and current domain, **When** a new domain is detected for the same kode_desa, **Then** the old domain is saved to history with is_active=false and the new domain becomes the active/current domain
2. **Given** domain history exists for a village, **When** viewing the village's domain history, **Then** all historical domains are shown with timestamps, source, and active status in chronological order

---

### User Story 2 - Prevent Duplicate Village Entries Due to Domain Changes (Priority: P1)

As a system, I need to match villages by kode_desa as the authoritative key so that when a domain changes, we don't create duplicate village entries - we just update the domain and record history.

**Why this priority**: Prevents data duplication which is the core problem described.

**Independent Test**: Can be tested by simulating crawler data where a village sends a new domain but same kode_desa, verifying only 1 village record exists and history is updated.

**Acceptance Scenarios**:

1. **Given** a village record exists with kode_desa X, **When** crawler/import sends data for kode_desa X with a different domain, **Then** no new village record is created, the existing village's domain is updated, and the old domain is moved to history
2. **Given** multiple potential matches by domain, **When** kode_desa is provided, **Then** matching is done by kode_desa regardless of domain

---

### User Story 3 - View and Manage Domain History (Priority: P2)

As an administrator, I need to view domain history for each village and have the ability to manually manage it when needed.

**Why this priority**: Provides visibility and admin control over the history.

**Independent Test**: Can be tested by accessing a village's domain history page/modal and verifying all historical domains are displayed with correct information.

**Acceptance Scenarios**:

1. **Given** I am an authenticated admin viewing a village list, **When** I click the domain history action for a village, **Then** I see all domains ever used by that village with dates and sources
2. **Given** I am an admin viewing domain history, **When** I manually add a new domain record, **Then** the system properly normalizes it, updates the active status, and records who made the change

---

### Edge Cases

- **Domain without kode_desa**: When domain comes in without kode_desa, system attempts to find village by domain_normalized; if found but kode_desa differs, it logs potential inconsistency for admin review without creating duplicate village
- **Kode_desa change**: If kode_desa changes in another system, manual mapping/merge is required (out of scope)
- **Multiple kode_desa using same domain** (redirects): History is stored with desa_id pointing to relevant village based on kode_desa; ambiguity noted in notes
- **Expired/deleted domain**: Historical records remain even if domain is no longer active
- **Race conditions**: Concurrent updates to domain must be handled in transactions to maintain only one active domain per village

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST create a `domain_histories` table to store all domain changes per village with full audit information
- **FR-002**: System MUST use `kode_desa` as the authoritative key for matching villages during data import/crawling
- **FR-003**: When a village's domain changes, System MUST move the old domain to history (set is_active=false) and set the new domain as active (is_active=true) in a single transaction
- **FR-004**: System MUST display only the current/active domain in the village list (no duplicate villages due to domain changes)
- **FR-005**: System MUST normalize domains using the existing `fixDomainName()` helper before storing/comparing
- **FR-006**: System MUST provide UI to view complete domain history for each village with domain, obtained_at, source, and is_active status
- **FR-007**: System MUST allow administrators to manually add domain records and change which domain is active
- **FR-008**: System MUST prevent creating duplicate village records when only the domain changes for the same kode_desa
- **FR-009**: System MUST handle domains received without kode_desa by attempting lookup via normalized domain
- **FR-010**: System MUST migrate existing village domains to the history table preserving current active domain
- **FR-011**: System MUST ensure only one domain is marked as active per village at any time (enforced via application logic in transactions)
- **FR-012**: System MUST store audit fields including source (crawler/manual/import), obtained_at, created_by, and notes

### Key Entities *(include if feature involves data)*

- **DomainHistory**: Represents a historical record of a domain used by a village. Key attributes: desa_id (nullable FK), kode_desa (authoritative key), domain, domain_normalized, is_active, source, obtained_at, note, created_by. Belongs to Desa.
- **Desa (Village)**: Existing entity. Must be linked to domain histories and maintain current domain for quick display. Will have domain field updated to reflect active domain.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: When a domain changes for the same kode_desa, no new village record is created - only the domain is updated and history recorded (0 duplicate villages created per domain change)
- **SC-002**: For every village, exactly one domain history record has is_active=true at any time (100% consistency)
- **SC-003**: Administrators can view complete domain history for any village within 2 clicks from the village list
- **SC-004**: All existing village domains are successfully migrated to history table with correct active status (100% migration success)
- **SC-005**: Domain matching during import/crawler prioritizes kode_desa over domain with 100% accuracy when both are present

## Assumptions

- The existing `fixDomainName()` helper is sufficient for domain normalization
- `kode_desa` is the authoritative identifier and won't change without manual intervention
- Database is MySQL (as noted in constraints - no partial unique index possible, must enforce via application)
- Admin users have appropriate permissions to view/modify domain history
- Existing data may have duplicate village entries due to domain changes that need consolidation during migration
