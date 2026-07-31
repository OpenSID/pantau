<?php

use App\Http\Middleware\TracksidAuthentication;
use App\Http\Middleware\WebDashboard;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        then: function () {
            Route::prefix('api/v1')
                ->middleware('api')
                ->group(base_path('routes/apiv1.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->preventRequestsDuringMaintenance(except: ['api*']);
        $middleware->alias([
            'auth'          => \App\Http\Middleware\Authenticate::class,
            'guest'         => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'abilities'     => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability'       => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'tracksid'      => TracksidAuthentication::class,
            'web.dashboard' => WebDashboard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);
    })
    ->create();
