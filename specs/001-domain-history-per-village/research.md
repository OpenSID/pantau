# Research: Domain History per Village

**Feature**: Domain History per Village  
**Date**: 2026-10-22

## Research Tasks

### 1. How are domains currently handled in tracking/import?
**Finding**: Domains are sent via API endpoints (TrackOpenkabController, TrackOpendkController, etc.) and processed through Request classes that use `fixDomainName()`. The Desa model doesn't currently have explicit domain fields in its model definition visible from quick scan, but the feature spec says "pastikan ada kolom domain (string, nullable)" - need to look up actual schema.

**Action**: Check Desa table schema and how domain is stored.

### 2. Existing domain normalization
**Finding**: `fixDomainName()` in `app/Helpers/helper.php` normalizes by lowercasing, removing http/https, removing www, taking only the domain part before first slash. This is the standard normalization to use.

**Decision**: Use existing `fixDomainName()` helper.

### 3. How matching is currently done
**Finding**: Looking at tracking controllers, they use `updateOrCreate` with keys like `kode_kab` or similar. For Desa/OpenSID tracking, need to understand the full flow. Also looking at TrackOpenkabRequest, it does validation and merges data.

**Need to investigate**: OpenSID/Desa tracking mechanism - might be in TrackDesaController or similar. Let us look up how Desa data comes in.

### 4. Database constraints and transactions
**Finding**: MySQL is used (common in this Laravel app). No partial unique indexes in MySQL - must enforce "only one active domain per village" via application logic in transactions as stated in requirements.

**Decision**: Use DB transactions with proper locking/select for update when changing active domain.

### 5. Migration strategy for existing data
**Finding**: Need to migrate existing desa.domain values to domain_histories table. Also need to handle potential duplicates if same kode_desa appears multiple times (though unlikely if kode_desa is unique, but feature says "jika ada duplikat desa yang tercipta karena domain" so need to consolidate).

**Decision**: Follow migration steps from spec - create history table, populate from existing desa records where domain not null, consolidate duplicates by kode_desa preferring latest updated_at, ensure desa.domain syncs with active history.

## Key Decisions

1. **Model structure**: Create `DomainHistory` model with belongsTo Desa. Add hasMany `domainHistories()` to Desa model.
2. **Service layer**: Create `DomainHistoryService` to encapsulate domain change logic with transactions.
3. **Transaction safety**: Use `DB::transaction()` and `lockForUpdate()` when switching active domains.
4. **Normalization**: Always use `fixDomainName()` for domain_normalized.
5. **Matching priority**: Always match by kode_desa first when available; only fallback to domain if kode_desa missing.
6. **Indexing**: Add indexes on kode_desa, domain_normalized, desa_id as specified.
