<?php
namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use Illuminate\Http\Request;

class AgreementHistoryController extends Controller
{
    /**
     * Menampilkan halaman filter dan timeline histori perjanjian.
     */
    public function index(Request $request)
    {
        // Ambil ID perjanjian yang dipilih dari form filter
        $selectedAgreementId = $request->input('agreement_id');

        // Ambil semua perjanjian yang memiliki koordinator untuk ditampilkan di dropdown filter
        $agreementsForFilter = Agreement::has('fieldCoordinator.user')
            ->with('fieldCoordinator.user')
            ->orderBy('agreement_number', 'desc')
            ->get();

        $agreement = null;
        if ($selectedAgreementId) {
            // Jika ada perjanjian yang dipilih, ambil datanya beserta seluruh historinya
            $agreement = Agreement::with(['histories' => function ($query) {
                // Urutkan histori dari yang terbaru ke terlama
                $query->latest();
            }, 'fieldCoordinator.user'])
                ->find($selectedAgreementId);
        }

        // Kirim data ke view
        return view('masterdata.agreement_histories.index', compact(
            'agreementsForFilter',
            'agreement',
            'selectedAgreementId'
        ));
    }
}
