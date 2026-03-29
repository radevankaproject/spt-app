<?php
namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\DepositTransaction;
use App\Models\FieldCoordinator;
use App\Models\Agreement;
use App\Models\YearlyDepositTarget;
use App\Models\MonthlyDepositTarget;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepositReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('print_pdf')) {
            return $this->generatePdf($request);
        }

        $reportType         = $request->input('report_type', 'monthly');
        $specificMonth      = $request->input('specific_month', date('m'));
        $specificYear       = $request->input('specific_year', date('Y'));
        $search             = $request->input('search');
        $fieldCoordinatorId = $request->input('field_coordinator_id');

        // 1. INIT QUERY (Eager Load)
        $query = DepositTransaction::with(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator']);

        $currentYearFilter = Carbon::now()->year;
        $query->whereHas('agreement', function ($agreementQuery) use ($currentYearFilter) {
            $agreementQuery->where('status', 'active')
                ->whereYear('start_date', '<=', $currentYearFilter)
                ->whereYear('end_date', '>=', $currentYearFilter);
        });

        if ($fieldCoordinatorId) {
            $query->whereHas('agreement', function ($q) use ($fieldCoordinatorId) { $q->where('field_coordinator_id', $fieldCoordinatorId); });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('agreement', function ($aq) use ($search) {
                    $aq->where('agreement_number', 'like', '%' . $search . '%')
                        ->orWhereHas('fieldCoordinator.user', function ($uq) use ($search) { $uq->where('name', 'like', '%' . $search . '%'); });
                });
            });
        }

        // ✅ 2. FILTER WAKTU (TAHUNAN / BULANAN) HARUS DIEKSEKUSI SEBELUM ->GET()
        if ($reportType === 'yearly') {
            $query->whereYear('deposit_date', $specificYear);
        } else {
            $query->whereYear('deposit_date', $specificYear)
                  ->whereMonth('deposit_date', $specificMonth);
        }

        // ✅ 3. AMBIL DATA REPORTS (Sekarang sudah tersaring rapi!)
        $reports = $query->latest('deposit_date')->get();
        $totalAmount = $reports->where('is_validated', true)->sum('amount');

        // ✅ 4. AMBIL DATA TARGET DARI DATABASE
        $yearlyTargetData = YearlyDepositTarget::with('monthlyTargets')->where('year', $specificYear)->first();

        $reportTitle = 'Laporan Transaksi Setoran';
        $chartLabels = []; $chartValues = []; $chartTargets = [];
        $totalTargetAmount = 0;

        // ✅ 5. LOGIKA GRAFIK & TARGET
        if ($reportType === 'yearly') {
            $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $chartLabels = $months;
            $totalTargetAmount = $yearlyTargetData ? $yearlyTargetData->total_target : 0;

            for ($i = 1; $i <= 12; $i++) {
                // Actual Setoran per bulan
                $actual = $reports->where('is_validated', true)->filter(function($item) use ($i, $specificYear) {
                    $date = Carbon::parse($item->deposit_date);
                    return $date->month == $i && $date->year == $specificYear;
                })->sum('amount');
                $chartValues[] = $actual;

                // Target Proyeksi per bulan (dari database target)
                $monthTarget = $yearlyTargetData ? $yearlyTargetData->monthlyTargets->where('month', $i)->first() : null;
                $chartTargets[] = $monthTarget ? $monthTarget->target_amount : 0;
            }
            $reportTitle = 'Laporan Setoran Tahun ' . $specificYear;
            $chartTitle = 'Grafik Tren Setoran vs Target Proyeksi Tahun ' . $specificYear;

        } else { // monthly
            $monthTarget = $yearlyTargetData ? $yearlyTargetData->monthlyTargets->where('month', (int)$specificMonth)->first() : null;
            $totalTargetAmount = $monthTarget ? $monthTarget->target_amount : 0;

            $startOfMonth = Carbon::createFromDate($specificYear, $specificMonth, 1)->startOfMonth();
            $coordinators = collect();

            foreach ($reports->where('is_validated', true) as $rep) {
                $name = $rep->agreement->fieldCoordinator->user->name ?? 'Lainnya';
                if (!$coordinators->has($name)) $coordinators->put($name, 0);
                $coordinators->put($name, $coordinators->get($name) + $rep->amount);
            }

            $chartLabels = $coordinators->keys()->toArray();
            $chartValues = $coordinators->values()->toArray();
            $chartTargets = [];

            $reportTitle = 'Laporan Setoran Bulan ' . $startOfMonth->translatedFormat('F Y');
            $chartTitle = 'Grafik Validasi Setoran per Koordinator Lapangan';
        }

        // Kumpulkan Detail Filter untuk Judul
        $filterDetails = [];
        if ($search) $filterDetails[] = "Pencarian: '{$search}'";
        if ($fieldCoordinatorId) {
            $korlap = FieldCoordinator::with('user')->find($fieldCoordinatorId);
            if ($korlap && $korlap->user) $filterDetails[] = "Korlap: " . $korlap->user->name;
        }
        if (!empty($filterDetails)) $reportTitle .= ' (' . implode(' | ', $filterDetails) . ')';

        $fieldCoordinators = FieldCoordinator::with('user')->whereHas('user', function($q) use ($search) {
            if($search) $q->where('name', 'like', "%{$search}%");
        })->get()->sortBy(function($fc) { return $fc->user->name ?? ''; });

        $chartLabels = json_encode($chartLabels);
        $chartValues = json_encode($chartValues);
        $chartTargets = json_encode($chartTargets);

        $percentage = $totalTargetAmount > 0 ? round(($totalAmount / $totalTargetAmount) * 100, 2) : 0;

        return view('masterdata.deposit_reports.index', compact(
            'reports', 'totalAmount', 'totalTargetAmount', 'percentage', 'reportType', 'specificMonth', 'specificYear',
            'reportTitle', 'chartTitle', 'chartLabels', 'chartValues', 'chartTargets', 'search', 'fieldCoordinators', 'fieldCoordinatorId'
        ));
    }

    public function generatePdf(Request $request)
    {
        $reportType         = $request->input('report_type', 'monthly');
        $specificMonth      = $request->input('specific_month', date('m'));
        $specificYear       = $request->input('specific_year', date('Y'));
        $search             = $request->input('search');
        $fieldCoordinatorId = $request->input('field_coordinator_id');

        $query = DepositTransaction::with(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator']);

        $currentYearFilter = Carbon::now()->year;
        $query->whereHas('agreement', function ($q) use ($currentYearFilter) {
            $q->where('status', 'active')->whereYear('start_date', '<=', $currentYearFilter)->whereYear('end_date', '>=', $currentYearFilter);
        });

        if ($fieldCoordinatorId) {
            $query->whereHas('agreement', function ($q) use ($fieldCoordinatorId) { $q->where('field_coordinator_id', $fieldCoordinatorId); });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('agreement', function ($aq) use ($search) {
                    $aq->where('agreement_number', 'like', '%' . $search . '%')->orWhereHas('fieldCoordinator.user', function ($uq) use ($search) { $uq->where('name', 'like', '%' . $search . '%'); });
                });
            });
        }

        $query->join('agreements', 'deposit_transactions.agreement_id', '=', 'agreements.id')
            ->orderBy('agreements.agreement_number', 'asc')->orderBy('deposit_transactions.deposit_date', 'asc');

        // ✅ FILTER TAHUNAN / BULANAN UNTUK PDF
        if ($reportType === 'yearly') {
            $query->whereYear('deposit_transactions.deposit_date', $specificYear);
        } else {
            $query->whereYear('deposit_transactions.deposit_date', $specificYear)
                  ->whereMonth('deposit_transactions.deposit_date', $specificMonth);
        }

        // AMBIL DATA
        $reports = $query->select('deposit_transactions.*')->get();
        $totalAmount = $reports->where('is_validated', true)->sum('amount');
        $reportsByAgreement = $reports->groupBy('agreement_id');

        // AMBIL TARGET
        $yearlyTargetData = YearlyDepositTarget::with('monthlyTargets')->where('year', $specificYear)->first();
        $totalTargetAmount = 0;
        $reportTitle = 'Laporan Transaksi Setoran';

        if ($reportType === 'yearly') {
            $reportTitle = 'Laporan Setoran Tahun ' . $specificYear;
            $totalTargetAmount = $yearlyTargetData ? $yearlyTargetData->total_target : 0;
        } else {
            $startOfMonth = Carbon::createFromDate($specificYear, $specificMonth, 1)->startOfMonth();
            $reportTitle = 'Laporan Setoran Bulan ' . $startOfMonth->translatedFormat('F Y');
            $monthTarget = $yearlyTargetData ? $yearlyTargetData->monthlyTargets->where('month', (int)$specificMonth)->first() : null;
            $totalTargetAmount = $monthTarget ? $monthTarget->target_amount : 0;
        }

        $filterDetails = [];
        if ($search) $filterDetails[] = "Pencarian: '{$search}'";
        if ($fieldCoordinatorId) {
            $korlap = FieldCoordinator::with('user')->find($fieldCoordinatorId);
            if ($korlap && $korlap->user) $filterDetails[] = "Korlap: " . $korlap->user->name;
        }
        if (!empty($filterDetails)) $reportTitle .= ' (' . implode(' | ', $filterDetails) . ')';

        $percentage = $totalTargetAmount > 0 ? round(($totalAmount / $totalTargetAmount) * 100, 2) : 0;

        $pdf = Pdf::loadView('pdf.deposit_report', compact('reportsByAgreement', 'totalAmount', 'totalTargetAmount', 'percentage', 'reportTitle', 'search', 'fieldCoordinatorId'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Setoran_' . str_replace(' ', '_', $reportTitle) . '.pdf');
    }
}
