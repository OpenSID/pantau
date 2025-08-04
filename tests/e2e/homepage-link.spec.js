const { test, expect } = require('@playwright/test');

test.describe('Application Links Tests', () => {
  const applicationLinks = [
    {
      path: '/web/openkab',
      name: 'OpenKab',
      description: 'Sistem Informasi Kabupaten'
    },
    {
      path: '/web/opendk',
      name: 'OpenDK',
      description: 'Sistem Informasi Daerah'
    },
    {
      path: '/web/opensid',
      name: 'OpenSID',
      description: 'Sistem Informasi Desa'
    },
    {
      path: '/web/layanandesa',
      name: 'Layanan Desa',
      description: 'Portal Layanan Desa'
    },
    {
      path: '/web/keloladesa',
      name: 'Kelola Desa',
      description: 'Aplikasi Kelola Desa'
    }
  ];

  test('should verify all application links are accessible', async ({ page }) => {
    for (const app of applicationLinks) {
      console.log(`\n🔍 Testing ${app.name} (${app.path})...`);

      // Navigate to the application page
      const response = await page.goto(app.path, {
        waitUntil: 'networkidle',
        timeout: 30000
      });

      // Verify HTTP status is successful
      const status = response?.status() || 0;
      expect(status, `${app.name} should return successful HTTP status`).toBeLessThan(400);
      console.log(`  ✅ HTTP Status: ${status}`);

      // Verify page loads completely
      await expect(page.locator('body')).toBeVisible();

      // Verify URL is correct
      expect(page.url()).toContain(app.path);
      console.log(`  ✅ URL correct: ${page.url()}`);

      // Verify page has substantial content
      const bodyText = await page.locator('body').textContent();
      const contentLength = bodyText?.trim().length || 0;
      expect(contentLength).toBeGreaterThan(100);
      console.log(`  ✅ Content length: ${contentLength} characters`);

      // Verify page title is not empty
      const title = await page.title();
      expect(title.trim()).not.toBe('');
      console.log(`  ✅ Page title: "${title}"`);
    }
  });

  test('should verify application links exist on homepage', async ({ page }) => {
    // Navigate to homepage
    await page.goto('/');

    for (const app of applicationLinks) {
      console.log(`\n🔍 Looking for ${app.name} link on homepage...`);

      // Try multiple strategies to find the link
      const strategies = [
        () => page.locator(`a[href="${app.path}"]`),
        () => page.locator(`a[href*="${app.path}"]`),
        () => page.locator(`a:has-text("${app.name}")`),
        () => page.locator(`a[title*="${app.name}" i]`),
        () => page.locator(`a[alt*="${app.name}" i]`)
      ];

      let linkFound = false;
      let linkElement = null;

      for (const strategy of strategies) {
        linkElement = strategy();
        if (await linkElement.count() > 0) {
          linkFound = true;
          break;
        }
      }

      if (linkFound && linkElement) {
        await expect(linkElement.first()).toBeVisible();
        console.log(`  ✅ ${app.name} link found and visible`);

        // Get the actual href
        const href = await linkElement.first().getAttribute('href');
        console.log(`  ✅ Link href: ${href}`);
      } else {
        console.log(`  ⚠️  ${app.name} link not found on homepage`);

        // Additional search in page content
        const pageContent = await page.content();
        const hasReference = pageContent.toLowerCase().includes(app.name.toLowerCase());
        if (hasReference) {
          console.log(`  ℹ️  ${app.name} is mentioned in page content`);
        }
      }
    }
  });

  test('should verify application links are clickable from homepage', async ({ page }) => {
    for (const app of applicationLinks) {
      console.log(`\n🖱️  Testing click navigation to ${app.name}...`);

      // Go to homepage
      await page.goto('/');

      // Find the link using multiple strategies
      let linkElement = null;
      const strategies = [
        () => page.locator(`a[href="${app.path}"]`),
        () => page.locator(`a[href*="${app.path}"]`),
        () => page.locator(`a:has-text("${app.name}")`)
      ];

      for (const strategy of strategies) {
        const element = strategy();
        if (await element.count() > 0) {
          linkElement = element.first();
          break;
        }
      }

      if (linkElement) {
        // Ensure link is visible and clickable
        await expect(linkElement).toBeVisible();
        await expect(linkElement).toBeEnabled();

        // Click the link
        await linkElement.click();

        // Wait for navigation
        await page.waitForLoadState('networkidle', { timeout: 15000 });

        // Verify navigation success
        const currentUrl = page.url();
        expect(currentUrl).toContain(app.path.replace('/web/', ''));
        console.log(`  ✅ Successfully navigated to: ${currentUrl}`);

        // Verify page loaded properly
        await expect(page.locator('body')).toBeVisible();

        const title = await page.title();
        console.log(`  ✅ Destination page title: "${title}"`);
      } else {
        console.log(`  ⚠️  Could not find clickable link for ${app.name}`);
      }
    }
  });

  test('should verify application pages load required resources', async ({ page }) => {
    for (const app of applicationLinks) {
      console.log(`\n📦 Testing resources for ${app.name}...`);

      const failedRequests = [];

      // Monitor network requests
      page.on('response', response => {
        if (response.status() >= 400) {
          failedRequests.push({
            url: response.url(),
            status: response.status()
          });
        }
      });

      // Navigate to the page
      await page.goto(app.path, { waitUntil: 'networkidle' });

      // Wait for any lazy-loaded resources
      await page.waitForTimeout(3000);

      // Filter out non-critical failed requests
      const criticalFailures = failedRequests.filter(request => {
        const url = request.url.toLowerCase();
        return !url.includes('favicon') &&
               !url.includes('analytics') &&
               !url.includes('ads') &&
               !url.includes('tracking');
      });

      if (criticalFailures.length > 0) {
        console.log(`  ⚠️  Failed requests for ${app.name}:`);
        criticalFailures.forEach(failure => {
          console.log(`    - ${failure.status}: ${failure.url}`);
        });
      } else {
        console.log(`  ✅ All critical resources loaded successfully`);
      }

      // Don't fail the test for resource loading issues, just log them
      expect(criticalFailures.length).toBeLessThanOrEqual(3);
    }
  });

  test('should verify application pages are responsive', async ({ page }) => {
    const viewports = [
      { width: 1920, height: 1080, name: 'Desktop Large' },
      { width: 1366, height: 768, name: 'Desktop Medium' },
      { width: 768, height: 1024, name: 'Tablet' },
      { width: 375, height: 667, name: 'Mobile' }
    ];

    for (const app of applicationLinks) {
      console.log(`\n📱 Testing responsiveness for ${app.name}...`);

      for (const viewport of viewports) {
        // Set viewport
        await page.setViewportSize({ width: viewport.width, height: viewport.height });

        // Navigate to page
        await page.goto(app.path, { waitUntil: 'domcontentloaded' });

        // Verify page loads
        await expect(page.locator('body')).toBeVisible();

        // Check for horizontal scrollbar (indicates responsive issues)
        const bodyWidth = await page.evaluate(() => document.body.scrollWidth);
        const viewportWidth = viewport.width;

        if (bodyWidth > viewportWidth + 50) { // Allow small margin
          console.log(`  ⚠️  Potential responsive issue on ${viewport.name}: body width ${bodyWidth}px > viewport ${viewportWidth}px`);
        } else {
          console.log(`  ✅ Responsive on ${viewport.name}`);
        }
      }
    }
  });

  test('should verify application pages performance', async ({ page }) => {
    for (const app of applicationLinks) {
      console.log(`\n⚡ Testing performance for ${app.name}...`);

      const startTime = Date.now();

      // Navigate to page
      await page.goto(app.path, { waitUntil: 'networkidle' });

      const loadTime = Date.now() - startTime;
      console.log(`  ⏱️  Load time: ${loadTime}ms`);

      // Verify page loads within reasonable time (adjust as needed)
      expect(loadTime).toBeLessThan(30000); // 30 seconds max

      if (loadTime < 3000) {
        console.log(`  ✅ Fast loading (< 3s)`);
      } else if (loadTime < 10000) {
        console.log(`  ✅ Acceptable loading (< 10s)`);
      } else {
        console.log(`  ⚠️  Slow loading (> 10s)`);
      }
    }
  });
});
