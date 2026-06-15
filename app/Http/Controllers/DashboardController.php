<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\BludBankAccount;
use App\Models\DepositTransaction;
use App\Models\FieldCoordinator;
use App\Models\Leader;
use App\Models\LocationRequest;
use App\Models\ParkingLocation;
use App\Models\RoadSection;
use App\Models\Treasurer;
use App\Models\UptProfile;
use App\Models\User;
use App\Models\YearlyDepositTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk Admin dengan data yang komprehensif.
     * 
     * @return \Illuminate\View\View
     */
    public function adminDashboard()
    {
        // --- 1. Data untuk Info Cards ---
        $currentLeader = Leader::with('user')->latest()->first();
        $startDate = $currentLeader ? Carbon::parse($currentLeader->start_date) : now();
        $activeBankAccount = BludBankAccount::where('is_active', true)->first();
        $currentYearValidatedDeposit = DepositTransaction::where('is_validated', true)
            ->whereYear('deposit_date', now()->year)->sum('amount');

        // --- 1b. Quick Stats Baru ---
        $totalAgreements = Agreement::where('status', 'active')->count();
        $totalParkingLocations = ParkingLocation::count();
        $totalRoadSections = RoadSection::count();
        $totalFieldCoordinators = FieldCoordinator::count();
        $pendingValidationsCount = DepositTransaction::where('is_validated', false)->count();
        $depositThisMonth = DepositTransaction::where('is_validated', true)
            ->whereMonth('deposit_date', now()->month)
            ->whereYear('deposit_date', now()->year)->sum('amount');

        // --- 1c. PKS Segera Berakhir (30 hari ke depan) ---
        $expiringAgreements = Agreement::with('fieldCoordinator.user')
            ->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->orderBy('end_date', 'asc')
            ->limit(5)->get();

        // --- 2. Data untuk Tabel "Terbaru" (Max 8) ---
        $recentDeposits = DepositTransaction::with('agreement.fieldCoordinator.user')
            ->whereHas('agreement')
            ->where('is_validated', true)
            ->latest('deposit_date')->limit(8)->get();

        $recentParkingLocations = ParkingLocation::with('roadSection')->latest()->limit(8)->get();
        $recentCoordinators = FieldCoordinator::with('user')->latest()->limit(8)->get();

        // --- 3. Data untuk Grafik ---
        $currentYear = now()->year;

        $monthlyDeposits = DepositTransaction::select(
            DB::raw('MONTH(deposit_date) as month'),
            DB::raw('SUM(amount) as total')
        )
            ->where('is_validated', true)->whereYear('deposit_date', $currentYear)
            ->groupBy('month')->orderBy('month')->pluck('total', 'month')->all();

        $yearlyTarget = YearlyDepositTarget::with('monthlyTargets')->where('year', $currentYear)->first();

        $mainChartLabels = [];
        $mainChartData = [];
        $targetChartData = [];

        for ($m = 1; $m <= 12; $m++) {
            $mainChartLabels[] = Carbon::create()->month($m)->translatedFormat('F');
            $mainChartData[] = isset($monthlyDeposits[$m]) ? (float) $monthlyDeposits[$m] : 0;

            if ($yearlyTarget) {
                $monthTarget = $yearlyTarget->monthlyTargets->firstWhere('month', $m);
                $targetChartData[] = $monthTarget ? (float) $monthTarget->target_amount : 0;
            } else {
                $targetChartData[] = 0;
            }
        }

        // B. Grafik Zona (Polar Area Charts)
        $roadSectionsByZone = RoadSection::select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')->pluck('total', 'zone')->all();

        $locationsByZone = ParkingLocation::join('road_sections', 'parking_locations.road_section_id', '=', 'road_sections.id')
            ->select('road_sections.zone', DB::raw('count(parking_locations.id) as total'))
            ->groupBy('road_sections.zone')->pluck('total', 'zone')->all();

        $zoneChartData = [
            'labels' => array_keys($roadSectionsByZone),
            'roadSections' => array_values($roadSectionsByZone),
            'parkingLocations' => array_values($locationsByZone),
        ];

        // C. Grafik Titik per Ruas Jalan (Bar Chart)
        $locationsPerRoadSection = RoadSection::withCount('parkingLocations')
            ->orderBy('parking_locations_count', 'desc')
            ->limit(20)->get();

        $barChartData = [
            'labels' => $locationsPerRoadSection->pluck('name'),
            'data' => $locationsPerRoadSection->pluck('parking_locations_count'),
        ];

        return view('admin.dashboard', compact(
            'currentLeader',
            'activeBankAccount',
            'startDate',
            'currentYearValidatedDeposit',
            'totalAgreements',
            'totalParkingLocations',
            'totalRoadSections',
            'totalFieldCoordinators',
            'pendingValidationsCount',
            'depositThisMonth',
            'expiringAgreements',
            'recentDeposits',
            'recentParkingLocations',
            'recentCoordinators',
            'mainChartLabels',
            'mainChartData',
            'targetChartData',
            'zoneChartData',
            'barChartData',
        ));
    }

    /**
     * Mencari perjanjian berdasarkan nomor dan redirect ke halaman detail.
     */
    public function findAgreement(Request $request)
    {
        $request->validate(['agreement_number' => 'required|string']);
        $agreement = Agreement::where('agreement_number', 'like', '%'.$request->agreement_number.'%')->first();

        if ($agreement) {
            return redirect()->route('masterdata.agreements.show', $agreement->id);
        }

        return redirect()->back()->with('error', 'Perjanjian dengan nomor '.$request->agreement_number.' tidak ditemukan.');
    }

    public function staffPksDashboard()
    {
        // Card Pimpinan
        $currentLeader = Leader::with('user')->latest()->first();

        // Quick Stats
        $totalParkingLocations = ParkingLocation::count();
        $totalAgreements = Agreement::where('status', 'active')->count();
        $totalRoadSections = RoadSection::count();
        $totalFieldCoordinators = FieldCoordinator::count();

        // 10 Daftar Lokasi Terbaru
        $recentParkingLocations = ParkingLocation::with('roadSection')->latest()->limit(10)->get();

        // 10 Daftar PKS Terbaru (Hanya yang Aktif)
        $recentAgreements = Agreement::with('fieldCoordinator.user')
            ->where('status', 'active')
            ->withCount('activeParkingLocations')
            ->latest()
            ->limit(10)
            ->get();

        // PKS Segera Berakhir (30 hari ke depan)
        $expiringAgreements = Agreement::with('fieldCoordinator.user')
            ->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->orderBy('end_date', 'asc')
            ->limit(5)->get();

        // Grafik Jumlah Lokasi per Ruas Jalan (Top 10)
        $locationsPerRoadSection = RoadSection::withCount('parkingLocations')
            ->orderBy('parking_locations_count', 'desc')
            ->limit(10)->get();

        $barChartData = [
            'labels' => $locationsPerRoadSection->pluck('name'),
            'data' => $locationsPerRoadSection->pluck('parking_locations_count'),
        ];

        return view('staff.pks.dashboard', compact(
            'currentLeader',
            'recentParkingLocations',
            'totalParkingLocations',
            'recentAgreements',
            'totalAgreements',
            'totalRoadSections',
            'totalFieldCoordinators',
            'expiringAgreements',
            'barChartData'
        ));
    }

    /**
     * ✅ Dashboard untuk Staff Keuangan.
     */
    public function staffKeuDashboard()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Grafik Setoran per Bulan
        $monthlyDeposits = DepositTransaction::select(
            DB::raw('MONTH(deposit_date) as month'),
            DB::raw('SUM(amount) as total')
        )
            ->where('is_validated', true)->whereYear('deposit_date', $currentYear)
            ->groupBy('month')->orderBy('month')->pluck('total', 'month')->all();

        $depositChartLabels = [];
        $depositChartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $depositChartLabels[] = Carbon::create()->month($m)->translatedFormat('F');
            $depositChartData[] = $monthlyDeposits[$m] ?? 0;
        }

        // Daftar PKS yang sudah & belum bayar bulan ini
        $allActiveAgreements = Agreement::with('fieldCoordinator.user')
            ->where('status', 'active')
            ->get();

        $paidAgreementIds = DepositTransaction::whereYear('deposit_date', $currentYear)
            ->whereMonth('deposit_date', $currentMonth)
            ->pluck('agreement_id')->unique();

        $paidAgreements = $allActiveAgreements->whereIn('id', $paidAgreementIds);
        $unpaidAgreements = $allActiveAgreements->whereNotIn('id', $paidAgreementIds);

        // Jumlah Setoran
        $depositThisMonth = $paidAgreements->flatMap->depositTransactions
            ->where('deposit_date.month', $currentMonth)
            ->where('deposit_date.year', $currentYear)
            ->sum('amount');
        $depositThisYear = array_sum($depositChartData);

        // Quick Stats baru
        $totalActiveAgreements = $allActiveAgreements->count();
        $pendingValidationsCount = DepositTransaction::where('is_validated', false)->count();
        $paidCount = $paidAgreements->count();
        $unpaidCount = $unpaidAgreements->count();

        return view('staff.keu.dashboard', compact(
            'depositChartLabels',
            'depositChartData',
            'paidAgreements',
            'unpaidAgreements',
            'depositThisMonth',
            'depositThisYear',
            'totalActiveAgreements',
            'pendingValidationsCount',
            'paidCount',
            'unpaidCount'
        ));
    }

    /**
     * ✅ Dashboard untuk Leader (Gabungan PKS & Keuangan).
     */
    public function leaderDashboard()
    {
        // Ambil semua data dari kedua dashboard staff
        $pksData = $this->staffPksDashboard()->getData();
        $keuData = $this->staffKeuDashboard()->getData();

        // Ambil 50 titik parkir acak untuk peta
        $randomMapLocations = ParkingLocation::with('roadSection')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->inRandomOrder()
            ->take(50)
            ->get();

        // Gabungkan semua data
        $allData = array_merge($pksData, $keuData);
        $allData['randomMapLocations'] = $randomMapLocations;
        $allData['pendingValidationsCount'] = DepositTransaction::where('is_validated', false)->count();

        return view('leader.dashboard', $allData);
    }

    public function fieldCoordinatorDashboard()
    {
        $user = Auth::user();

        // ✅ INI YANG BENAR: Cari data Korlap, bukan Bendahara
        $coordinator = FieldCoordinator::where('user_id', $user->id)->first();

        if (! $coordinator) {
            abort(403, 'Profil Koordinator Lapangan tidak ditemukan. Hubungi Administrator.');
        }

        $activeAgreement = Agreement::with(['activeParkingLocations.roadSection'])
            ->where('field_coordinator_id', $coordinator->id)
            ->where('status', 'active')
            ->first();

        $totalLocations = 0;
        $dailyDeposit = 0;
        $recentLocations = collect();

        $hasPaidCurrentMonth = false;
        $isContractLunas = false;
        $currentMonthName = '';
        $nextMonthName = '';
        $nextMonthTotal = 0;
        $daysInNextMonth = 0;

        if ($activeAgreement) {
            $totalLocations = $activeAgreement->activeParkingLocations->count();
            $dailyDeposit = $activeAgreement->daily_deposit_amount;

            $recentLocations = $activeAgreement->activeParkingLocations()
                ->with('roadSection')
                ->latest()
                ->limit(5)
                ->get();

            $paidMonthsCount = DepositTransaction::where('agreement_id', $activeAgreement->id)->count();

            $contractStartDate = Carbon::parse($activeAgreement->start_date)->startOfMonth();
            $contractEndDate = Carbon::parse($activeAgreement->end_date)->endOfMonth();

            $targetDate = $contractStartDate->copy()->addMonths($paidMonthsCount);
            $now = Carbon::now()->startOfMonth();

            if ($targetDate->gt($contractEndDate)) {
                $isContractLunas = true;
                $hasPaidCurrentMonth = true;
                $currentMonthName = $contractEndDate->translatedFormat('F Y');
            } elseif ($targetDate->gt($now)) {
                $hasPaidCurrentMonth = true;
                $currentMonthName = $now->translatedFormat('F Y');

                $nextMonthName = $targetDate->translatedFormat('F Y');
                $daysInNextMonth = $targetDate->daysInMonth;
                $nextMonthTotal = $dailyDeposit * $daysInNextMonth;
            } else {
                $hasPaidCurrentMonth = false;
                $currentMonthName = $targetDate->translatedFormat('F Y');
            }
        }

        $recentRequests = LocationRequest::with('parkingLocation')
            ->whereHas('agreement', function ($q) use ($coordinator) {
                $q->where('field_coordinator_id', $coordinator->id);
            })->latest()->limit(5)->get();

        $currentLeader = Leader::with('user')->latest()->first();
        $currentTreasurer = Treasurer::with('user')->whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->first();

        $activeBankAccount = BludBankAccount::where('is_active', true)->first();

        // ✅ AMBIL PROFIL UPT & FORMAT NOMOR WHATSAPP
        $uptProfile = UptProfile::first();
        $uptName = $uptProfile->name ?? 'UPT Perparkiran';

        // Bersihkan nomor HP (Hanya ambil angka)
        $uptPhoneRaw = $uptProfile->phone ?? '';
        $uptPhoneWa = preg_replace('/[^0-9]/', '', $uptPhoneRaw);
        // Jika berawalan '0', ganti menjadi '62' untuk link wa.me
        if (str_starts_with($uptPhoneWa, '0')) {
            $uptPhoneWa = '62'.substr($uptPhoneWa, 1);
        }

        return view('field_coordinator.dashboard', compact(
            'coordinator', 'activeAgreement', 'totalLocations', 'dailyDeposit',
            'recentLocations', 'recentRequests', 'currentLeader', 'currentTreasurer',
            'activeBankAccount', 'hasPaidCurrentMonth', 'isContractLunas', 'currentMonthName',
            'nextMonthName', 'nextMonthTotal', 'daysInNextMonth', 'uptName', 'uptPhoneWa'
        ));
    }

    /**
     * ✅ Dashboard untuk Bendahara (Treasurer).
     */
    public function treasurerDashboard()
    {
        $user = Auth::user();

        $treasurer = Treasurer::where('user_id', $user->id)->first();

        if (! $treasurer) {
            abort(403, 'Akun Anda tidak terdaftar sebagai Bendahara.');
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Data Setoran Pending (Global)
        $pendingValidationsCount = DepositTransaction::where('is_validated', false)->count();
        $pendingAmount = DepositTransaction::where('is_validated', false)->sum('amount');

        // 2. Data Setoran yang sudah divalidasi oleh Bendahara ini pada bulan ini
        $validatedThisMonth = DepositTransaction::where('treasurer_id', $treasurer->id)
            ->where('is_validated', true)
            ->whereMonth('validation_date', $currentMonth)
            ->whereYear('validation_date', $currentYear)
            ->sum('amount');

        // 3. Total Validasi Tahun Ini
        $totalValidatedThisYear = DepositTransaction::where('is_validated', true)
            ->whereYear('deposit_date', $currentYear)
            ->sum('amount');

        // 4. Jumlah Transaksi yang sudah divalidasi
        $totalTransactionsValidated = DepositTransaction::where('treasurer_id', $treasurer->id)
            ->where('is_validated', true)
            ->whereYear('validation_date', $currentYear)
            ->count();

        // 5. Tabel Transaksi Menunggu Validasi (5 Terbaru)
        $recentPendingDeposits = DepositTransaction::with('agreement.fieldCoordinator.user')
            ->where('is_validated', false)
            ->latest('deposit_date')
            ->limit(5)
            ->get();

        // 6. Tabel Riwayat Validasi Bendahara (5 Terbaru)
        $recentValidatedDeposits = DepositTransaction::with('agreement.fieldCoordinator.user')
            ->where('treasurer_id', $treasurer->id)
            ->where('is_validated', true)
            ->latest('validation_date')
            ->limit(5)
            ->get();

        $activeBankAccount = BludBankAccount::where('is_active', true)->first();

        return view('treasurer.dashboard', compact(
            'treasurer', 'pendingValidationsCount', 'pendingAmount',
            'validatedThisMonth', 'totalValidatedThisYear', 'totalTransactionsValidated',
            'recentPendingDeposits', 'recentValidatedDeposits', 'activeBankAccount'
        ));
    }

    // Fallback index
    public function index()
    {
        $user = Auth::user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'leader':
                return redirect()->route('leader.dashboard');
            case 'treasurer':
                return redirect()->route('treasurer.dashboard');
            case 'field_coordinator':
                return redirect()->route('field_coordinator.dashboard');
            case 'staff_keu':
                return redirect()->route('staff-keuangan.dashboard');
            case 'staff_pks':
                return redirect()->route('staff-pks.dashboard');
            default:
                return view('dashboard');
        }
    }

    public function searchAgreementsAjax(Request $request)
    {
        $term = $request->input('q');

        if (! $term) {
            return response()->json(['items' => []]);
        }

        $agreements = Agreement::with('fieldCoordinator.user')
            ->where(function ($query) use ($term) {
                $query->where('agreement_number', 'like', '%'.$term.'%')
                    ->orWhereHas('fieldCoordinator.user', function ($q) use ($term) {
                        $q->where('name', 'like', '%'.$term.'%');
                    });
            })
            ->limit(20)
            ->get();

        $results = $agreements->map(function ($agreement) {
            return [
                'id' => $agreement->id,
                'text' => $agreement->agreement_number.' ('.($agreement->fieldCoordinator->user->name ?? 'N/A').')',
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function searchParkingLocationsAjax(Request $request)
    {
        $term = $request->input('q');

        if (! $term) {
            return response()->json(['items' => []]);
        }

        $locations = ParkingLocation::with('roadSection')
            ->where('name', 'like', '%'.$term.'%')
            ->limit(20)
            ->get();

        $results = $locations->map(function ($location) {
            return [
                'id' => $location->id,
                'text' => $location->name.' ('.($location->roadSection->name ?? 'Tanpa Ruas Jalan').')',
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * ✅ METHOD BARU: Menyediakan data Setoran untuk Select2 AJAX.
     */
    public function searchDepositsAjax(Request $request)
    {
        $term = $request->input('q');

        if (! $term || strlen($term) < 3) { // Minimal 3 karakter untuk mulai mencari
            return response()->json(['items' => []]);
        }

        // Mencari transaksi yang nomor referensinya BERAKHIRAN dengan term yang diketik
        $deposits = DepositTransaction::with('agreement')
            ->where('referral_code ', 'like', '%'.$term.'%')
            ->latest('deposit_date')
            ->limit(20)
            ->get();

        $results = $deposits->map(function ($deposit) {
            return [
                'id' => $deposit->id,
                'text' => 'Ref: ...'.substr($deposit->referral_code, -6).' | Rp '.number_format($deposit->amount, 0, ',', '.').' ('.$deposit->agreement->agreement_number.')',
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * ✅ FITUR PENCARIAN GLOBAL (NAVBAR)
     */
    public function globalSearch(Request $request)
    {
        $term = $request->input('q');

        // Batasi minimal ketik 2 huruf biar server nggak capek
        if (! $term || strlen($term) < 2) {
            return response()->json([]);
        }

        $results = [];

        // 1. Cari Lokasi Parkir (Titik)
        $locations = ParkingLocation::where('name', 'like', "%{$term}%")->limit(3)->get();
        foreach ($locations as $loc) {
            $results[] = [
                'title' => $loc->name,
                'subtitle' => 'Lokasi Parkir',
                'url' => route('masterdata.parking-locations.show', $loc->id),
                'icon' => 'ri icon-base ri-map-pin-line text-danger bg-label-danger',
            ];
        }

        // 2. Cari Perjanjian PKS
        $agreements = Agreement::where('agreement_number', 'like', "%{$term}%")->limit(3)->get();
        foreach ($agreements as $agr) {
            $results[] = [
                'title' => $agr->agreement_number,
                'subtitle' => 'Perjanjian PKS',
                'url' => route('masterdata.agreements.show', $agr->id),
                'icon' => 'ri icon-base ri-file-text-line text-primary bg-label-primary',
            ];
        }

        // 3. Cari User (Khusus Admin) - SMART ROUTING
        if (Auth::user()->role === 'admin') {
            $users = User::where('name', 'like', "%{$term}%")->limit(3)->get();

            foreach ($users as $u) {
                // Tentukan URL berdasarkan Role User yang dicari
                $url = route('admin.users.show', $u->id); // Default URL
                $icon = 'ri icon-base ri-user-line text-secondary bg-label-secondary'; // Default Icon
                $roleLabel = str_replace('_', ' ', $u->role);

                // Cek relasi dan sesuaikan route
                if ($u->role === 'leader' && $u->leader) {
                    $url = route('admin.leaders.show', $u->leader->id);
                    $icon = 'ri icon-base ri-user-star-line text-warning bg-label-warning';
                    $roleLabel = 'Pimpinan UPT';
                } elseif ($u->role === 'treasurer' && $u->treasurer) {
                    $url = route('admin.treasurers.show', $u->treasurer->id);
                    $icon = 'ri icon-base ri-safe-2-line text-success bg-label-success';
                    $roleLabel = 'Bendahara';
                } elseif ($u->role === 'field_coordinator' && $u->fieldCoordinator) {
                    $url = route('admin.field-coordinators.show', $u->fieldCoordinator->id);
                    $icon = 'ri icon-base ri-user-location-line text-info bg-label-info';
                    $roleLabel = 'Koordinator Lapangan';
                } elseif (in_array($u->role, ['admin', 'staff_pks', 'staff_keu'])) {
                    // Admin & Staff cukup pakai route users biasa
                    $url = route('admin.users.show', $u->id);
                    $icon = 'ri icon-base ri-shield-user-line text-primary bg-label-primary';
                }

                $results[] = [
                    'title' => $u->name,
                    'subtitle' => 'Pengguna ('.ucwords($roleLabel).')',
                    'url' => $url,
                    'icon' => $icon,
                ];
            }
        }

        return response()->json($results);
    }
}
