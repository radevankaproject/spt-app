<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LeaderController;
use App\Http\Controllers\Admin\UptProfileController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\FieldCoordinatorController;
use App\Http\Controllers\MasterData\RoadSectionController;
use App\Http\Controllers\MasterData\ParkingLocationController;
use App\Http\Controllers\MasterData\AgreementController;
use App\Http\Controllers\MasterData\DepositTransactionController;
use App\Http\Controllers\MasterData\DepositReportController;
use App\Http\Controllers\MasterData\AgreementHistoryController;
use App\Http\Controllers\MasterData\BludBankAccountController;
use App\Http\Controllers\PublicVerificationController;
use App\Http\Controllers\ProfileSettingController;
use App\Http\Controllers\AppVersionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute Publik
Route::get('/', function () {
    return view('auth/login');
});
Route::get('/verify/agreement/{code}', [PublicVerificationController::class, 'verifyAgreement'])->name('public.agreement.verify');


// Rute yang Memerlukan Autentikasi
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (Redirector)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil Pengguna
    Route::get('/profile/settings', [ProfileSettingController::class, 'edit'])->name('profile.settings');
    Route::patch('/profile/settings', [ProfileSettingController::class, 'updateProfile'])->name('profile.update.custom');
    Route::put('/password/settings', [ProfileSettingController::class, 'updatePassword'])->name('password.update.custom');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- DASHBOARD SPESIFIK UNTUK SETIAP ROLE ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    });
    Route::middleware('role:leader')->prefix('leader')->name('leader.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'leaderDashboard'])->name('dashboard');
    });
    Route::middleware('role:staff_pks')->prefix('staff-pks')->name('staff-pks.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'staffPksDashboard'])->name('dashboard');
    });
    Route::middleware('role:staff_keu')->prefix('staff-keuangan')->name('staff-keuangan.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'staffKeuDashboard'])->name('dashboard');
    });
    Route::middleware('role:field_coordinator')->prefix('field-coordinator')->name('field_coordinator.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'fieldCoordinatorDashboard'])->name('dashboard');
    });


    // --- MANAJEMEN DATA (CRUD) - HANYA UNTUK ADMIN & STAFF ---
    Route::middleware('role:admin,staff_pks,staff_keu')->prefix('masterdata')->name('masterdata.')->group(function () {
        Route::resource('road-sections', RoadSectionController::class)->except(['show']);
        Route::resource('parking-locations', ParkingLocationController::class)->except(['show']);
        Route::resource('agreements', AgreementController::class)->except(['show']);
        Route::get('agreement-histories', [AgreementHistoryController::class, 'index'])->name('agreement-histories.index');
        Route::post('agreements/{agreement}/detach-parking-location/{parkingLocation}', [AgreementController::class, 'detachParkingLocation'])->name('agreements.detach-parking-location');
        Route::resource('deposit-transactions', DepositTransactionController::class)->except(['show']);
        Route::post('deposit-transactions/{depositTransaction}/validate', [DepositTransactionController::class, 'validateDeposit'])
            ->name('deposit-transactions.validate');
        Route::get('deposit-reports', [DepositReportController::class, 'index'])->name('deposit-reports.index');
        Route::get('deposit-reports/pdf', [DepositReportController::class, 'generatePdf'])->name('deposit-reports.pdf');
        Route::get('check-transaction/{agreement}', [DepositTransactionController::class, 'checkExistingTransaction'])
            ->name('check-existing-transaction');
    });


    // --- AKSES LIHAT (VIEW-ONLY) - TERMASUK UNTUK LEADER ---
    Route::middleware('role:admin,staff_pks,staff_keu,leader')->prefix('masterdata')->name('masterdata.')->group(function () {
        Route::get('parking-locations/{parking_location}', [ParkingLocationController::class, 'show'])->name('parking-locations.show');
        Route::get('deposit-transactions/{deposit_transaction}', [DepositTransactionController::class, 'show'])->name('deposit-transactions.show');
        Route::get('deposit-transactions/{depositTransaction}/pdf', [DepositTransactionController::class, 'generatePdf'])
            ->name('deposit-transactions.pdf');
        Route::get('agreements/{agreement}', [AgreementController::class, 'show'])->name('agreements.show');
        // Route::get('agreements/{agreement}/pdf-history', [AgreementController::class, 'showPdfHistory'])->name('agreements.pdf-history');
        Route::get('agreements/{agreement}/pdf-history', [AgreementController::class, 'showPdfHistory'])->name('agreements.pdf-history');
        Route::get('agreements/{agreement}/pdf', [AgreementController::class, 'generatePdf'])->name('agreements.pdf');
        // Route::get('agreements/{agreement}/pdf', [AgreementController::class, 'generatePdf'])->name('agreements.pdf');
    });


    // --- RUTE-ROUTE ADMIN LAINNYA ---
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard Admin
            Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

            // Pencarian PKS
            Route::post('/agreements/find', [DashboardController::class, 'findAgreement'])->name('agreements.find');

            // ✅ PERBAIKAN: Definisikan rute spesifik SEBELUM resource
            // --- Users ---
            Route::get('users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
            Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
            Route::resource('users', UserController::class);

            // --- Leaders ---
            Route::get('leaders/trashed', [LeaderController::class, 'trashed'])->name('leaders.trashed');
            Route::patch('leaders/{id}/restore', [LeaderController::class, 'restore'])->name('leaders.restore');
            Route::resource('leaders', LeaderController::class);

            // --- Field Coordinators ---
            Route::get('field-coordinators/trashed', [FieldCoordinatorController::class, 'trashed'])->name('field-coordinators.trashed');
            Route::patch('field-coordinators/{id}/restore', [FieldCoordinatorController::class, 'restore'])->name('field-coordinators.restore');
            Route::resource('field-coordinators', FieldCoordinatorController::class);

            // --- Rute lainnya ---
            Route::resource('blud-bank-accounts', BludBankAccountController::class);

            Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
            Route::post('backup', [BackupController::class, 'store'])->name('backup.store');
            Route::get('backup/{backup}/download', [BackupController::class, 'download'])->name('backup.download');
            Route::delete('backup/{backup}', [BackupController::class, 'destroy'])->name('backup.destroy');

            Route::get('upt-profile', [UptProfileController::class, 'index'])->name('upt-profile.index');
            Route::post('upt-profile', [UptProfileController::class, 'update'])->name('upt-profile.update');

            Route::get('app-versions/manage', [AppVersionController::class, 'manage'])->name('app-versions.manage');
            Route::post('app-versions/manage', [AppVersionController::class, 'store'])->name('app-versions.store');
        });


    // --- RUTE-ROUTE AJAX (Bisa diakses oleh beberapa role) ---
    Route::middleware('role:admin,staff_pks,staff_keu,leader')->prefix('masterdata')->name('masterdata.')->group(function () {
        Route::get('get-road-sections-by-zone/{zone}', [ParkingLocationController::class, 'getRoadSectionsByZone'])->name('road-sections.getByZone');
        Route::get('get-parking-locations-by-road-section/{roadSectionId}', [ParkingLocationController::class, 'getParkingLocationsByRoadSection'])->name('get-parking-locations-by-road-section');
        Route::get('search-active-agreements', [DepositTransactionController::class, 'searchActiveAgreements'])->name('search-active-agreements');
        Route::get('search-agreements-ajax', [DashboardController::class, 'searchAgreementsAjax'])->name('search-agreements-ajax');
        Route::get('search-locations-ajax', [DashboardController::class, 'searchParkingLocationsAjax'])->name('search-locations-ajax');
        Route::get('search-deposits-ajax', [DashboardController::class, 'searchDepositsAjax'])->name('search-deposits-ajax');
    });

    Route::get('/app-versions', [AppVersionController::class, 'index'])->name('app.versions');
});

require __DIR__ . '/auth.php';
