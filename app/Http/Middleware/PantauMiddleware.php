<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PantauMiddleware
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
        // abort jika aplikasi pantau tidak ada di list.
        abort_unless(
            in_array($request->route('pantau'), ['opensid', 'opendk']),
            404
        );

        // set session provinsi
        session()->put('pantau', $request->pantau);

        return $next($request);
    }
}
