<?php

namespace App\Providers;

use App\Support\BusinessSettings;
use App\Support\HomepageSections;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BusinessSettings::class);
        $this->app->scoped(HomepageSections::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            if (str_starts_with($view->name(), 'errors.')) {
                return;
            }

            $view->with('businessSettings', app(BusinessSettings::class));
            $view->with('homepageSections', app(HomepageSections::class)->visibility());
        });
    }
}
