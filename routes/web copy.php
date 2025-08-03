<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LeaderController;
use App\Http\Controllers\Admin\FieldCoordinatorController;
use App\Http\Controllers\MasterData\RoadSectionController;
use App\Http\Controllers\MasterData\ParkingLocationController;
use App\Http\Controllers\MasterData\AgreementController;
use App\Http\Controllers\MasterData\DepositTransactionController;
use App\Http\Controllers\MasterData\DepositReportController;
use App\Http\Controllers\MasterData\AgreementHistoryController;
use App\Http\Controllers\MasterData\BludBankAccountController;
use App\Http\Controllers\PublicVerificationController;

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/verify/agreement/{code}', [PublicVerificationController::class, 'verifyAgreement'])->name('public.agreement.verify');

// Rute dashboard umum (jika ada) atau redirect setelah login
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- START: Route Khusus Admin ---
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard Admin
            Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

            // ✅ TAMBAHKAN ROUTE PENCARIAN INI
            Route::post('/agreements/find', [DashboardController::class, 'findAgreement'])->name('agreements.find');

            // Manajemen Pengguna (CRUD)
            Route::resource('users', UserController::class);
            Route::resource('leaders', LeaderController::class);
            Route::resource('field-coordinators', FieldCoordinatorController::class);
            // ✅ TAMBAHKAN BARIS INI
            Route::resource('blud-bank-accounts', BludBankAccountController::class);
            // ✅ DENGAN ROUTE BARU YANG LEBIH LENGKAP:
            Route::get('backup', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backup.index');
            Route::post('backup', [\App\Http\Controllers\Admin\BackupController::class, 'store'])->name('backup.store');
            Route::get('backup/{backup}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backup.download');
            Route::delete('backup/{backup}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backup.destroy');
            // ✅ TAMBAHKAN DUA ROUTE BARU INI UNTUK PROFIL UPT
            Route::get('upt-profile', [\App\Http\Controllers\Admin\UptProfileController::class, 'index'])->name('upt-profile.index');
            Route::post('upt-profile', [\App\Http\Controllers\Admin\UptProfileController::class, 'update'])->name('upt-profile.update');
        });

    // LEADER ROUTES
    Route::middleware('role:leader')->group(function () {
        Route::get('/leader/dashboard', [DashboardController::class, 'leaderDashboard'])->name('leader.dashboard');
        // Tambahkan rute leader lainnya di sini
    });

    // FIELD COORDINATOR ROUTES
    Route::middleware('role:field_coordinator')->group(function () {
        Route::get('/field-coordinator/dashboard', [DashboardController::class, 'fieldCoordinatorDashboard'])->name('field_coordinator.dashboard');
        // Tambahkan rute field coordinator lainnya di sini
    });

    // STAFF ROUTES
    // Ganti method untuk route STAFF PKS
    Route::middleware('role:staff_pks')->group(function () {
        Route::get('/staff-pks/dashboard', [DashboardController::class, 'staffPksDashboard'])->name('staff-pks.dashboard');
    });

    // Ganti method untuk route STAFF KEUANGAN
    Route::middleware('role:staff_keu')->group(function () {
        Route::get('/staff-keuangan/dashboard', [DashboardController::class, 'staffKeuDashboard'])->name('staff-keuangan.dashboard');
    });

    // Ganti method untuk route LEADER
    Route::middleware('role:leader')->group(function () {
        Route::get('/leader/dashboard', [DashboardController::class, 'leaderDashboard'])->name('leader.dashboard');
        Route::get('/search-agreements-ajax', [DashboardController::class, 'searchAgreementsAjax'])->name('leader.search-agreements-ajax');
    });

    // START: ROUTES UNTUK ADMIN DAN STAFF PKS (MasterData)
    Route::middleware(['auth', 'role:admin,staff_pks,staff_keu'])->prefix('masterdata')->name('masterdata.')->group(function () {
        // Routes untuk Manajemen Ruas Jalan
        Route::resource('road-sections', RoadSectionController::class);

        // Routes untuk Manajemen Lokasi Parkir Ruas Jalan
        Route::resource('parking-locations', ParkingLocationController::class);

        // ✅ TAMBAHKAN ROUTE BARU INI
        Route::get('get-road-sections-by-zone/{zone}', [ParkingLocationController::class, 'getRoadSectionsByZone'])
            ->name('road-sections.getByZone');

        Route::get('get-parking-locations-by-road-section/{roadSectionId}', [ParkingLocationController::class, 'getParkingLocationsByRoadSection'])
            ->name('get-parking-locations-by-road-section');

        // Routes untuk Manajemen Perjanjian Kerjasama
        Route::resource('agreements', AgreementController::class);

        // START: Route untuk mengeluarkan lokasi parkir dari perjanjian
        Route::post('agreements/{agreement}/detach-parking-location/{parkingLocation}', [AgreementController::class, 'detachParkingLocation'])
            ->name('agreements.detach-parking-location');

        // START: Route AJAX untuk pencarian perjanjian aktif (untuk Select2)
        Route::get('search-active-agreements', [DepositTransactionController::class, 'searchActiveAgreements'])
            ->name('search-active-agreements');
        // END: Route AJAX untuk pencarian perjanjian aktif

        // START: Route untuk Generate PDF Perjanjian (Ini yang ditambahkan/dipastikan ada)
        Route::get('agreements/{agreement}/pdf', [AgreementController::class, 'generatePdf'])->name('agreements.pdf');
        // END: Route untuk Generate PDF Perjanjian

        // START: Route untuk Riwayat Perjanjian
        Route::get('agreement-histories', [AgreementHistoryController::class, 'index'])->name('agreement-histories.index');
        // END: Route untuk Riwayat Perjanjian

        // START: Route untuk Generate PDF Riwayat Perjanjian
        Route::get('agreement-histories/{history}/pdf', [AgreementHistoryController::class, 'generatePdf'])->name('agreement-histories.pdf');
        // END: Route untuk Generate PDF Riwayat Perjanjian


    });
    // END: ROUTES BARU UNTUK ADMIN DAN STAFF (MasterData)

    //Role Admin, Staff Keuangan
    Route::middleware(['auth', 'role:admin,staff_keu'])->prefix('masterdata')->name('masterdata.')->group(function () {

        // Routes untuk Manajemen Transaksi Setoran
        Route::resource('deposit-transactions', DepositTransactionController::class);

        // Route untuk validasi setoran (bisa diakses Admin/Leader)
        Route::post('deposit-transactions/{depositTransaction}/validate', [DepositTransactionController::class, 'validateDeposit'])
            ->name('deposit-transactions.validate');

        // ✅ TAMBAHKAN ROUTE BARU INI
        Route::get('deposit-transactions/{depositTransaction}/pdf', [DepositTransactionController::class, 'generatePdf'])
            ->name('deposit-transactions.pdf');

        // START: Route untuk Laporan Deposit
        Route::get('deposit-reports', [DepositReportController::class, 'index'])->name('deposit-reports.index');
        // END: Route untuk Laporan Deposit

        // Pastikan nama route ini unik dan berbeda dari index
        Route::get('deposit-reports/pdf', [DepositReportController::class, 'generatePdf'])->name('deposit-reports.pdf');
        // END: Route untuk Laporan Deposit (Cetak PDF)

        // Route AJAX untuk mengambil lokasi parkir berdasarkan ruas jalan

        // Routes untuk Manajemen Transaksi Setoran
        Route::resource('deposit-transactions', DepositTransactionController::class);

        // Route untuk validasi setoran (bisa diakses Admin/Leader)
        Route::post('deposit-transactions/{depositTransaction}/validate', [DepositTransactionController::class, 'validateDeposit'])
            ->name('deposit-transactions.validate');

        Route::get('check-transaction/{agreement}', [DepositTransactionController::class, 'checkExistingTransaction'])
            ->name('check-existing-transaction');
    });
    // End Role Admin, Staff Keu

    // Rute dashboard default jika tidak cocok dengan role spesifik
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile/settings', [\App\Http\Controllers\ProfileSettingController::class, 'edit'])->name('profile.settings');
    Route::patch('/profile/settings', [\App\Http\Controllers\ProfileSettingController::class, 'updateProfile'])->name('profile.update.custom');
    Route::put('/password/settings', [\App\Http\Controllers\ProfileSettingController::class, 'updatePassword'])->name('password.update.custom');
});

require __DIR__ . '/auth.php';
