<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class WebDashboard
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
        $route = Route::current();
        $uri = $route->uri();

        if (! auth()->check()) {
            if (! Str::startsWith($uri, 'web') && $uri !== '/') {
                return redirect('web');
            }
        }

        // Change the config values here
        Config::set('adminlte', Config::get('weblte')); // example
        if (auth()->check()) {
            Config::set('adminlte.dashboard_url', 'dashboard');
        }

        return $next($request);
    }
}
