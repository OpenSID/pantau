<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class TracksidAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->verifikasiToken($request)) {
            return $next($request);
        } elseif ($this->verifikasiHashLicense($request)) {
            return $next($request);
        }

        throw new AuthenticationException();
    }

    /**
     * Cek verifikasi token dari dev token.
     *
     * @return bool
     */
    protected function verifikasiToken(Request $request)
    {
        $devToken = config('tracksid.sandi.dev_token');
        if (empty($devToken)) {
            return false;
        }
        $token = $request->bearerToken() ?? $request->input('token') ?? '';
        return hash_equals($devToken, $token);
    }

    /**
     * Cek verifikasi token dari file license opensid.
     *
     * @return bool
     */
    protected function verifikasiHashLicense(Request $request)
    {
        $licensePath = base_path('LICENSE_OPENSID');
        if (!file_exists($licensePath)) {
            return false;
        }
        $token = $request->bearerToken() ?? $request->input('token') ?? '';
        return hash_equals(hash_file('sha256', $licensePath), $token);
    }
}
