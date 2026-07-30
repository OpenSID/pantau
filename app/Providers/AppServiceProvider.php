<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

// New imports
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the Excel facade alias (moved from config/app.php)
        AliasLoader::getInstance()->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootLogQuery();
        $this->bootQueryBuilderMacros();

        // Rate Limiter from RouteServiceProvider
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Gates from AuthServiceProvider
        Gate::define('is-admin', function (Authenticatable $user) {
            return $user->hasRole('Administrator');
        });

        Gate::define('is-admin-wilayah', function (Authenticatable $user) {
            return $user->hasRole('Admin Wilayah');
        });

        // Event listeners from EventServiceProvider
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            if (Auth::check() === false) {
                $event->menu->add([
                    'text' => '',
                    'url' => 'login',
                    'icon' => 'fas fa-sign-in-alt',
                    'topnav_right' => true,
                ]);
            }

            foreach (pantau_wilayah_khusus() as $key => $val) {
                $event->menu->addIn('khusus', [
                    'text' => $val,
                    'url' => "sesi/provinsi/{$key}",
                    'active' => session('provinsi.kode_prov') == $key ? true : false,
                ]);
            }

            if (session('pantau') == 'opensid' || session('pantau') == null) {
                foreach (config('opensid.menu') as $key => $val) {
                    $event->menu->addBefore('utama', $val);
                }
            }

            if (session('pantau') == 'opendk') {
                foreach (config('opendk.menu') as $key => $val) {
                    $event->menu->addBefore('utama', $val);
                }
                foreach (config('opendk.title') as $key => $val) {
                    Config::set("adminlte.{$key}", $val);
                }
            }
        });
    }

    protected function bootQueryBuilderMacros()
    {
        // Helper function untuk konversi SQL dengan bindings
        $toRawSqlFunction = function () {
            $sql = $this->toSql();
            $bindings = $this->getBindings();

            foreach ($bindings as $binding) {
                if (is_string($binding)) {
                    $binding = "'".str_replace("'", "''", $binding)."'";
                } elseif (is_bool($binding)) {
                    $binding = $binding ? '1' : '0';
                } elseif (is_null($binding)) {
                    $binding = 'NULL';
                } elseif (is_numeric($binding)) {
                    $binding = (string) $binding;
                }

                $sql = preg_replace('/\?/', $binding, $sql, 1);
            }

            return $sql;
        };

        // Macro untuk Query Builder
        Builder::macro('toBoundSql', $toRawSqlFunction);
        Builder::macro('toRawSql', $toRawSqlFunction);
        Builder::macro('filterWilayah', function ($request) {
            return $this->when($request->kode_provinsi, function ($query) use ($request) {
                $query->where('kode_provinsi', $request->kode_provinsi);
            })->when($request->kode_kabupaten, function ($query) use ($request) {
                $query->where('kode_kabupaten', $request->kode_kabupaten);
            })->when($request->kode_kecamatan, function ($query) use ($request) {
                $query->where('kode_kecamatan', $request->kode_kecamatan);
            });
        });
        // Macro untuk Eloquent Builder
        EloquentBuilder::macro('toBoundSql', $toRawSqlFunction);
        EloquentBuilder::macro('toRawSql', $toRawSqlFunction);
    }

    protected function bootLogQuery()
    {
        if ($this->app->environment('local')) {
            Event::listen(QueryExecuted::class, function ($query) {
                $bindings = collect($query->bindings)->map(function ($param) {
                    if (is_numeric($param)) {
                        return $param;
                    } else {
                        return "'$param'";
                    }
                });

                $this->app->log->debug(Str::replaceArray('?', $bindings->toArray(), $query->sql));
            });
        }
    }
}
