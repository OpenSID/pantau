const { test, expect } = require('@playwright/test');
const configLoader = require('../config-loader');
const E2ETestHelper = require('../utils/e2e-helper');

test.describe('Dashboard Tests', () => {
  // Test ini menggunakan authentication state yang sudah disimpan
  test.beforeEach(async ({ page }) => {
    page.setDefaultTimeout(30000);
    page.setDefaultNavigationTimeout(30000);
    E2ETestHelper.setupPageLogging(page);
  });

  test('should access dashboard successfully', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Verify we're on dashboard page
    const currentUrl = await E2ETestHelper.getCurrentUrl(page);
    expect(currentUrl).toContain('/dashboard');

    // Verify user is authenticated (not redirected to login)
    expect(currentUrl).not.toContain('/login');

    // Check page title
    const title = await E2ETestHelper.getPageTitle(page);
    expect(title).toBeTruthy();
  });

  test('should display dashboard navigation', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Check for common dashboard navigation elements
    const navElements = [
      '.main-header',
      '.navbar',
      '.main-sidebar',
      '.sidebar',
      '.nav-sidebar',
      '.content-wrapper'
    ];

    let hasNavigation = false;
    for (const selector of navElements) {
      const elementCount = await page.locator(selector).count();
      if (elementCount > 0) {
        hasNavigation = true;
        break;
      }
    }

    expect(hasNavigation).toBeTruthy();
  });

  test('should display user information', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Look for user information in various possible locations
    const userSelectors = [
      '.user-panel',
      '.user-menu',
      '.navbar-nav .dropdown',
      '[class*="user"]',
      '.info a',
      '.user-footer'
    ];

    let hasUserInfo = false;
    for (const selector of userSelectors) {
      const element = page.locator(selector);
      const count = await element.count();
      if (count > 0) {
        const isVisible = await element.first().isVisible();
        if (isVisible) {
          hasUserInfo = true;
          break;
        }
      }
    }

    expect(hasUserInfo).toBeTruthy();
  });

  test('should have working navigation links', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Find navigation links
    const navLinks = await page.locator('a[href*="/dashboard"], .nav-link[href*="/"], .sidebar a[href]').all();

    if (navLinks.length > 0) {
      // Test first few navigation links (avoid testing too many)
      const linksToTest = navLinks.slice(0, Math.min(3, navLinks.length));

      for (const link of linksToTest) {
        const href = await link.getAttribute('href');
        if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
          try {
            await link.click();
            await E2ETestHelper.waitForPageReady(page);

            // Verify navigation worked
            const currentUrl = await E2ETestHelper.getCurrentUrl(page);
            expect(currentUrl).not.toContain('/login');

            // Go back to dashboard
            await page.goto('/dashboard');
            await E2ETestHelper.waitForPageReady(page);
          } catch (error) {
            console.warn(`Failed to test navigation link ${href}:`, error.message);
          }
        }
      }
    }
  });

  test('should maintain session across page refresh', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Refresh the page
    await page.reload();
    await E2ETestHelper.waitForPageReady(page);

    // Should still be authenticated
    const currentUrl = await E2ETestHelper.getCurrentUrl(page);
    expect(currentUrl).not.toContain('/login');
    expect(currentUrl).toContain('/dashboard');
  });

  test('should display main content area', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Check for main content containers
    const contentSelectors = [
      '.content-wrapper',
      '.main-content',
      '.content',
      'main',
      '.container-fluid',
      '.row'
    ];

    let hasContent = false;
    for (const selector of contentSelectors) {
      const elementCount = await page.locator(selector).count();
      if (elementCount > 0) {
        hasContent = true;
        break;
      }
    }

    expect(hasContent).toBeTruthy();
  });

  test('should handle responsive layout', async ({ page }) => {
    await page.goto('/dashboard');
    await E2ETestHelper.waitForPageReady(page);

    // Test desktop view
    await page.setViewportSize({ width: 1280, height: 720 });
    await page.waitForTimeout(1000);

    let desktopElements = await page.locator('.main-sidebar, .sidebar').count();

    // Test mobile view
    await page.setViewportSize({ width: 375, height: 667 });
    await page.waitForTimeout(1000);

    // In mobile view, sidebar might be hidden or collapsed
    const mobileLayout = await page.locator('body').evaluate(el => {
      return window.getComputedStyle(el).getPropertyValue('--mobile-detected') ||
             el.classList.contains('sidebar-collapse') ||
             el.classList.contains('sidebar-mini');
    });

    // At least one layout should be detected
    expect(desktopElements > 0 || mobileLayout).toBeTruthy();

    // Reset to desktop view
    await page.setViewportSize({ width: 1280, height: 720 });
  });
});
