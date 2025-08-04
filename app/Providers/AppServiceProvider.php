<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View; // ✅ 1. Import View facade
use Illuminate\Support\Facades\Cache; // ✅ 2. Import Cache facade
use Illuminate\Support\Facades\Schema; // ✅ 3. Import Schema facade
use App\Models\AppVersion;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale Carbon
        Carbon::setLocale(App::getLocale()); // Ini wajib
        Paginator::useBootstrapFive();
        if (Schema::hasTable('app_versions')) {
            $latestAppVersion = Cache::remember('latest_app_version', 60, function () {
                return AppVersion::latest('release_date')->first();
            });

            View::share('latestAppVersion', $latestAppVersion);
        }
    }
}
