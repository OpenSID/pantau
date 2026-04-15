<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

abstract class TracksidApiTest extends TestCase
{
    use WithFaker;

    /**
     * Default bearer token for API testing
     *
     * @var string
     */
    protected $bearerToken;

    /**
     * Setup the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Use the dev token from config for testing
        $this->bearerToken = config('tracksid.sandi.dev_token');
    }

    /**
     * Make a GET request with Bearer token
     *
     * @param string $uri
     * @param array $headers
     * @return \Illuminate\Testing\TestResponse
     */
    protected function getJsonWithToken(string $uri, array $headers = [])
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->getJson($uri, $headers);
    }

    /**
     * Make a POST request with Bearer token
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Testing\TestResponse
     */
    protected function postJsonWithToken(string $uri, array $data = [], array $headers = [])
    {        
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson($uri, $data, $headers);
    }

    /**
     * Make a PUT request with Bearer token
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Testing\TestResponse
     */
    protected function putJsonWithToken(string $uri, array $data = [], array $headers = [])
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->putJson($uri, $data, $headers);
    }

    /**
     * Make a PATCH request with Bearer token
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Testing\TestResponse
     */
    protected function patchJsonWithToken(string $uri, array $data = [], array $headers = [])
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->patchJson($uri, $data, $headers);
    }

    /**
     * Make a DELETE request with Bearer token
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Testing\TestResponse
     */
    protected function deleteJsonWithToken(string $uri, array $data = [], array $headers = [])
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->deleteJson($uri, $data, $headers);
    }
}