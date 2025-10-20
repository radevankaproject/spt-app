<?php
namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\DepositTransaction; // Pastikan ini diimpor
use App\Models\FieldCoordinator;   // Pastikan ini diimpor
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepositReportController extends Controller
{
    /**
     * Display the deposit report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Jika tombol 'Cetak PDF' diklik, panggil method generatePdf
        if ($request->has('print_pdf')) {
            return $this->generatePdf($request);
        }

        $reportType         = $request->input('report_type', 'daily');
        $specificDate       = $request->input('specific_date');
        $specificMonth      = $request->input('specific_month');
        $specificYear       = $request->input('specific_year');
        $startDate          = $request->input('start_date');
        $endDate            = $request->input('end_date');
        $search             = $request->input('search');               // Filter pencarian umum
        $fieldCoordinatorId = $request->input('field_coordinator_id'); // Filter Korlap dari dropdown

        $query = DepositTransaction::with(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator']);

        $currentYearFilter = Carbon::now()->year;
        $query->whereHas('agreement', function ($agreementQuery) use ($currentYearFilter) {
            $agreementQuery->where('status', 'active')
                ->whereYear('start_date', '<=', $currentYearFilter)
                ->whereYear('end_date', '>=', $currentYearFilter);
        });

        // Terapkan filter berdasarkan Field Coordinator yang dipilih dari dropdown
        if ($fieldCoordinatorId) {
            $query->whereHas('agreement', function ($agreementQuery) use ($fieldCoordinatorId) {
                $agreementQuery->where('field_coordinator_id', $fieldCoordinatorId);
            });
        }

        // Terapkan filter pencarian umum (nomor perjanjian atau nama korlap)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('agreement', function ($agreementQuery) use ($search) {
                    $agreementQuery->where('agreement_number', 'like', '%' . $search . '%')
                        ->orWhereHas('fieldCoordinator.user', function ($fcUserQuery) use ($search) {
                            $fcUserQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            });
        }

        $reportTitle = 'Laporan Transaksi Setoran'; // Judul default

        switch ($reportType) {
            case 'daily':
                $dateToFilter = $specificDate ? Carbon::parse($specificDate) : Carbon::today();
                $query->whereDate('deposit_date', $dateToFilter);
                $reportTitle = 'Jumlah Setoran Tanggal ' . $dateToFilter->translatedFormat('d F Y');
                break;
            case 'monthly':
                $monthToFilter = $specificMonth ? $specificMonth : Carbon::now()->month;
                $yearToFilter  = $specificYear ? $specificYear : Carbon::now()->year;
                $query->whereMonth('deposit_date', $monthToFilter)
                    ->whereYear('deposit_date', $yearToFilter);
                $reportTitle = 'Jumlah Setoran Bulan ' . Carbon::createFromDate($yearToFilter, $monthToFilter, 1)->translatedFormat('F Y');
                break;
            case 'yearly':
                $yearToFilter = $specificYear ? $specificYear : Carbon::now()->year;
                $query->whereYear('deposit_date', $yearToFilter);
                $reportTitle = 'Jumlah Setoran Tahun ' . $yearToFilter;
                break;
            case 'custom_range':
                if ($startDate && $endDate) {
                    $query->whereBetween('deposit_date', [$startDate, $endDate]);
                    $reportTitle = 'Jumlah Setoran Dari ' . Carbon::parse($startDate)->translatedFormat('d F Y') . ' Sampai ' . Carbon::parse($endDate)->translatedFormat('d F Y');
                } elseif ($startDate) {
                    $query->whereDate('deposit_date', '>=', $startDate);
                    $reportTitle = 'Jumlah Setoran Mulai ' . Carbon::parse($startDate)->translatedFormat('d F Y');
                } elseif ($endDate) {
                    $query->whereDate('deposit_date', '<=', $endDate);
                    $reportTitle = 'Jumlah Setoran Sampai ' . Carbon::parse($endDate)->translatedFormat('d F Y');
                } else {
                    $reportTitle = 'Jumlah Setoran Rentang Waktu Kustom (Tidak Ada Tanggal Dipilih)';
                }
                break;
        }

        // NEW: Tambahkan informasi filter ke reportTitle
        $filterDetails = [];
        if ($search) {
            $filterDetails[] = $search;
        }
        if ($fieldCoordinatorId) {
            $korlap = FieldCoordinator::with('user')->find($fieldCoordinatorId);
            if ($korlap && $korlap->user) {
                $filterDetails[] = ' - ' . $korlap->user->name;
            }
        }
        if (! empty($filterDetails)) {
            $reportTitle .= ' (' . implode($filterDetails) . ')';
        }

        $reports     = $query->latest('deposit_date')->get();
        $totalAmount = $reports->sum('amount');

        // Ambil daftar Field Coordinator untuk dropdown filter
        $fieldCoordinatorsQuery = FieldCoordinator::select('field_coordinators.*')
            ->join('users', 'field_coordinators.user_id', '=', 'users.id');

        if ($search) { // Terapkan filter pencarian umum ke opsi dropdown Korlap
            $fieldCoordinatorsQuery->where(function ($q) use ($search) {
                $q->where('users.name', 'like', '%' . $search . '%')
                    ->orWhereHas('agreements', function ($agreementQuery) use ($search) {
                        $agreementQuery->where('agreement_number', 'like', '%' . $search . '%');
                    });
            });
        }
        $fieldCoordinators = $fieldCoordinatorsQuery->orderBy('users.name', 'asc')->get();

        return view('masterdata.deposit_reports.index', compact(
            'reports',
            'totalAmount',
            'reportType',
            'specificDate',
            'specificMonth',
            'specificYear',
            'startDate',
            'endDate',
            'reportTitle',
            'search',
            'fieldCoordinators',
            'fieldCoordinatorId'
        ));
    }

    /**
     * Generate PDF for the deposit report.
     * This method will receive the same filters as the index method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generatePdf(Request $request)
    {
        $reportType         = $request->input('report_type', 'daily');
        $specificDate       = $request->input('specific_date');
        $specificMonth      = $request->input('specific_month');
        $specificYear       = $request->input('specific_year');
        $startDate          = $request->input('start_date');
        $endDate            = $request->input('end_date');
        $search             = $request->input('search');
        $fieldCoordinatorId = $request->input('field_coordinator_id');

        $query = DepositTransaction::with(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator']);

        $currentYearFilter = Carbon::now()->year;
        $query->whereHas('agreement', function ($agreementQuery) use ($currentYearFilter) {
            $agreementQuery->where('status', 'active')
                ->whereYear('start_date', '<=', $currentYearFilter)
                ->whereYear('end_date', '>=', $currentYearFilter);
        });

        // Terapkan filter berdasarkan Field Coordinator yang dipilih dari dropdown
        if ($fieldCoordinatorId) {
            $query->whereHas('agreement', function ($agreementQuery) use ($fieldCoordinatorId) {
                $agreementQuery->where('field_coordinator_id', $fieldCoordinatorId);
            });
        }

        // Terapkan filter pencarian umum
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('agreement', function ($agreementQuery) use ($search) {
                    $agreementQuery->where('agreement_number', 'like', '%' . $search . '%')
                        ->orWhereHas('fieldCoordinator.user', function ($fcUserQuery) use ($search) {
                            $fcUserQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            });
        }

        $query->join('agreements', 'deposit_transactions.agreement_id', '=', 'agreements.id')
            ->orderBy('agreements.agreement_number', 'asc')
            ->orderBy('deposit_transactions.deposit_date', 'asc');

        // Ambil data dari database
        $reports = $query->select('deposit_transactions.*')->get();

                                                                             // Hitung total dari semua data sebelum dikelompokkan
        $totalAmount = $reports->where('is_validated', true)->sum('amount'); // Hitung hanya yg tervalidasi

        // ✅ KELOMPOKKAN HASILNYA BERDASARKAN ID PERJANJIAN
        $reportsByAgreement = $reports->groupBy('agreement_id');

        $reportTitle = 'Laporan Transaksi Setoran'; // Judul default untuk PDF

        switch ($reportType) {
            case 'daily':
                $dateToFilter = $specificDate ? Carbon::parse($specificDate) : Carbon::today();
                $query->whereDate('deposit_date', $dateToFilter);
                $reportTitle = 'Jumlah Setoran Tanggal ' . $dateToFilter->translatedFormat('d F Y');
                break;
            case 'monthly':
                $monthToFilter = $specificMonth ? $specificMonth : Carbon::now()->month;
                $yearToFilter  = $specificYear ? $specificYear : Carbon::now()->year;
                $query->whereMonth('deposit_date', $monthToFilter)
                    ->whereYear('deposit_date', $yearToFilter);
                $reportTitle = 'Jumlah Setoran Bulan ' . Carbon::createFromDate($yearToFilter, $monthToFilter, 1)->translatedFormat('F Y');
                break;
            case 'yearly':
                $yearToFilter = $specificYear ? $specificYear : Carbon::now()->year;
                $query->whereYear('deposit_date', $yearToFilter);
                $reportTitle = 'Jumlah Setoran Tahun ' . $yearToFilter;
                break;
            case 'custom_range':
                if ($startDate && $endDate) {
                    $query->whereBetween('deposit_date', [$startDate, $endDate]);
                    $reportTitle = 'Jumlah Setoran Dari ' . Carbon::parse($startDate)->translatedFormat('d F Y') . ' Sampai ' . Carbon::parse($endDate)->translatedFormat('d F Y');
                } elseif ($startDate) {
                    $query->whereDate('deposit_date', '>=', $startDate);
                    $reportTitle = 'Jumlah Setoran Mulai ' . Carbon::parse($startDate)->translatedFormat('d F Y');
                } elseif ($endDate) {
                    $query->whereDate('deposit_date', '<=', $endDate);
                    $reportTitle = 'Jumlah Setoran Sampai ' . Carbon::parse($endDate)->translatedFormat('d F Y');
                } else {
                    $reportTitle = 'Jumlah Setoran Rentang Waktu Kustom (Tidak Ada Tanggal Dipilih)';
                }
                break;
        }

        // NEW: Tambahkan informasi filter ke reportTitle untuk PDF
        $filterDetails = [];
        if ($search) {
            $filterDetails[] = $search;
        }
        if ($fieldCoordinatorId) {
            $korlap = FieldCoordinator::with('user')->find($fieldCoordinatorId);
            if ($korlap && $korlap->user) {
                $filterDetails[] = ' - ' . $korlap->user->name;
            }
        }
        if (! empty($filterDetails)) {
            $reportTitle .= ' ' . implode($filterDetails);
        }

        $reports     = $query->latest('deposit_date')->get();
        $totalAmount = $reports->sum('amount');

        $pdf = Pdf::loadView('pdf.deposit_report', compact('reportsByAgreement', 'totalAmount', 'reportTitle', 'search', 'fieldCoordinatorId'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('Laporan_Setoran_' . str_replace(' ', '_', $reportTitle) . '.pdf');
    }
}
