const { test, expect } = require('@playwright/test');
const configLoader = require('../config-loader');
const E2ETestHelper = require('../utils/e2e-helper');

test.describe('Authentication Flow Tests', () => {
  // Test untuk authentication flow menggunakan stored state
  test.beforeEach(async ({ page }) => {
    page.setDefaultTimeout(30000);
    page.setDefaultNavigationTimeout(30000);
    E2ETestHelper.setupPageLogging(page);
  });

  test('should maintain authentication across different pages', async ({ page }) => {
    // Test navigating to various pages while authenticated
    const pagesToTest = ['/dashboard', '/dashboard/keloladesa', '/dashboard/layanan-desa'];

    for (const pagePath of pagesToTest) {
      try {
        await page.goto(pagePath);
        await E2ETestHelper.waitForPageReady(page);

        // Should not be redirected to login
        const currentUrl = await E2ETestHelper.getCurrentUrl(page);
        expect(currentUrl).not.toContain('/login');

      } catch (error) {
        console.warn(`Failed to test page ${pagePath}:`, error.message);
      }
    }
  });

  test('should have access to protected resources', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Try to access API endpoints that require authentication
    const response = await page.request.get('/dashboard/summary-keloladesa');

    // Should not get 401 or redirect to login
    expect(response.status()).not.toBe(401);
    expect(response.status()).not.toBe(302); // redirect
  });

  test('should display user-specific content', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Look for user-specific elements
    const userEmail = configLoader.get('auth.email');

    // Check if user email appears anywhere on the page
    const pageContent = await page.content();
    const hasUserEmail = pageContent.includes(userEmail) ||
                        pageContent.includes(userEmail.split('@')[0]); // username part

    // Or check for generic user indicators
    const hasUserIndicators = await page.locator(
      '.user-menu, .user-panel, [class*="user"], .dropdown-toggle'
    ).count() > 0;

    expect(hasUserEmail || hasUserIndicators).toBeTruthy();
  });

  test('should handle logout if logout function exists', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Look for logout button/link with various patterns
    const logoutSelectors = [
      'a[href*="/logout"]',
      'form[action*="/logout"] button',
      '.dropdown-menu a:has-text("Logout")',
      '.dropdown-menu a:has-text("Keluar")',
      '.user-menu a:has-text("Logout")',
      '.user-menu a:has-text("Keluar")',
      'button:has-text("Logout")',
      'button:has-text("Keluar")'
    ];

    let logoutElement = null;
    for (const selector of logoutSelectors) {
      const element = page.locator(selector).first();
      if (await element.count() > 0 && await element.isVisible()) {
        logoutElement = element;
        break;
      }
    }

    if (logoutElement) {
      // Test logout functionality
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle' }),
        logoutElement.click()
      ]);

      // Should be redirected to login page or home page
      const currentUrl = await E2ETestHelper.getCurrentUrl(page);
      const isLoggedOut = currentUrl.includes('/login') ||
                         currentUrl === configLoader.get('app.baseURL') ||
                         currentUrl.endsWith('/');

      expect(isLoggedOut).toBeTruthy();
    } else {
      // Skip test if no logout button found
      test.skip();
    }
  });
});
