# Data Model: Domain History per Village

## Entities

### DomainHistory
New table to track domain history for each village.

**Table name:** `domain_histories`

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint unsigned | PK, auto_increment | Primary key |
| desa_id | bigint unsigned | FK -> desa.id, nullable, ON DELETE SET NULL | Reference to village (nullable if no direct mapping) |
| kode_desa | varchar(255) | indexed, not null | Authoritative village code for mapping |
| domain | varchar(255) | not null | Domain as recorded |
| domain_normalized | varchar(255) | indexed, not null | Domain after fixDomainName() normalization |
| is_active | boolean | default false | True if this is the current/active domain for the village |
| source | varchar(50) | nullable | Source of record: 'crawler', 'manual', 'import' |
| obtained_at | timestamp | nullable | When domain was first/last detected |
| note | text | nullable | Additional notes (e.g., ambiguity cases) |
| created_by | bigint unsigned | nullable | User ID if created manually |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

**Indexes:**
- Index on `kode_desa`
- Index on `domain_normalized` 
- Index on `desa_id`
- Composite considerations for lookups

**Relationships:**
- `belongsTo(Desa::class)` - optional relationship

### Desa (modifications)
Existing table - may need to ensure/update fields.

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| domain | varchar(255) | nullable | Current active domain (synced with domain_histories.is_active=true) |
| domain_updated_at | timestamp | nullable | When domain was last updated (optional but recommended) |

**Relationships (to add):**
- `hasMany(DomainHistory::class)` - all domain history for this village

## Business Rules

1. **Single active domain per village**: At any time, for a given desa_id (or logical village), only one DomainHistory record can have `is_active = true`. Enforced by application logic in transactions.
2. **kode_desa is authoritative**: Matching must prioritize kode_desa over domain.
3. **Normalization required**: All domains must be normalized with `fixDomainName()` before storage/comparison.
4. **Transaction safety**: When changing domains, deactivating old and activating new must happen in a single transaction.
5. **Orphaned history**: DomainHistory can exist with desa_id NULL (when village not found by kode_desa) for audit purposes.
