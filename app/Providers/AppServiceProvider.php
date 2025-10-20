<?php
namespace App\Providers;

use App\Models\AppVersion;
use App\Models\UptProfile;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
            $latestAppVersion = Cache::rememberForever('version', function () {
                return AppVersion::latest('release_date')->first();
            });

            View::share('latestAppVersion', $latestAppVersion);
        }
        View::composer(['layouts.partials._sidebar', 'layouts.partials._navbar'], function ($view) {
            // Cek dulu apakah tabelnya ada, untuk menghindari error saat migrasi awal
            if (Schema::hasTable('upt_profiles')) {
                // Ambil data dari cache selamanya. Jika cache kosong, jalankan fungsi
                // untuk mengambil data dari DB lalu simpan ke cache.
                $uptProfile = Cache::rememberForever('upt_profile', function () {
                    // Ambil baris pertama, atau buat data default jika tabelnya kosong
                    return UptProfile::firstOrCreate(
                        ['id' => 1],
                        [
                            'app_name' => config('app.app_name', 'SiPKS'),
                            'name'     => config('app.name', 'UPT Perparkiran Pekanbaru'),
                        ]
                    );
                });

                // Kirim variabel $uptProfile ke view (_sidebar atau _navbar)
                $view->with('uptProfile', $uptProfile);
            }
        });
    }
}
