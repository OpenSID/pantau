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
        } elseif ($this->verifikasiHashLicenseLama($request)) {
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
        return in_array(config('tracksid.sandi.dev_token'), [$request->bearerToken(), $request->input('token')]);
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

    /**
     * Cek verifikasi token dari file license opensid denganm cara lama.
     *
     * @return bool
     */
    protected function verifikasiHashLicenseLama(Request $request)
    {
        return hash_equals(sha1(''), $request->bearerToken() ?? $request->input('token') ?? '');
    }
}
