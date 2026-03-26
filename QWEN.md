# QWEN Code Guide for TracksID (Laravel)

Date: Minggu, 22 Februari 2026
Project Root: /data/docker/opendesa/tracksid

## Project Overview

TracksID (formerly PantauSID) is a Laravel 10 monitoring application in the OpenSID ecosystem. It monitors and integrates with OpenKab, OpenDK, OpenSID, LayananDesa, and KelolaDesa applications across Indonesia.

**Key Technologies:**
- **Backend:** PHP 8.1+, Laravel Framework ^10.48
- **UI/Theme:** AdminLTE (`jeroennoten/laravel-adminlte`)
- **Authentication:** Laravel Sanctum
- **Database:** MySQL/MariaDB
- **Testing:** PHPUnit (Unit/Feature), Playwright (E2E)
- **Utilities:** 
  - Spatie Laravel Backup & Permission
  - Maatwebsite Excel & PhpSpreadsheet
  - Yajra DataTables
  - Laravel Telescope (debugging)
  - Telegram notifications

**Project Structure:**
- `app/` – Application code (Controllers, Models, Helpers, Exports, Imports, Notifications)
- `routes/` – Route definitions (web.php, api.php, apiv1.php, console.php, channels.php)
- `resources/` – Views and assets
- `public/` – Web root
- `database/` – Migrations, seeders, factories
- `tests/` – PHPUnit tests + Playwright E2E tests
- `helpers/` – Custom helper functions

## Setup and Running

### Prerequisites
- PHP 8.1+ and Composer
- Node.js (v16+) and npm
- MySQL/MariaDB

### Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure .env (database, mail, etc.)
# Key settings:
# - APP_TIMEZONE=Asia/Jakarta
# - APP_LOCALE=id
# - DB_CONNECTION=mysql
# - DB_DATABASE=pantau
```

### Install Dependencies
```bash
composer install
npm install
```

### Database Setup
```bash
# Development (fresh migration with seeds)
php artisan migrate:fresh --seed

# Production (migrate only)
php artisan migrate --seed
```

### Start Application
```bash
php artisan serve --port=8000
```
Access at: http://localhost:8000

### Frontend Build (Laravel Mix)
```bash
# Development build
npx mix

# Or with npm scripts (if configured)
npm run dev
npm run prod
```

## Testing

### PHPUnit (Unit/Feature Tests)
Configuration: `phpunit.xml`

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite=Feature
./vendor/bin/phpunit --testsuite=Unit

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage
```

**Test Environment (from phpunit.xml):**
- `APP_ENV=testing`
- `CACHE_DRIVER=array`
- `MAIL_MAILER=array`
- `QUEUE_CONNECTION=sync`
- `SESSION_DRIVER=array`
- `TELESCOPE_ENABLED=false`

### Playwright (E2E Tests)
Configuration: `playwright.config.js`

```bash
# Run all E2E tests
npm run test:e2e

# Run with browser UI (headed mode)
npm run test:e2e:headed

# Run with Playwright UI
npm run test:e2e:ui

# Debug mode
npm run test:e2e:debug

# View HTML report
npm run test:e2e:report
```

**E2E Configuration:**
- Create `.env.e2e` from `.env.e2e.example`
- Default credentials (from seeders):
  - Email: `eddie.ridwan@gmail.com`
  - Password: `Admin100%`
- Authentication state saved to: `test-results/storage-state/auth.json`
- Test reports: `playwright-report/`, `test-results/results.json`

**E2E Test Structure:**
- `tests/e2e/setup.spec.js` – Environment setup
- `tests/e2e/auth.spec.js` – Authentication tests
- `tests/e2e/login.spec.js` – Login page tests
- `tests/e2e/dashboard.spec.js` – Dashboard tests
- `tests/e2e/auth-flow.spec.js` – Authentication flow tests

## Development Conventions

### Code Style (PHP-CS-Fixer)
Configuration: `.php-cs-fixer.php`

Key rules:
- Short array syntax
- Single quotes for strings
- PSR-12 coding style
- Ordered imports (alphabetical)
- No trailing whitespace
- Blank lines between methods/properties

```bash
# Run PHP-CS-Fixer (if installed)
php-cs-fixer fix

# Or via vendor (if available)
./vendor/bin/php-cs-fixer fix
```

### StyleCI
Configuration: `.styleci.yml`
- Laravel preset
- Disabled: `no_unused_imports`
- Applies to PHP, JS, and CSS files

### Laravel Conventions
- PSR-4 autoloading under `App\` namespace
- Controllers in `app/Http/Controllers/`
- Models in `app/Models/`
- Form Requests for validation
- Custom helpers autoloaded from:
  - `app/Helpers/helper.php`
  - `helpers/general_helper.php`

### Testing Practices
- Unit tests in `tests/Unit/`
- Feature tests in `tests/Feature/`
- E2E tests in `tests/e2e/`
- Use seeders for test data
- E2E tests use shared authentication state
- Screenshots/videos captured on E2E failures

## Key Configuration Files

| File | Purpose |
|------|---------|
| `.env` | Application environment |
| `.env.e2e` | E2E test environment |
| `composer.json` | PHP dependencies |
| `package.json` | Node.js dependencies |
| `phpunit.xml` | PHPUnit configuration |
| `playwright.config.js` | Playwright configuration |
| `webpack.mix.js` | Laravel Mix asset compilation |
| `.php-cs-fixer.php` | PHP code style rules |
| `.styleci.yml` | StyleCI configuration |

## Database Seeders

Located in `database/seeders/`:
- `DatabaseSeeder.php` – Main seeder
- `UserSeeder.php` – Admin users
- `RegionSeeder.php` – Regional data
- `DesaSeeder.php` – Village data
- `GrupSeeder.php` – User groups
- `AksesSeeder.php` – Access permissions
- `NotifikasiSeeder.php` – Notifications

## Useful Commands

```bash
# Environment & Setup
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed

# Development Server
php artisan serve --port=8000

# Testing
./vendor/bin/phpunit
npm run test:e2e
npx playwright test --debug

# Code Style
php-cs-fixer fix

# Cache Management
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Database
php artisan tinker
php artisan db:seed
php artisan migrate:status
```

## Notes

- **Production Warning:** Application detects production environment; use `--force` flag for destructive operations in production
- **E2E Testing:** Requires running Laravel server on port 8000 (auto-started by Playwright config)
- **Mapbox Token:** Optional, configure via `TRACKSID_MAPBOX_TOKEN` in `.env`
- **Backup Path:** `ROOT_BACKUP` configured in `.env` for Spatie Backup package
- **Composer Deprecations:** Some Composer deprecation warnings may appear due to PHP version (cosmetic only)
- **Wilayah Boundaries:** New feature for administrative boundaries (see `WILAYAH_BOUNDARIES_DOCUMENTATION.md`)

## Troubleshooting

### Database Connection
Ensure MySQL is running and credentials in `.env` match your setup:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pantau
DB_USERNAME=root
DB_PASSWORD=rahasia
```

### E2E Tests Failing
1. Check `.env.e2e` configuration
2. Ensure database is seeded: `php artisan migrate:fresh --seed`
3. Verify server is running on configured URL
4. Delete auth state and re-run: `rm -rf test-results/storage-state/`

### Asset Compilation
If frontend assets aren't loading:
```bash
npm install
npx mix
```

### Permission Issues
Ensure proper permissions for storage and cache:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```
