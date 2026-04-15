# AGENTS.md

This file provides guidance to agents when working with code in this repository.

## Commands

**Testing:**
- Run PHPUnit tests: `php artisan test` or `./vendor/bin/phpunit`
- Run single test: `php artisan test --filter TestName`
- Run E2E tests: `npm run test:e2e` (auto-starts server and runs migrations)
- Run E2E with UI: `npm run test:e2e:ui`
- Run E2E in debug mode: `npm run test:e2e:debug`

**Code Quality:**
- Fix PHP code style: `vendor/bin/php-cs-fixer fix`
- Check PHP code style: `vendor/bin/php-cs-fixer fix --dry-run`

**Development:**
- Start server: `php artisan serve`
- Compile assets: `npm run dev` (development) or `npm run prod` (production)

## Critical Patterns

**Security - GitHub API (MANDATORY):**
- All GitHub API calls MUST use `lastrelease()` helper which validates URLs via `is_trusted_github_api_url()`
- Only these endpoints are allowed: `/repos/OpenSID/rilis-premium/releases/latest`, `/repos/OpenSID/rilis-pbb/releases/latest`, `/repos/OpenSID/opendk/releases/latest`, `/repos/OpenSID/rilis-opensid-api/releases/latest`
- Never make direct HTTP requests to GitHub without this validation (SSRF protection)

**Custom Helpers (Auto-loaded):**
- `app/Helpers/helper.php` - Core helpers (version checking, domain validation, backup functions)
- `helpers/general_helper.php` - General helpers (date formatting, image handling, number formatting)
- Both are auto-loaded via composer.json, no manual imports needed

**Database & Models:**
- Desa model uses complex scopes: `jumlahDesa()`, `desaValid()`, `filterWilayah()`, `hostingOnline()`, `hostingOffline()`, `aktif()`
- Region filtering via `FilterWilayahTrait` and `HasRegionAccess` trait
- Version checking uses cached GitHub API calls with fallback defaults
- TEMA_PRO themes: ['Silir', 'Batuah', 'Pusako', 'DeNava', 'Lestari']

**E2E Testing:**
- Configuration loaded from `.env.e2e` file (not `.env`)
- Test credentials: `eddie.ridwan@gmail.com` / `Admin100%` (from UserSeeder)
- Authentication state saved in `test-results/storage-state/auth.json`
- Playwright auto-starts Laravel server via webServer config
- Global setup runs `php artisan migrate:fresh --seed` automatically

**Code Style:**
- PHP CS Fixer configured with Laravel preset (`.php-cs-fixer.php`)
- Single quotes for strings, short array syntax
- Ordered imports alphabetically
- No trailing whitespace, specific brace positioning
- StyleCI config (`.styleci.yml`) overrides: disables `no_unused_imports` rule

**Gotchas:**
- `sudahInstal()` helper checks for `storage_path('installed')` file existence
- Domain validation uses `fixDomainName()` to normalize URLs
- Local IP detection via `is_local()` helper (localhost, 192.168, 127.0, 10.x)
- Backup functions check for `/usr/bin/rclone` existence for cloud storage sync
- Log permissions can be changed via `changeLogPermissions()` helper

## Security & Bug Review Focus

**Celah Keamanan yang Perlu Diperiksa:**
- SQL injection pada query builder raw SQL
- XSS pada output user input
- CSRF token validation
- Command injection pada exec() calls
- Path traversal pada file operations
- Authentication bypass pada login
- Authorization bypass pada Admin Wilayah
- Hardcoded secrets di config files
- SSRF pada GitHub API calls (cek `is_trusted_github_api_url()`)

**Bug Logika yang Sering Terjadi:**
- Race condition pada backup operations
- Null pointer pada helper functions
- Memory leak pada large dataset queries
- Infinite loop pada recursive functions
- Error handling yang hilang pada HTTP requests
- Edge case pada date/time calculations
