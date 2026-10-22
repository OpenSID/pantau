# Implementation Plan: Domain History per Village

**Branch**: `001-domain-history-per-village` | **Date**: 2026-10-22 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/001-domain-history-per-village/spec.md`

## Summary

This feature implements domain history tracking for villages (desa) in the OpenSID tracking system. Currently, when a village's domain changes, it can cause duplication issues or lose history. The solution stores all domain changes in a new `domain_histories` table with `kode_desa` as the authoritative key for matching. Only one domain is marked as active per village at a time, and the old domain is moved to history when changed. All import/crawler processes must match by `kode_desa` first to prevent creating duplicate village entries due to domain changes.

## Technical Context

**Language/Version**: PHP 8.1+, Laravel 9.x  
**Primary Dependencies**: Laravel Framework, Eloquent ORM, DataTables, jQuery/Bootstrap (existing frontend stack)  
**Storage**: MySQL/MariaDB (database as per existing setup)  
**Testing**: PHPUnit/Pest, Playwright (E2E tests already configured)  
**Target Platform**: Web application (Linux server)  
**Project Type**: Web application (Laravel backend + Blade views)  
**Performance Goals**: Handle bulk imports/crawls efficiently with proper indexing on kode_desa and domain_normalized  
**Constraints**: MySQL - cannot use partial unique indexes for "is_active true" constraint, must enforce via application logic in transactions. Must use existing helper `fixDomainName()` for normalization. Must follow existing code style (PSR-2, PHP CS Fixer Laravel preset).  
**Scale/Scope**: Affects Desa model, tracking/import logic, admin UI, API endpoints. Migration needed for existing data.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

Based on project structure and existing patterns, no constitutional violations. The implementation follows existing Laravel patterns in the codebase.

## Project Structure

### Documentation (this feature)

```text
specs/001-domain-history-per-village/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output (created by /speckit.tasks)
```

### Source Code (repository root)

```text
app/
├── Models/
│   ├── Desa.php                    # Add domainHistories() relation, modify matching logic
│   └── DomainHistory.php           # New model
├── Http/
│   ├── Controllers/
│   │   ├── Api/                    # May need updates for track endpoints
│   │   └── Admin/DesaDomainHistoryController.php # New controller
│   └── Requests/                   # Validation for manual entries
├── Services/
│   └── DomainHistoryService.php   # New service for domain change logic
└── Helpers/
    └── helper.php                 # Uses existing fixDomainName()

database/
├── migrations/
│   ├── xxxx_create_domain_histories_table.php  # New migration
│   └── xxxx_migrate_existing_domains.php       # Data migration script
└── seeders/                        # May need updates

resources/
└── views/
    ├── desa/                      # Desa listing - add domain column and history action
    └── desa/domain-history/       # New views for history

routes/
├── web.php                        # Add routes for domain history
└── api.php                        # May need API routes

tests/
├── Feature/
│   ├── DomainHistoryTest.php      # New tests
│   └── DesaDomainMatchingTest.php # New tests
└── Unit/
    └── DomainHistoryServiceTest.php
```

**Structure Decision**: Laravel web application structure following existing patterns. New model, migration, service class for business logic, controller for UI/API, and views for history management.
