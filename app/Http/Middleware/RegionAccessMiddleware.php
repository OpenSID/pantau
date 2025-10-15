<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RegionAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Share user region access information with all views
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->hasRole('Admin Wilayah')) {
                $regionAccess = $user->userRegionAccess;
                View::share('userRegionAccess', $regionAccess);
                View::share('isAdminWilayah', true);
            } else {
                View::share('userRegionAccess', null);
                View::share('isAdminWilayah', false);
            }
        }

        return $next($request);
    }
}
