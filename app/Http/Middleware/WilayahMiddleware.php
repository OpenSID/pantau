<?php

namespace App\Http\Middleware;

use App\Models\Wilayah;
use Closure;
use Illuminate\Http\Request;

class WilayahMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // abort jika provinsi tidak ada di list config.
        abort_unless(
            array_key_exists($provinsi = $request->route('provinsi'), pantau_wilayah_khusus()),
            404
        );

        // set session provinsi
        session()->put(
            'provinsi',
            Wilayah::provinsi()
                ->where('kode_prov', $provinsi)
                ->first()
        );

        return $next($request);
    }
}
