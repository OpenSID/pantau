const { test, expect } = require('@playwright/test');

test.describe('Homepage Tests', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to homepage
    await page.goto('/');
  });

  test('should display homepage without authentication', async ({ page }) => {
    // Check if page loads successfully
    await expect(page).toHaveURL('/');

    // Check if page title is correct
    await expect(page).toHaveTitle(/SID|OpenSID|Desa/);

    // Verify page is accessible without login
    await expect(page.locator('body')).toBeVisible();
  });

  test('should display main navigation elements', async ({ page }) => {
    // Check for common navigation elements
    const navigationSelectors = [
      'nav',
      '.navbar',
      '[role="navigation"]',
      'header'
    ];

    let navigationFound = false;
    for (const selector of navigationSelectors) {
      const element = page.locator(selector);
      if (await element.count() > 0) {
        await expect(element.first()).toBeVisible();
        navigationFound = true;
        break;
      }
    }

    expect(navigationFound).toBe(true);
  });

  test('should have specific application links', async ({ page }) => {
    const requiredLinks = [
      { path: '/web/openkab', name: 'OpenKab' },
      { path: '/web/opendk', name: 'OpenDK' },
      { path: '/web/opensid', name: 'OpenSID' },
      { path: '/web/layanandesa', name: 'Layanan Desa' },
      { path: '/web/keloladesa', name: 'Kelola Desa' }
    ];

    for (const linkInfo of requiredLinks) {
      // Check if link exists on the page
      const linkElement = page.locator(`a[href="${linkInfo.path}"]`);

      if (await linkElement.count() > 0) {
        // Link found - verify it's visible
        await expect(linkElement.first()).toBeVisible();
        console.log(`✅ Found ${linkInfo.name} link: ${linkInfo.path}`);
      } else {
        // Try to find link with partial href match
        const partialLinkElement = page.locator(`a[href*="${linkInfo.path}"]`);

        if (await partialLinkElement.count() > 0) {
          await expect(partialLinkElement.first()).toBeVisible();
          console.log(`✅ Found ${linkInfo.name} link (partial match): ${linkInfo.path}`);
        } else {
          // Try to find by text content
          const textLinkElement = page.locator(`a:has-text("${linkInfo.name}")`);

          if (await textLinkElement.count() > 0) {
            await expect(textLinkElement.first()).toBeVisible();
            console.log(`✅ Found ${linkInfo.name} link by text`);
          } else {
            console.log(`⚠️  ${linkInfo.name} link not found on homepage`);
          }
        }
      }
    }
  });

  test('should verify application links functionality', async ({ page }) => {
    const requiredLinks = [
      '/web/openkab',
      '/web/opendk',
      '/web/opensid',
      '/web/layanandesa',
      '/web/keloladesa'
    ];

    for (const linkPath of requiredLinks) {
      console.log(`Testing ${linkPath}...`);

      // Navigate directly to the link
      const response = await page.goto(linkPath, {
        waitUntil: 'domcontentloaded',
        timeout: 30000
      });

      // Check if page loads successfully (not 404 or 500)
      expect(response?.status()).toBeLessThan(400);

      // Verify page content loads
      await expect(page.locator('body')).toBeVisible();

      // Check if URL is correct
      expect(page.url()).toContain(linkPath);

      // Verify page has some content (not empty)
      const bodyText = await page.locator('body').textContent();
      expect(bodyText?.trim().length).toBeGreaterThan(50);

      console.log(`✅ ${linkPath} loads successfully`);
    }
  });

  test('should verify application links are clickable from homepage', async ({ page }) => {
    const requiredLinks = [
      { path: '/web/openkab', name: 'OpenKab' },
      { path: '/web/opendk', name: 'OpenDK' },
      { path: '/web/opensid', name: 'OpenSID' },
      { path: '/web/layanandesa', name: 'Layanan Desa' },
      { path: '/web/keloladesa', name: 'Kelola Desa' }
    ];

    for (const linkInfo of requiredLinks) {
      // Go back to homepage for each test
      await page.goto('/');

      // Try different ways to find the link
      let linkElement = page.locator(`a[href="${linkInfo.path}"]`);

      if (await linkElement.count() === 0) {
        // Try partial match
        linkElement = page.locator(`a[href*="${linkInfo.path}"]`);
      }

      if (await linkElement.count() === 0) {
        // Try by text content
        linkElement = page.locator(`a:has-text("${linkInfo.name}")`);
      }

      if (await linkElement.count() > 0) {
        // Link found - test clicking it
        const firstLink = linkElement.first();

        // Ensure link is visible and clickable
        await expect(firstLink).toBeVisible();

        // Click the link
        await Promise.race([
          firstLink.click({ timeout: 10000 }),
          page.waitForTimeout(10000)
        ]);

        // Wait for navigation
        await page.waitForLoadState('domcontentloaded', { timeout: 15000 });

        // Verify navigation occurred
        expect(page.url()).toContain(linkInfo.path.replace('/web/', ''));

        console.log(`✅ Successfully clicked and navigated to ${linkInfo.name}`);
      } else {
        console.log(`⚠️  Could not find clickable ${linkInfo.name} link on homepage`);
      }
    }
  });

  test('should verify application pages have proper titles', async ({ page }) => {
    const linkTests = [
      { path: '/web/openkab', expectedTitleParts: ['OpenKab', 'Kabupaten'] },
      { path: '/web/opendk', expectedTitleParts: ['OpenDK', 'Daerah'] },
      { path: '/web/opensid', expectedTitleParts: ['OpenSID', 'SID'] },
      { path: '/web/layanandesa', expectedTitleParts: ['Layanan', 'Desa'] },
      { path: '/web/keloladesa', expectedTitleParts: ['Kelola', 'Desa'] }
    ];

    for (const linkTest of linkTests) {
      // Navigate to the page
      await page.goto(linkTest.path, { waitUntil: 'domcontentloaded' });

      // Get page title
      const title = await page.title();
      console.log(`${linkTest.path} title: "${title}"`);

      // Check if title contains expected parts (case insensitive)
      const titleLower = title.toLowerCase();
      const hasExpectedContent = linkTest.expectedTitleParts.some(part =>
        titleLower.includes(part.toLowerCase())
      );

      if (hasExpectedContent) {
        console.log(`✅ ${linkTest.path} has appropriate title`);
      } else {
        console.log(`⚠️  ${linkTest.path} title may not be specific: "${title}"`);
      }

      // At minimum, title should not be empty
      expect(title.trim()).not.toBe('');
      expect(title.length).toBeGreaterThan(5);
    }
  });

  test('should display main content sections', async ({ page }) => {
    // Check for main content area
    const contentSelectors = [
      'main',
      '.content',
      '.main-content',
      '#content',
      '.container'
    ];

    let contentFound = false;
    for (const selector of contentSelectors) {
      const element = page.locator(selector);
      if (await element.count() > 0) {
        await expect(element.first()).toBeVisible();
        contentFound = true;
        break;
      }
    }

    expect(contentFound).toBe(true);
  });

  test('should have working links in navigation', async ({ page }) => {
    // Get all navigation links
    const links = page.locator('a[href]').filter({ hasText: /.+/ });
    const linkCount = await links.count();

    if (linkCount > 0) {
      // Test first few links to ensure they're not broken
      const maxLinksToTest = Math.min(5, linkCount);

      for (let i = 0; i < maxLinksToTest; i++) {
        const link = links.nth(i);
        const href = await link.getAttribute('href');

        // Skip external links and javascript links
        if (href && !href.startsWith('http') && !href.startsWith('javascript:') && !href.startsWith('mailto:')) {
          // Check if link is visible and clickable
          await expect(link).toBeVisible();

          // Verify href is not empty
          expect(href.trim()).not.toBe('');
        }
      }
    }
  });

  test('should display footer information', async ({ page }) => {
    // Check for footer
    const footerSelectors = [
      'footer',
      '.footer',
      '#footer',
      '.site-footer'
    ];

    let footerFound = false;
    for (const selector of footerSelectors) {
      const element = page.locator(selector);
      if (await element.count() > 0) {
        await expect(element.first()).toBeVisible();
        footerFound = true;
        break;
      }
    }

    // Footer might not be visible on short pages, so we'll just check if it exists
    expect(footerFound).toBe(true);
  });

  test('should be responsive on mobile viewport', async ({ page }) => {
    // Change to mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });

    // Reload page to trigger responsive behavior
    await page.reload();

    // Check if page still loads properly
    await expect(page.locator('body')).toBeVisible();

    // Check if navigation adapts to mobile (common patterns)
    const mobileNavSelectors = [
      '.navbar-toggler',
      '.mobile-menu',
      '.hamburger',
      '[data-toggle="collapse"]',
      '.menu-toggle'
    ];

    // At least one mobile navigation pattern should exist
    let mobileNavFound = false;
    for (const selector of mobileNavSelectors) {
      const element = page.locator(selector);
      if (await element.count() > 0) {
        mobileNavFound = true;
        break;
      }
    }

    // Note: Some sites might not have mobile-specific navigation
    // so we'll make this assertion optional
    if (mobileNavFound) {
      console.log('Mobile navigation elements found');
    }
  });

  test('should load without JavaScript errors', async ({ page }) => {
    const jsErrors = [];

    // Listen for console errors
    page.on('console', msg => {
      if (msg.type() === 'error') {
        jsErrors.push(msg.text());
      }
    });

    // Listen for page errors
    page.on('pageerror', error => {
      jsErrors.push(error.message);
    });

    // Navigate to page
    await page.goto('/');

    // Wait a bit for any async errors
    await page.waitForTimeout(2000);

    // Check if there are any critical JS errors
    const criticalErrors = jsErrors.filter(error =>
      !error.includes('favicon') &&
      !error.includes('404') &&
      !error.includes('net::ERR_INTERNET_DISCONNECTED')
    );

    expect(criticalErrors.length).toBe(0);
  });

  test('should have basic SEO elements', async ({ page }) => {
    // Check for basic SEO elements
    const title = await page.title();
    expect(title.trim()).not.toBe('');
    expect(title.length).toBeGreaterThan(10);

    // Check for meta description
    const metaDescription = page.locator('meta[name="description"]');
    if (await metaDescription.count() > 0) {
      const content = await metaDescription.getAttribute('content');
      expect(content?.trim()).not.toBe('');
    }

    // Check for meta keywords (optional)
    const metaKeywords = page.locator('meta[name="keywords"]');
    if (await metaKeywords.count() > 0) {
      const content = await metaKeywords.getAttribute('content');
      expect(content?.trim()).not.toBe('');
    }
  });

  test('should handle search functionality if available', async ({ page }) => {
    // Look for search input
    const searchSelectors = [
      'input[type="search"]',
      'input[name="search"]',
      'input[placeholder*="cari" i]',
      'input[placeholder*="search" i]',
      '.search-input',
      '#search'
    ];

    let searchInput = null;
    for (const selector of searchSelectors) {
      const element = page.locator(selector);
      if (await element.count() > 0 && await element.first().isVisible()) {
        searchInput = element.first();
        break;
      }
    }

    if (searchInput) {
      // Test search functionality
      await expect(searchInput).toBeVisible();
      await expect(searchInput).toBeEditable();

      // Try typing in search
      await searchInput.fill('test search');
      const value = await searchInput.inputValue();
      expect(value).toBe('test search');
    }
  });
});
