# Quickstart: Domain History per Village

## Overview
This feature adds domain history tracking to prevent duplicate village entries when domains change. Villages are matched by `kode_desa` (authoritative key), and domain changes are tracked in `domain_histories` table.

## Key Changes

### Database
1. New table `domain_histories` with fields for tracking all domain changes
2. Desa model gets relation to domain histories
3. Migration to populate history from existing desa.domain data

### Backend Logic
1. `DomainHistory` model with belongsTo Desa relation
2. `DomainHistoryService` to handle domain change logic atomically
3. Update import/crawler/tracking logic to match by kode_desa first
4. API endpoints for viewing/managing domain history

### Frontend
1. Add "Domain saat ini" column to Desa listing
2. Add "Riwayat Domain" action to view history
3. Modal/page to display domain history

## Usage

### For tracking/import systems
When receiving domain data:
1. Normalize domain with `fixDomainName()`
2. Look up Desa by `kode_desa` (primary key)
3. If found and domain differs, use DomainHistoryService to record change
4. If not found by kode_desa but found by domain, handle as per rules (log for review)

### For admins
- View domain history: Click history icon in village list
- Manually add domain: Use "Catat domain baru" button in history view
- Force set active: Use "Set Active" button (admin only)

## Migration Steps
1. Run migration to create `domain_histories` table
2. Run data migration script to populate from existing desa records
3. Consolidate duplicate desa entries by kode_desa if any exist
4. Verify sync between desa.domain and active history
