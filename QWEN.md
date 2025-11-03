# QWEN Code Guide for TracksID (Laravel)

Date (locale): Rabu, 15 Oktober 2025
OS: linux
Project Root: /data/docker/opendesa/tracksid

## Project Overview

This repository contains a Laravel 10 (PHP 8.1+) web application in the OpenSID ecosystem. The README references PantauSID (a monitoring app integrated with OpenKab/OpenDK/OpenSID/LayananDesa/KelolaDesa). The directory here is `tracksid`, and E2E docs refer to “TracksID”. Treat this as a Laravel-based monitoring/admin application.

Key technologies and tooling:
- Backend: PHP 8.1+, Laravel Framework ^10.48
- UI/Theme: AdminLTE (`jeroennoten/laravel-adminlte`)
- Auth/Session: Laravel Sanctum
- Utilities: Telescope (optional), Spatie packages (backup, permission), Excel (PhpSpreadsheet & Maatwebsite), Yajra DataTables
- Testing: PHPUnit for unit/feature tests, Playwright for E2E
- Styling: PHP-CS-Fixer (local config present); StyleCI config exists
- Build: `webpack.mix.js` present (Laravel Mix); package.json currently focuses on Playwright only
- Database: MySQL/MariaDB (default examples use MySQL)

Autoload & helpers:
- PSR-4 autoloading for `App\`, `Database\Factories\`, `Database\Seeders\`
- File autoloads: `app/Helpers/helper.php`, `helpers/general_helper.php`

Repo layout highlights:
- app/, bootstrap/, config/, database/, lang/, public/, resources/, routes/
- tests/ (PHPUnit), tests/e2e (Playwright), playwright-report/, test-results/
- composer.json, phpunit.xml, playwright.config.js, webpack.mix.js

## Setup, Building, and Running

### Prerequisites
- PHP 8.1+ and Composer
- Node.js (v16+) and npm
- MySQL/MariaDB

### Environment
- Copy env: `cp .env.example .env`
- Generate app key: `php artisan key:generate`
- Configure `.env` (timezone, locale, database, mail, etc.)
  - Defaults include `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`
  - DB section: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
  - Optional: `ROOT_BACKUP`, `TRACKSID_MAPBOX_TOKEN` (commented in example)

### Install dependencies
```
composer install
npm install
```

### Database & seeds
- Development (fresh):
```
php artisan migrate:fresh --seed
```
- Production:
```
php artisan migrate --seed
```

### Start the application
```
php artisan serve --port=8000
```
- E2E config expects `http://localhost:8000` by default (configurable).
- Alternatively, run under your preferred web server (Nginx/Apache + PHP-FPM).

### Frontend build (Laravel Mix)
- `webpack.mix.js` exists, but `package.json` does not define Mix scripts.
- Typical commands (if Mix and related devDependencies are installed):
```
npx mix
# or
npm run dev
npm run prod
```
- TODO: Confirm and add build scripts/devDependencies for Mix if needed.

## Testing

### PHPUnit (Unit/Feature)
- Config: `phpunit.xml` (bootstrap `vendor/autoload.php`)
- Test suites: `tests/Unit`, `tests/Feature`
- Coverage includes: `app/`
- Recommended command:
```
./vendor/bin/phpunit
```
- Test env variables (from phpunit.xml):
  - `APP_ENV=testing`, `BCRYPT_ROUNDS=4`, `CACHE_DRIVER=array`, `MAIL_MAILER=array`, `QUEUE_CONNECTION=sync`, `SESSION_DRIVER=array`, `TELESCOPE_ENABLED=false`
  - (SQLite in-memory is commented out; use MySQL or configure SQLite as needed.)

### Playwright (E2E)
- Scripts (from `package.json`):
  - `npm run test:e2e` → `playwright test`
  - `npm run test:e2e:headed` → `playwright test --headed`
  - `npm run test:e2e:ui` → `playwright test --ui`
  - `npm run test:e2e:debug` → `playwright test --debug`
  - `npm run test:e2e:report` → `playwright show-report`
- Config: `playwright.config.js`
  - testDir: `./tests/e2e`
  - globalSetup: `./tests/global-setup`
  - reporter: `html`, `json` (`test-results/results.json`), `list`
  - use: reads from `tests/config-loader` for `app.baseURL`, timeouts, media settings
  - webServer: `php artisan serve` (auto-start, reuse server locally)
  - project: `authenticated` uses saved `storageState`: `./test-results/storage-state/auth.json`
- E2E environment: `.env.e2e` (create manually)
  - Example vars (from docs):
```
E2E_APP_URL=http://localhost:8000
E2E_DB_CONNECTION=mysql
E2E_DB_HOST=127.0.0.1
E2E_DB_PORT=3306
E2E_DB_DATABASE=tracksid_test
E2E_DB_USERNAME=root
E2E_DB_PASSWORD=your_password
E2E_ADMIN_EMAIL=eddie.ridwan@gmail.com
E2E_ADMIN_PASSWORD=Admin100%
```
- Typical flows:
  - Global setup logs in once and saves auth state
  - Tests use AdminLTE UI and robust selectors
- Quick start (if present): `./run-e2e-tests.sh` (per docs)
- Manual:
```
php artisan migrate:fresh --seed
php artisan serve --port=8000
npx playwright test
```

## Development Conventions

### Code style
- PHP-CS-Fixer: `.php-cs-fixer.php` defines extensive rules (arrays, braces, spacing, imports, phpdoc settings, etc.).
  - Run fixer if installed (globally or as a project tool):
```
php-cs-fixer fix
# or (if installed via composer)
./vendor/bin/php-cs-fixer fix
```
  - NOTE: php-cs-fixer is not listed in `require-dev`; install accordingly if needed.
- StyleCI: `.styleci.yml` is present (CI formatting). Keep code consistent.

### Laravel conventions
- PSR-4 namespaces under `App\`
- Use standard Laravel structure for controllers, models, requests, jobs, events
- Keep business logic out of controllers; prefer services/helpers where appropriate
- Register custom helpers via autoloaded files

### Testing practices
- Unit tests in `tests/Unit`, feature/integration in `tests/Feature`
- For E2E, rely on auth storage state; prefer waiting on events/elements over fixed timeouts; collect screenshots/videos/traces on failures
- Ensure database is in a known state (seeders)

### Security & secrets
- Do not commit real credentials or tokens (e.g., `.env`, `.env.e2e`) to VCS
- Keep MAPBOX and other API keys in env variables
- Sanitize and validate inputs; rely on CSRF protection

## CI/CD (from E2E docs)

Example pipeline steps:
```
composer install --no-dev --optimize-autoloader
npm ci
php artisan migrate:fresh --seed --force
npx playwright test --reporter=html,json
```
Configure environment variables on the CI platform (APP_URL, DB settings, E2E_* credentials).

## Useful Paths & Files
- `app/` – application code
- `routes/` – route definitions
- `resources/` – views/assets
- `public/` – web root
- `tests/` – PHPUnit tests
- `tests/e2e/` – Playwright tests
- `test-results/` – E2E outputs (JSON, storage-state)
- `playwright-report/` – HTML reports
- `.env.example` – base application env example
- `.env.e2e.example` – example E2E env (if present)
- `composer.json` – dependencies, scripts, autoload
- `phpunit.xml` – test suites & coverage
- `playwright.config.js` – E2E runner configuration

## Notes & TODOs
- Build pipeline: `webpack.mix.js` exists. Confirm and add Mix devDependencies and npm scripts if frontend compilation is required.
- PHP-CS-Fixer: Install and/or add composer script to standardize formatting commands.
- E2E config loader: Ensure `tests/config-loader` maps to `.env.e2e` (or desired source) and baseURL is correct.
- Credentials: E2E admin credentials in docs are examples; use test-only accounts.

## Quick Command Cheat Sheet
```
# Install
composer install
npm install

# Env & key
cp .env.example .env
php artisan key:generate

# DB (dev)
php artisan migrate:fresh --seed

# Run server
php artisan serve --port=8000

# Unit/Feature tests
./vendor/bin/phpunit

# E2E tests
npm run test:e2e
npm run test:e2e:headed
npm run test:e2e:debug
npm run test:e2e:report

# Formatter (if available)
php-cs-fixer fix
```

---
This file is intended as operational context for Qwen Code in future interactions. Keep commands and conventions aligned with the project’s actual configuration and update sections as the tooling evolves.