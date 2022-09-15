<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
        $this->blade();
    }

    protected function blade()
    {
        Blade::directive('selected', function ($condition) {
            return "<?php if({$condition}): echo 'selected'; endif; ?>";
        });
    }
}
