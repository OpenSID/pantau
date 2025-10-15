# E2E Testing Guide

## Overview

This document provides guidance on running and maintaining E2E (End-to-End) tests for the TracksID application.

## Prerequisites

-   Node.js (v16 or higher)
-   PHP (v8.0 or higher)
-   Composer
-   MySQL/MariaDB database
-   Laravel application properly configured

## Setup

### 1. Install Dependencies

```bash
# Install Node.js dependencies
npm install

# Install PHP dependencies
composer install
```

### 2. Configure Environment

Create or update `.env.e2e` file with your test environment settings:

```bash
# Application URL
E2E_APP_URL=http://localhost:8000

# Database Configuration
E2E_DB_CONNECTION=mysql
E2E_DB_HOST=127.0.0.1
E2E_DB_PORT=3306
E2E_DB_DATABASE=tracksid_test
E2E_DB_USERNAME=root
E2E_DB_PASSWORD=your_password

# Authentication Credentials
E2E_ADMIN_EMAIL=eddie.ridwan@gmail.com
E2E_ADMIN_PASSWORD=Admin100%
```

### 3. Database Setup

The tests will automatically run migrations and seeders, but make sure your database is accessible.

## Running Tests

### Quick Start (Recommended)

```bash
# Run all E2E tests with automatic setup
./run-e2e-tests.sh
```

### Manual Execution

```bash
# Setup database manually
php artisan migrate:fresh --seed

# Start Laravel server (in another terminal)
php artisan serve --port=8000

# Run specific test suite
npx playwright test tests/e2e/auth.spec.js

# Run all tests
npx playwright test

# Run tests in headed mode (with browser UI)
npx playwright test --headed

# Run tests with specific browser
npx playwright test --project=chromium
```

## Test Structure

### Test Files

-   `tests/e2e/setup.spec.js` - Environment and basic connectivity tests
-   `tests/e2e/auth.spec.js` - Authentication and login tests

### Support Files

-   `tests/e2e-config-loader.js` - Configuration management
-   `tests/utils/e2e-helper.js` - Test utilities and helpers
-   `tests/global-setup.js` - Global test setup (database, cache clearing)

## Test Configuration

### Playwright Config (`playwright.config.js`)

-   **testDir**: `./tests/e2e` - Test directory
-   **fullyParallel**: `true` - Run tests in parallel
-   **retries**: 2 on CI, 0 locally
-   **workers**: 1 on CI for stability
-   **globalSetup**: Automatic database setup
-   **webServer**: Automatic Laravel server startup

### Browsers Tested

-   Chromium (Desktop)
-   Firefox (Desktop)
-   WebKit/Safari (Desktop)
-   Mobile Chrome (Pixel 5)
-   Mobile Safari (iPhone 12)

## Authentication Tests

### Test Cases Covered

1. **Successful Login** - Tests login with valid seeded user credentials
2. **Invalid Login Handling** - Tests error handling for wrong credentials
3. **Session Maintenance** - Tests that sessions persist across page navigation
4. **Logout Functionality** - Tests logout process (if logout button found)
5. **CSRF Token Validation** - Ensures Laravel CSRF protection is working

### User Credentials

Tests use seeded user credentials from `database/seeders/UserSeeder.php`:

-   Email: `eddie.ridwan@gmail.com`
-   Password: `Admin100%`

## Debugging

### Screenshots and Videos

-   Screenshots are automatically taken on test failures
-   Videos are recorded for failed tests
-   Traces are captured for debugging (on retries)

### Console Logging

The test helper automatically logs:

-   Browser console errors
-   Page errors
-   Failed network requests
-   Debug information on failures

### Manual Debugging

```bash
# Run tests in debug mode
npx playwright test --debug

# Open test results
npx playwright show-report

# Generate and view trace
npx playwright test --trace on
```

## Common Issues and Solutions

### 1. Database Connection Issues

```bash
# Check database configuration in .env.e2e
# Ensure database exists and is accessible
# Run database setup manually:
php artisan migrate:fresh --seed
```

### 2. Server Not Starting

```bash
# Check if port 8000 is available
# Manually start server:
php artisan serve --port=8000
```

### 3. Login Failures

```bash
# Verify user credentials in database
# Check if UserSeeder has run properly
# Ensure CSRF protection is not blocking requests
```

### 4. Element Not Found

```bash
# Check if AdminLTE theme is properly loaded
# Verify CSS and JavaScript are loading correctly
# Use headed mode to visually inspect page
```

## Best Practices

### 1. Test Independence

-   Each test should be independent and not rely on others
-   Use `beforeEach` hooks for common setup
-   Clean up after tests in `afterEach` hooks

### 2. Waiting Strategies

-   Use `waitForLoadState('networkidle')` instead of fixed timeouts
-   Wait for specific elements to be visible/hidden
-   Use the helper functions for consistent waiting

### 3. Error Handling

-   Always use try-catch blocks in tests
-   Take screenshots on failures
-   Log detailed debug information

### 4. Configuration Management

-   Use environment variables for configuration
-   Don't hardcode URLs, credentials, or timeouts
-   Use the config loader for consistent settings

## CI/CD Integration

### Environment Variables

Set these in your CI environment:

```bash
E2E_APP_URL=http://localhost:8000
E2E_DB_HOST=127.0.0.1
E2E_DB_DATABASE=tracksid_test
E2E_ADMIN_EMAIL=eddie.ridwan@gmail.com
E2E_ADMIN_PASSWORD=Admin100%
```

### Pipeline Commands

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci

# Setup database
php artisan migrate:fresh --seed --force

# Run tests
npx playwright test --reporter=html,json
```

## Extending Tests

### Adding New Test Files

1. Create new `.spec.js` file in `tests/e2e/`
2. Import required helpers and config
3. Follow existing patterns for error handling
4. Add appropriate `beforeEach` and `afterEach` hooks

### Using Test Helpers

```javascript
const E2ETestHelper = require("../utils/e2e-helper");

// Perform login
await E2ETestHelper.performLogin(page, email, password);

// Fill form fields with retry
await E2ETestHelper.fillFormField(page, 'input[name="field"]', "value");

// Click elements safely
await E2ETestHelper.clickElement(page, "button.submit");

// Wait for page ready
await E2ETestHelper.waitForPageReady(page);
```

## Troubleshooting

If tests are failing, check:

1. Database connection and seeding
2. Laravel server is running and accessible
3. Environment configuration is correct
4. Browser permissions and network access
5. Console logs for JavaScript errors

For more help, run tests in headed mode and check the browser developer tools.
