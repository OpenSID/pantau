<?php

namespace Tests\Unit\Helpers;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SecurityHelperTest extends TestCase
{
    /**
     * Test that is_trusted_github_api_url validates trusted URLs correctly.
     */
    public function test_is_trusted_github_api_url_allows_trusted_urls()
    {
        $trustedUrls = [
            'https://api.github.com/repos/OpenSID/rilis-premium/releases/latest',
            'https://api.github.com/repos/OpenSID/rilis-pbb/releases/latest',
            'https://api.github.com/repos/OpenSID/opendk/releases/latest',
            'https://api.github.com/repos/OpenSID/rilis-opensid-api/releases/latest',
        ];

        foreach ($trustedUrls as $url) {
            $this->assertTrue(is_trusted_github_api_url($url), "URL should be trusted: {$url}");
        }
    }

    /**
     * Test that is_trusted_github_api_url rejects untrusted URLs.
     */
    public function test_is_trusted_github_api_url_rejects_untrusted_urls()
    {
        $untrustedUrls = [
            // Different schemes
            'http://api.github.com/repos/OpenSID/rilis-premium/releases/latest',
            'ftp://api.github.com/repos/OpenSID/rilis-premium/releases/latest',
            
            // Different hosts
            'https://github.com/repos/OpenSID/rilis-premium/releases/latest',
            'https://malicious.com/repos/OpenSID/rilis-premium/releases/latest',
            'https://api.github.com.evil.com/repos/OpenSID/rilis-premium/releases/latest',
            'https://evil.api.github.com/repos/OpenSID/rilis-premium/releases/latest',
            
            // Different paths
            'https://api.github.com/repos/evil/repo/releases/latest',
            'https://api.github.com/repos/OpenSID/evil-repo/releases/latest',
            'https://api.github.com/repos/OpenSID/rilis-premium/releases/1',
            'https://api.github.com/repos/OpenSID/rilis-premium',
            'https://api.github.com/users/OpenSID',
            
            // SSRF attempts
            'https://api.github.com/../../../etc/passwd',
            'https://api.github.com/repos/OpenSID/rilis-premium/releases/latest/../../../admin',
            
            // Internal IP addresses (should be blocked by domain check)
            'https://127.0.0.1/repos/OpenSID/rilis-premium/releases/latest',
            'https://192.168.1.1/repos/OpenSID/rilis-premium/releases/latest',
            'https://10.0.0.1/repos/OpenSID/rilis-premium/releases/latest',
            
            // Invalid URLs
            '',
            'not-a-url',
            'https://',
            'https://api.github.com',
        ];

        foreach ($untrustedUrls as $url) {
            $this->assertFalse(is_trusted_github_api_url($url), "URL should be rejected: {$url}");
        }
    }

    /**
     * Test that lastrelease function blocks untrusted URLs.
     */
    public function test_lastrelease_blocks_untrusted_urls()
    {
        Http::fake([
            '*' => Http::response(['tag_name' => 'v1.0.0'], 200)
        ]);

        $untrustedUrls = [
            'https://malicious.com/api',
            'https://api.github.com/repos/evil/repo/releases/latest',
            'http://api.github.com/repos/OpenSID/rilis-premium/releases/latest',
        ];

        foreach ($untrustedUrls as $url) {
            $result = lastrelease($url);
            $this->assertFalse($result, "lastrelease should reject untrusted URL: {$url}");
        }

        // Verify no HTTP requests were made to untrusted URLs
        Http::assertNothingSent();
    }

    /**
     * Test that lastrelease function allows trusted URLs and makes HTTP requests.
     */
    public function test_lastrelease_allows_trusted_urls()
    {
        $mockResponse = [
            'tag_name' => 'v2.1.0',
            'name' => 'Release 2.1.0',
            'published_at' => '2023-01-01T00:00:00Z'
        ];

        Http::fake([
            'api.github.com/repos/OpenSID/rilis-premium/releases/latest' => Http::response($mockResponse, 200)
        ]);

        $trustedUrl = 'https://api.github.com/repos/OpenSID/rilis-premium/releases/latest';
        $result = lastrelease($trustedUrl);

        $this->assertNotFalse($result);
        $this->assertEquals('v2.1.0', $result->tag_name);
        $this->assertEquals('Release 2.1.0', $result->name);

        // Verify the HTTP request was made
        Http::assertSent(function ($request) use ($trustedUrl) {
            return $request->url() === $trustedUrl;
        });
    }

    /**
     * Test that lastrelease function handles HTTP errors gracefully.
     */
    public function test_lastrelease_handles_http_errors()
    {
        Http::fake([
            'api.github.com/repos/OpenSID/rilis-premium/releases/latest' => Http::response([], 404)
        ]);

        $trustedUrl = 'https://api.github.com/repos/OpenSID/rilis-premium/releases/latest';
        $result = lastrelease($trustedUrl);

        $this->assertFalse($result);
    }
}