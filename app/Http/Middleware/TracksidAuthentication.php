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
        $token = $request->bearerToken() ?? $request->input('token') ?? '';
        return hash_equals(config('tracksid.sandi.dev_token'), $token);
    }

    /**
     * Cek verifikasi token dari file license opensid.
     *
     * @return bool
     */
    protected function verifikasiHashLicense(Request $request)
    {
        return hash_equals(hash_file('sha256', base_path('LICENSE_OPENSID')), $request->bearerToken() ?? $request->input('token') ?? '');
    }
}
