<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
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
    }

    protected function bootQueryBuilderMacros()
    {
        // Helper function untuk konversi SQL dengan bindings
        $toRawSqlFunction = function () {
            $sql = $this->toSql();
            $bindings = $this->getBindings();

            foreach ($bindings as $binding) {
                if (is_string($binding)) {
                    $binding = "'" . str_replace("'", "''", $binding) . "'";
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
