<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class WebDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $route = Route::current();        
        if (!auth()->check() && $route->uri !== 'web') {
            return redirect('web');
        }
        // Change the config values here        
        if($route->uri === 'web') {
            Config::set('adminlte', Config::get('weblte')); // example
        }        
        return $next($request);
    }    
}