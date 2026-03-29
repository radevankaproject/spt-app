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
        $selectedAgreementId = $request->input('agreement_id');

        // Ambil data untuk filter (Dioptimasi dengan Select untuk menghemat memory)
        $agreementsForFilter = Agreement::has('fieldCoordinator.user')
            ->with('fieldCoordinator.user:id,name') // Hanya ambil ID dan Nama
            ->select('id', 'agreement_number', 'field_coordinator_id')
            ->orderBy('agreement_number', 'desc')
            ->get();

        $agreement = null;
        if ($selectedAgreementId) {
            // ✅ FIX N+1: Tambahkan 'histories.changer' dan 'leader.user'
            $agreement = Agreement::with([
                'histories' => function ($query) {
                    $query->latest(); // Urutkan terbaru di atas
                },
                'histories.changer', // <-- Obat N+1
                'fieldCoordinator.user',
                'leader.user' // Tambahan untuk Info Ringkasan
            ])->find($selectedAgreementId);
        }

        return view('masterdata.agreement_histories.index', compact(
            'agreementsForFilter',
            'agreement',
            'selectedAgreementId'
        ));
    }
}
