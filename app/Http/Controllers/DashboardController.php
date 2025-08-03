<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\DepositTransaction;
use App\Models\FieldCoordinator;
use App\Models\ParkingLocation;
use App\Models\RoadSection;
use App\Models\Leader;
use App\Models\BludBankAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk Admin dengan data yang komprehensif.
     */
    public function adminDashboard()
    {
        // --- 1. Data untuk Info Cards ---
        $currentLeader = Leader::with('user')->latest()->first();
        $startDate = Carbon::parse($currentLeader->start_date);
        $activeBankAccount = BludBankAccount::where('is_active', true)->first();
        $currentYearValidatedDeposit = DepositTransaction::where('is_validated', true)
            ->whereYear('deposit_date', now()->year)->sum('amount');

        // --- 2. Data untuk Tabel "Terbaru" (Max 8) ---
        $recentDeposits = DepositTransaction::with('agreement.fieldCoordinator.user')
            ->whereHas('agreement')
            ->where('is_validated', true)
            ->latest('deposit_date')->limit(8)->get();

        $recentParkingLocations = ParkingLocation::with('roadSection')->latest()->limit(8)->get();
        $recentCoordinators = FieldCoordinator::with('user')->latest()->limit(8)->get();

        // --- 3. Data untuk Grafik ---

        // A. Grafik Setoran per Bulan (Mixed Chart)
        $monthlyDeposits = DepositTransaction::select(
            DB::raw('MONTH(deposit_date) as month'),
            DB::raw('SUM(amount) as total')
        )
            ->where('is_validated', true)->whereYear('deposit_date', now()->year)
            ->groupBy('month')->orderBy('month')->pluck('total', 'month')->all();

        $mainChartLabels = [];
        $mainChartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $mainChartLabels[] = \Carbon\Carbon::create()->month($m)->translatedFormat('F');
            $mainChartData[] = $monthlyDeposits[$m] ?? 0;
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
            'parkingLocations' => array_values($locationsByZone)
        ];

        // C. Grafik Titik per Ruas Jalan (Bar Chart)
        $locationsPerRoadSection = RoadSection::withCount('parkingLocations')
            ->orderBy('parking_locations_count', 'desc')
            ->limit(20)->get(); // Ambil 10 teratas

        $barChartData = [
            'labels' => $locationsPerRoadSection->pluck('name'),
            'data' => $locationsPerRoadSection->pluck('parking_locations_count')
        ];


        return view('admin.dashboard', compact(
            'currentLeader',
            'activeBankAccount',
            'startDate',
            'currentYearValidatedDeposit',
            'recentDeposits',
            'recentParkingLocations',
            'recentCoordinators',
            'mainChartLabels',
            'mainChartData',
            'zoneChartData',
            'barChartData'
        ));
    }

    /**
     * Mencari perjanjian berdasarkan nomor dan redirect ke halaman detail.
     */
    public function findAgreement(Request $request)
    {
        $request->validate(['agreement_number' => 'required|string']);
        $agreement = Agreement::where('agreement_number', 'like', '%' . $request->agreement_number . '%')->first();

        if ($agreement) {
            return redirect()->route('masterdata.agreements.show', $agreement->id);
        }

        return redirect()->back()->with('error', 'Perjanjian dengan nomor ' . $request->agreement_number . ' tidak ditemukan.');
    }

    public function staffPksDashboard()
    {
        // Card Pimpinan
        $currentLeader = Leader::with('user')->latest()->first();

        // 10 Daftar Lokasi Terbaru
        $recentParkingLocations = ParkingLocation::with('roadSection')->latest()->limit(10)->get();
        $totalParkingLocations = ParkingLocation::count();

        // 10 Daftar PKS Terbaru
        $recentAgreements = Agreement::with('fieldCoordinator.user')->latest()->limit(10)->get();
        $totalAgreements = Agreement::count();

        // Grafik Jumlah Lokasi per Ruas Jalan (Top 10)
        $locationsPerRoadSection = RoadSection::withCount('parkingLocations')
            ->orderBy('parking_locations_count', 'desc')
            ->limit(10)->get();

        $barChartData = [
            'labels' => $locationsPerRoadSection->pluck('name'),
            'data' => $locationsPerRoadSection->pluck('parking_locations_count')
        ];

        return view('staff.pks.dashboard', compact(
            'currentLeader',
            'recentParkingLocations',
            'totalParkingLocations',
            'recentAgreements',
            'totalAgreements',
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

        return view('staff.keu.dashboard', compact(
            'depositChartLabels',
            'depositChartData',
            'paidAgreements',
            'unpaidAgreements',
            'depositThisMonth',
            'depositThisYear'
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

        // Gabungkan semua data
        $allData = array_merge($pksData, $keuData);

        $allData['hideSidebar'] = true;

        return view('leader.dashboard', $allData);
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

        if (!$term) {
            return response()->json(['items' => []]);
        }

        $agreements = Agreement::with('fieldCoordinator.user')
            ->where(function ($query) use ($term) {
                $query->where('agreement_number', 'like', '%' . $term . '%')
                    ->orWhereHas('fieldCoordinator.user', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    });
            })
            ->limit(20)
            ->get();

        $results = $agreements->map(function ($agreement) {
            return [
                'id' => $agreement->id,
                'text' => $agreement->agreement_number . ' (' . ($agreement->fieldCoordinator->user->name ?? 'N/A') . ')'
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function searchParkingLocationsAjax(Request $request)
    {
        $term = $request->input('q');

        if (!$term) {
            return response()->json(['items' => []]);
        }

        $locations = ParkingLocation::with('roadSection')
            ->where('name', 'like', '%' . $term . '%')
            ->limit(20)
            ->get();

        $results = $locations->map(function ($location) {
            return [
                'id' => $location->id,
                'text' => $location->name . ' (' . ($location->roadSection->name ?? 'Tanpa Ruas Jalan') . ')'
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

        if (!$term || strlen($term) < 3) { // Minimal 3 karakter untuk mulai mencari
            return response()->json(['items' => []]);
        }

        // Mencari transaksi yang nomor referensinya BERAKHIRAN dengan term yang diketik
        $deposits = DepositTransaction::with('agreement')
            ->where('referral_code ', 'like', '%' . $term . '%' )
            ->latest('deposit_date')
            ->limit(20)
            ->get();

        $results = $deposits->map(function ($deposit) {
            return [
                'id' => $deposit->id,
                'text' => 'Ref: ...' . substr($deposit->referral_code, -6) . ' | Rp ' . number_format($deposit->amount, 0, ',', '.') . ' (' . $deposit->agreement->agreement_number . ')'
            ];
        });

        return response()->json(['results' => $results]);
    }
}
