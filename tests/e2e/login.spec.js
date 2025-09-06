const { test, expect } = require('@playwright/test');
const configLoader = require('../config-loader');
const E2ETestHelper = require('../utils/e2e-helper');

test.describe('Login Page Tests', () => {
  // Test ini tidak menggunakan authentication state karena kita ingin test login
  test.use({ storageState: { cookies: [], origins: [] } });

  test.beforeEach(async ({ page }) => {
    page.setDefaultTimeout(30000);
    page.setDefaultNavigationTimeout(30000);
    E2ETestHelper.setupPageLogging(page);
  });

  test('should display login page correctly', async ({ page }) => {
    await page.goto('/login');
    await E2ETestHelper.waitForPageReady(page);

    // Check page title (lebih fleksibel)
    const title = await E2ETestHelper.getPageTitle(page);
    expect(title).toBeTruthy(); // Pastikan ada title

    // Check login form elements
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();

    // Check for CSRF token
    const csrfTokenInput = page.locator('input[name="_token"]');
    const csrfTokenMeta = page.locator('meta[name="csrf-token"]');

    const hasCSRFInput = await csrfTokenInput.count() > 0;
    const hasCSRFMeta = await csrfTokenMeta.count() > 0;

    expect(hasCSRFInput || hasCSRFMeta).toBeTruthy();
  });  test('should login successfully with valid credentials', async ({ page }) => {
    const email = configLoader.get('auth.email');
    const password = configLoader.get('auth.password');

    await page.goto('/login');
    await E2ETestHelper.waitForPageReady(page);

    // Fill login form
    await E2ETestHelper.fillFormField(page, 'input[name="email"]', email);
    await E2ETestHelper.fillFormField(page, 'input[name="password"]', password);

    // Submit form
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle' }),
      E2ETestHelper.clickElement(page, 'button[type="submit"]')
    ]);

    // Verify successful login
    const currentUrl = await E2ETestHelper.getCurrentUrl(page);
    const isLoggedIn = !currentUrl.includes('/login');

    expect(isLoggedIn).toBeTruthy();

    // Check for authenticated user elements
    const hasUserMenu = await page.locator('.user-menu, .navbar-nav .dropdown, [class*="user"]').count() > 0;
    const noLoginForm = await page.locator('input[name="email"]').count() === 0;

    expect(hasUserMenu || noLoginForm).toBeTruthy();
  });

  test('should show error for invalid credentials', async ({ page }) => {
    await page.goto('/login');
    await E2ETestHelper.waitForPageReady(page);

    // Try with invalid credentials
    await E2ETestHelper.fillFormField(page, 'input[name="email"]', 'invalid@test.com');
    await E2ETestHelper.fillFormField(page, 'input[name="password"]', 'wrongpassword');

    await E2ETestHelper.clickElement(page, 'button[type="submit"]');
    await page.waitForTimeout(3000);

    // Should show error message or stay on login page
    const hasError = await page.locator('.alert-danger, .error, .invalid-feedback, .alert.alert-danger').count() > 0;
    const stayedOnLogin = page.url().includes('/login');

    expect(hasError || stayedOnLogin).toBeTruthy();
  });

  test('should validate required fields', async ({ page }) => {
    await page.goto('/login');
    await E2ETestHelper.waitForPageReady(page);

    // Try to submit empty form
    await page.locator('button[type="submit"]').click();

    // Wait for any validation messages or errors
    await page.waitForTimeout(1000);

    // Check if form prevented submission by staying on login page
    const currentUrl = page.url();
    expect(currentUrl).toContain('/login');

    // Check for any error messages (look for common error classes)
    const errorMessages = await page.locator('.invalid-feedback, .error, .help-block, .text-danger').count();
    const hasErrors = errorMessages > 0;

    // Check if fields have validation attributes or client-side validation kicked in
    const emailField = page.locator('input[name="email"]');
    const passwordField = page.locator('input[name="password"]');

    const emailRequired = await emailField.getAttribute('required');
    const passwordRequired = await passwordField.getAttribute('required');

    // At least one of these should be true: has required attributes OR has error messages OR stayed on login page
    const hasValidation = (emailRequired !== null || passwordRequired !== null) || hasErrors || currentUrl.includes('/login');

    expect(hasValidation).toBeTruthy();
  });
});
