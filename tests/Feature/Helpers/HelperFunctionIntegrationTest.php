<?php

namespace Tests\Feature\Helpers;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HelperFunctionIntegrationTest extends TestCase
{
    /**
     * Test that the actual helper functions using lastrelease work correctly with our security fix.
     */
    public function test_lastrelease_opensid_function_works_with_security_fix()
    {
        $mockResponse = [
            'tag_name' => 'v2.3.0',
            'name' => 'Release 2.3.0',
            'published_at' => '2023-01-01T00:00:00Z'
        ];

        Http::fake([
            'api.github.com/repos/OpenSID/rilis-premium/releases/latest' => Http::response($mockResponse, 200)
        ]);

        $version = lastrelease_opensid();

        $this->assertEquals('2.3.0', $version);
    }

    /**
     * Test that the PBB function works correctly.
     */
    public function test_lastrelease_pbb_function_works_with_security_fix()
    {
        $mockResponse = [
            'tag_name' => 'v2.4.0',
            'name' => 'Release 2.4.0',
            'published_at' => '2023-01-01T00:00:00Z'
        ];

        Http::fake([
            'api.github.com/repos/OpenSID/rilis-pbb/releases/latest' => Http::response($mockResponse, 200)
        ]);

        $version = lastrelease_pbb();

        $this->assertEquals('2.4.0', $version);
    }

    /**
     * Test that all the helper functions return default versions when GitHub is unavailable.
     */
    public function test_helper_functions_return_defaults_when_github_unavailable()
    {
        Http::fake([
            '*' => Http::response([], 404)
        ]);

        // These should return default values when the HTTP request fails
        $opensidVersion = lastrelease_opensid();
        $pbbVersion = lastrelease_pbb();
        $opendkVersion = lastrelease_opendk();
        $layananDesaVersion = lastrelease_api_layanandesa();

        // Verify they returned the cached/default values
        $this->assertIsString($opensidVersion);
        $this->assertIsString($pbbVersion);
        $this->assertIsString($opendkVersion);
        $this->assertIsString($layananDesaVersion);
    }
}