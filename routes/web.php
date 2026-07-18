<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\DepositTargetController;
use App\Http\Controllers\Admin\FieldCoordinatorController;
use App\Http\Controllers\Admin\LeaderController;
use App\Http\Controllers\Admin\LocationRequestController as AdminLocationRequestController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TreasurerController;
use App\Http\Controllers\Admin\UptProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SurveyParkingLocationController;
use App\Http\Controllers\Admin\JukirController;
use App\Http\Controllers\AppVersionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldCoordinator\LocationRequestController;
use App\Http\Controllers\MasterData\AgreementController;
use App\Http\Controllers\MasterData\AgreementHistoryController;
use App\Http\Controllers\MasterData\BludBankAccountController;
use App\Http\Controllers\MasterData\DepositReportController;
use App\Http\Controllers\MasterData\DepositTransactionController;
use App\Http\Controllers\MasterData\ParkingLocationController;
use App\Http\Controllers\MasterData\RoadSectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileSettingController;
use App\Http\Controllers\PublicVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute Publik
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});
Route::get('/verify/agreement/{code}', [PublicVerificationController::class, 'verifyAgreement'])->name('public.agreement.verify');

// Detail Jukir (Public)
Route::get('/jukir/{id_jukir}', [\App\Http\Controllers\PublicJukirComplaintController::class, 'show'])->name('public.jukir.show');

// Pengaduan Jukir (Public)
Route::get('/jukir/{id_jukir}/pengaduan', [\App\Http\Controllers\PublicJukirComplaintController::class, 'create'])->name('public.jukir.complaint.create');
Route::post('/jukir/{id_jukir}/pengaduan', [\App\Http\Controllers\PublicJukirComplaintController::class, 'store'])->name('public.jukir.complaint.store');
Route::get('/jukir/{id_jukir}/pengaduan/success', [\App\Http\Controllers\PublicJukirComplaintController::class, 'success'])->name('public.jukir.complaint.success');


// Rute yang Memerlukan Autentikasi
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (Redirector)
    // Shortcuts
    Route::get('/user-shortcuts', [\App\Http\Controllers\UserShortcutController::class, 'getAvailable'])->name('shortcuts.get');
    Route::post('/user-shortcuts', [\App\Http\Controllers\UserShortcutController::class, 'saveShortcuts'])->name('shortcuts.save');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil Pengguna
    Route::get('/profile/settings', [ProfileSettingController::class, 'edit'])->name('profile.settings');
    Route::patch('/profile/settings', [ProfileSettingController::class, 'updateProfile'])->name('profile.update.custom');
    Route::put('/password/settings', [ProfileSettingController::class, 'updatePassword'])->name('password.update.custom');

    Route::get('/profile', [ProfileSettingController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/delete-image', [ProfileController::class, 'deleteImage'])->name('profile.delete-image');

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
        Route::resource('location-requests', LocationRequestController::class);
    });
    // ✅ PERBAIKAN: Middleware-nya pakai role 'bendahara' sesuai isi tabel Users
    Route::middleware('role:treasurer')->prefix('treasurer')->name('treasurer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'treasurerDashboard'])->name('dashboard');
    });

    Route::middleware('role:staff_kta_jukir')->prefix('staff-kta-jukir')->name('staff-kta-jukir.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'staffKtaJukirDashboard'])->name('dashboard');
    });

    // --- MANAJEMEN DATA (CRUD) - HANYA UNTUK ADMIN & STAFF ---
    Route::middleware(['auth', 'verified'])->prefix('masterdata')->name('masterdata.')->group(function () {

        // --- Rute khusus Admin & Staff Keuangan ---
        // Hanya admin dan staff_keu yang bisa mengelola transaksi dan laporan.
        Route::middleware('role:admin,leader,staff_keu,treasurer')->group(function () {
            Route::resource('deposit-transactions', DepositTransactionController::class); // Semua aksi CRUD + show
            Route::post('deposit-transactions/{depositTransaction}/validate', [DepositTransactionController::class, 'validateDeposit'])->name('deposit-transactions.validate');
            Route::get('deposit-transactions/{depositTransaction}/pdf', [DepositTransactionController::class, 'generatePdf'])->name('deposit-transactions.pdf');
            Route::get('check-transaction/{agreement}', [DepositTransactionController::class, 'checkExistingTransaction'])->name('check-existing-transaction');

            Route::get('deposit-reports', [DepositReportController::class, 'index'])->name('deposit-reports.index');
            Route::get('deposit-reports/pdf', [DepositReportController::class, 'generatePdf'])->name('deposit-reports.pdf');

            Route::get('/deposit-targets', [DepositTargetController::class, 'index'])->name('deposit-targets.index');
            Route::post('/deposit-targets', [DepositTargetController::class, 'store'])->name('deposit-targets.store');
        });

        // ✅ Peta Wilayah Parkir (Dapat diakses oleh admin, leader, staff_pks, treasurer)
        Route::middleware('role:admin,leader,staff_pks,treasurer')->group(function () {
            Route::get('/parking-locations-map', [ParkingLocationController::class, 'mapView'])->name('parking-locations.map');
        });

        // --- Rute untuk Tiga Role (Admin, Staff Keu, Staff PKS) ---
        // Role-role ini bisa mengelola data dasar seperti ruas jalan, lokasi, dan perjanjian.
        Route::middleware('role:admin,leader,staff_keu,staff_pks')->group(function () {
            Route::resource('road-sections', RoadSectionController::class)->except(['show']);
            Route::get('parking-locations/import', [ParkingLocationController::class, 'importCreate'])
                ->name('parking-locations.importCreate');
            Route::post('parking-locations/import', [ParkingLocationController::class, 'importStore'])
                ->name('parking-locations.importStore');
            Route::delete('parking-locations/bulk-delete', [ParkingLocationController::class, 'bulkDeleteUnused'])
                ->name('parking-locations.bulkDeleteUnused');
            Route::patch('parking-locations/{parking_location}/toggle-status', [ParkingLocationController::class, 'toggleStatus'])
                ->name('parking-locations.toggleStatus');
            Route::resource('parking-locations', ParkingLocationController::class)->except(['show']);

            // ✅ Rute AJAX harus di atas resource
            Route::get('agreements/get-road-sections/{zone}', [AgreementController::class, 'getRoadSectionsByZone']);
            Route::get('agreements/get-parking-locations/{roadSectionId}', [AgreementController::class, 'getParkingLocationsByRoadSection']);
            Route::resource('agreements', AgreementController::class)->except(['show']);
            Route::get('agreements/{agreement}/renew', [AgreementController::class, 'renew'])->name('agreements.renew');
            Route::post('agreements/{agreement}/renew', [AgreementController::class, 'storeRenewal'])->name('agreements.storeRenewal');
            Route::get('agreement-histories', [AgreementHistoryController::class, 'index'])->name('agreement-histories.index');
            Route::post('agreements/{agreement}/detach-parking-location/{parkingLocation}', [AgreementController::class, 'detachParkingLocation'])->name('agreements.detach-parking-location');
        });

        Route::middleware('role:admin,staff_pks')->group(function () {
            Route::get('location-requests', [AdminLocationRequestController::class, 'index'])->name('location-requests.index');
            Route::get('location-requests/{locationRequest}', [AdminLocationRequestController::class, 'show'])->name('location-requests.show');

            // Aksi Eksekusi
            Route::post('location-requests/{locationRequest}/review', [AdminLocationRequestController::class, 'storeReview'])->name('location-requests.review');
            Route::post('location-requests/{locationRequest}/approve', [AdminLocationRequestController::class, 'approve'])->name('location-requests.approve');
            Route::post('location-requests/{locationRequest}/reject', [AdminLocationRequestController::class, 'reject'])->name('location-requests.reject');
        });

        // --- Rute yang bisa diakses SEMUA role (termasuk Leader untuk view & AJAX) ---
        // Ini untuk aksi lihat-saja (view-only) dan kebutuhan AJAX.
        Route::middleware('role:admin,staff_keu,staff_pks,leader,field_coordinator')->group(function () {
            Route::get('road-sections/{road_section}', [RoadSectionController::class, 'show'])->name('road-sections.show');
            Route::get('parking-locations/{parking_location}', [ParkingLocationController::class, 'show'])->name('parking-locations.show');
            Route::get('agreements/{agreement}', [AgreementController::class, 'show'])->name('agreements.show');
            Route::get('agreements/{agreement}/pdf-history', [AgreementController::class, 'showPdfHistory'])->name('agreements.pdf-history');
            Route::get('agreements/{agreement}/pdf', [AgreementController::class, 'generatePdf'])->name('agreements.pdf');
            Route::post('agreements/{agreement}/upload-scan', [AgreementController::class, 'uploadSignedDocument'])->name('agreements.upload-scan');
        });
    });

    // --- RUTE-ROUTE ADMIN LAINNYA ---
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard Admin
            Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

            // Laporan Titik Lokasi Parkir
            Route::get('parking-locations/report', [ParkingLocationController::class, 'report'])->name('parking-locations.report');
            Route::get('parking-locations/report/export-pdf', [ParkingLocationController::class, 'exportPdf'])->name('parking-locations.report.export-pdf');
            Route::get('parking-locations/report/export-excel', [ParkingLocationController::class, 'exportExcel'])->name('parking-locations.report.export-excel');

            // Pencarian PKS
            Route::post('/agreements/find', [DashboardController::class, 'findAgreement'])->name('agreements.find');

            // ✅ PERBAIKAN: Definisikan rute spesifik SEBELUM resource
            // --- Users ---
            Route::resource('users', UserController::class);

            // --- Leaders ---
            Route::resource('leaders', LeaderController::class);
            Route::post('leaders/{leader}/extend', [LeaderController::class, 'extend'])->name('leaders.extend');
            Route::patch('leader/{leader}/toggle-status', [LeaderController::class, 'toggleStatus'])->name('leaders.toggle-status');

            // --- Treasurers (Bendahara) ---
            Route::resource('treasurers', TreasurerController::class);
            Route::post('treasurers/{treasurer}/extend', [TreasurerController::class, 'extend'])->name('treasurers.extend');
            Route::patch('treasurers/{treasurer}/toggle-status', [TreasurerController::class, 'toggleStatus'])->name('treasurers.toggle-status');

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
            Route::put('app-versions/manage/{appVersion}', [AppVersionController::class, 'update'])->name('app-versions.update');
            Route::delete('app-versions/manage/{appVersion}', [AppVersionController::class, 'destroy'])->name('app-versions.destroy');

            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

            // --- Survey Lokasi Parkir ---
            Route::resource('survey-parking-locations', SurveyParkingLocationController::class);
        });

    // --- RUTE-ROUTE ADMIN & STAFF PKS (MANAGE USERS/KORLAP) ---
    Route::middleware('role:admin,staff_pks')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // --- Field Coordinators ---
            Route::resource('field-coordinators', FieldCoordinatorController::class);
            Route::patch('field-coordinators/{field_coordinator}/toggle-status', [FieldCoordinatorController::class, 'toggleStatus'])->name('field-coordinators.toggle-status');
            Route::patch('field-coordinators/{field_coordinator}/update-login', [FieldCoordinatorController::class, 'updateLogin'])->name('field-coordinators.update-login');
        });

    // --- RUTE JUKIR (Admin & Staff KTA Jukir) ---
    Route::middleware('role:admin,staff_kta_jukir')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('jukirs/import', [JukirController::class, 'importCreate'])->name('jukirs.importCreate');
            Route::post('jukirs/import', [JukirController::class, 'importStore'])->name('jukirs.importStore');
            Route::get('jukirs/{jukir}/print-kta', [JukirController::class, 'printKta'])->name('jukirs.print-kta');
            Route::resource('jukirs', JukirController::class)->except(['create', 'edit']);
            Route::resource('jukir-violations', \App\Http\Controllers\Admin\JukirViolationController::class)->only(['store', 'destroy']);
        });

    // --- RUTE-ROUTE AJAX (Bisa diakses oleh beberapa role) ---
    Route::middleware('role:admin,staff_pks,staff_keu,leader,treasurer')->prefix('masterdata')->name('masterdata.')->group(function () {
        Route::get('get-road-sections-by-zone/{zone}', [ParkingLocationController::class, 'getRoadSectionsByZone'])->name('road-sections.getByZone');
        Route::get('get-parking-locations-by-road-section/{roadSectionId}', [ParkingLocationController::class, 'getParkingLocationsByRoadSection'])->name('get-parking-locations-by-road-section');
        Route::get('search-active-agreements', [DepositTransactionController::class, 'searchActiveAgreements'])
            ->name('search-active-agreements');
        Route::get('search-agreements-ajax', [DashboardController::class, 'searchAgreementsAjax'])->name('search-agreements-ajax');
        Route::get('search-locations-ajax', [DashboardController::class, 'searchParkingLocationsAjax'])->name('search-locations-ajax');
        Route::get('search-deposits-ajax', [DashboardController::class, 'searchDepositsAjax'])->name('search-deposits-ajax');
        Route::get('global-search', [DashboardController::class, 'globalSearch'])->name('global-search');
    });

    Route::get('/app-versions', [AppVersionController::class, 'index'])->name('app.versions');
});

require __DIR__.'/auth.php';
