<?php

namespace App\Http\Controllers\MasterData; // Namespace yang sudah kita sepakati

use App\Http\Controllers\Controller;
use App\Models\DepositTransaction;
use App\Models\Agreement;
use App\Models\UptProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DepositTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchDate = $request->input('search_date');
        $searchMonth = $request->input('search_month');
        $searchYear = $request->input('search_year');
        $startDateRange = $request->input('start_date_range');
        $endDateRange = $request->input('end_date_range');

        $query = DepositTransaction::with(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator']);

        // Filter hanya untuk perjanjian yang aktif di tahun berjalan
        $currentYear = Carbon::now()->year;
        $query->whereHas('agreement', function ($agreementQuery) use ($currentYear) {
            $agreementQuery->where('status', 'active')
                ->whereYear('start_date', '<=', $currentYear)
                ->whereYear('end_date', '>=', $currentYear);
        });

        // Terapkan filter pencarian umum jika ada
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deposit_date', 'like', '%' . $search . '%')
                    ->orWhere('amount', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%')
                    ->orWhere('referral_code', 'like', '%' . $search . '%')
                    ->orWhereHas('agreement', function ($agreementQuery) use ($search) {
                        $agreementQuery->where('agreement_number', 'like', '%' . $search . '%')
                            ->orWhereHas('fieldCoordinator.user', function ($fcUserQuery) use ($search) {
                                $fcUserQuery->where('name', 'like', '%' . $search . '%');
                            })
                            ->orWhereHas('leader.user', function ($leaderUserQuery) use ($search) {
                                $leaderUserQuery->where('name', 'like', '%' . $search . '%');
                            });
                    })
                    ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                        $creatorQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Terapkan filter pencarian tanggal spesifik
        if ($searchDate) {
            $query->whereDate('deposit_date', $searchDate);
        }

        // Terapkan filter pencarian bulan
        if ($searchMonth) {
            $query->whereMonth('deposit_date', $searchMonth);
        }

        // Terapkan filter pencarian tahun
        if ($searchYear) {
            $query->whereYear('deposit_date', $searchYear);
        }

        // Terapkan filter pencarian rentang waktu
        if ($startDateRange && $endDateRange) {
            $query->whereBetween('deposit_date', [$startDateRange, $endDateRange]);
        } elseif ($startDateRange) {
            $query->whereDate('deposit_date', '>=', $startDateRange);
        } elseif ($endDateRange) {
            $query->whereDate('deposit_date', '<=', $endDateRange);
        }

        $depositTransactions = $query->latest('deposit_date')->paginate(10);

        return view('masterdata.deposit_transactions.index', compact(
            'depositTransactions',
            'search',
            'searchDate',
            'searchMonth',
            'searchYear',
            'startDateRange',
            'endDateRange'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Untuk Select2, kita tidak perlu mengirim semua perjanjian di awal
        // Mereka akan dimuat via AJAX
        $activeAgreements = collect(); // Kirim koleksi kosong atau null

        return view('masterdata.deposit_transactions.create', compact('activeAgreements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('DepositTransactionController@store: Request received.', $request->all());

        $validatedData = $request->validate([
            'agreement_id' => 'required|exists:agreements,id',
            // Validasi tanggal diubah agar hanya menerima hari ini atau sesudahnya
            'deposit_date' => 'required|date|after_or_equal:today',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
            'proof_of_transfer' => 'nullable|image|mimes:jpeg,png,jpg|max:300', // Maks 300KB
        ]);

        Log::info('DepositTransactionController@store: Validation successful.', $validatedData);

        try {
            $transactionData = Arr::except($validatedData, ['proof_of_transfer']);
            $transactionData['created_by_user_id'] = Auth::id();
            $transactionData['is_validated'] = false;

            // ✅ Hasilkan kode referensi unik yang aman di sisi server
            $transactionData['referral_code'] = 'TRXPRK-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));

            if ($request->hasFile('proof_of_transfer')) {
                $imageName = time() . '_proof.' . $request->proof_of_transfer->extension();
                // $request->proof_of_transfer->move(public_path('uploads/proofs'), $imageName);
                $transactionData['proof_of_transfer'] = $request->file('proof_of_transfer')
                    ->storeAs('uploads/proofs', $imageName, 'public');
            }

            DepositTransaction::create($transactionData);

            return redirect()->route('masterdata.deposit-transactions.index')
                ->with('success', 'Setoran berhasil dicatat!');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan setoran: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DepositTransaction $depositTransaction)
    {
        $depositTransaction->load(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator']);
        $uptProfile = UptProfile::firstOrFail();

        // ✅ LOGIKA BARU: Hitung detail bulan berdasarkan tanggal setoran

        $depositDate = Carbon::parse($depositTransaction->deposit_date)->addMonth(); // Menggunakan Carbon karena sudah di-cast di model

        $daysInMonth = $depositDate->daysInMonth;
        $monthName = $depositDate->translatedFormat('F');
        $year = $depositDate->year;

        return view('masterdata.deposit_transactions.show', compact(
            'depositTransaction',
            'uptProfile',
            'daysInMonth',
            'monthName',
            'year'
        ));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DepositTransaction $depositTransaction)
    {
        // Kode edit() Anda sudah benar, tidak perlu diubah.
        return view('masterdata.deposit_transactions.edit', compact('depositTransaction'));
    }

    /**
     * Mengupdate transaksi di database.
     */
    public function update(Request $request, DepositTransaction $depositTransaction)
    {
        // 1. Validasi, termasuk untuk file gambar baru
        $validatedData = $request->validate([
            'agreement_id' => ['required', 'exists:agreements,id'],
            'deposit_date' => ['required', 'date', Rule::unique('deposit_transactions')->where('agreement_id', $request->agreement_id)->ignore($depositTransaction->id)],
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
            'proof_of_transfer' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // Maks 1MB
        ]);

        // 2. Pisahkan data untuk diupdate
        $transactionData = Arr::except($validatedData, ['proof_of_transfer']);

        // 3. ✅ Logika untuk handle file upload baru
        if ($request->hasFile('proof_of_transfer')) {
            // Hapus bukti transfer lama jika ada
            if ($depositTransaction->proof_of_transfer) {
                Storage::disk('public')->delete($depositTransaction->proof_of_transfer);
            }

            // Simpan bukti transfer yang baru
            $imageName = time() . '_proof.' . $request->proof_of_transfer->extension();
            // $request->proof_of_transfer->move(public_path('uploads/proofs'), $imageName);
            $transactionData['proof_of_transfer'] = $request->file('proof_of_transfer')
                ->storeAs('uploads/proofs', $imageName, 'public');
        }

        $depositTransaction->update($transactionData);

        return redirect()->route('masterdata.deposit-transactions.index')
            ->with('success', 'Setoran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DepositTransaction $depositTransaction)
    {
        try {
            Storage::disk('public')->delete($depositTransaction->proof_of_transfer);
            $depositTransaction->delete();
        } catch (\Exception $e) {
            Log::error('DepositTransactionController@destroy: Error deleting deposit transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus setoran: ' . $e->getMessage());
        }

        return redirect()->route('masterdata.deposit-transactions.index')->with('success', 'Setoran berhasil dihapus!');
    }

    /**
     * Mark a deposit transaction as validated. (For Admin/Leader)
     */
    public function validateDeposit(DepositTransaction $depositTransaction)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isLeader()) {
            abort(403, 'Unauthorized action.');
        }

        $depositTransaction->update([
            'is_validated' => true,
            'validation_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Setoran berhasil divalidasi!');
    }

    public function searchActiveAgreements(Request $request)
    {
        $search = $request->input('term'); // Select2 sends the search term as 'term'
        Log::info('DepositTransactionController@searchActiveAgreements: Search term received: ' . $search);

        // Filter perjanjian yang berstatus 'active'
        $query = Agreement::where('status', 'active');

        // Terapkan pencarian berdasarkan nomor perjanjian atau nama koordinator lapangan
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('agreement_number', 'like', '%' . $search . '%')
                    ->orWhereHas('fieldCoordinator.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Eager load relasi yang dibutuhkan untuk menampilkan teks di Select2
        $agreements = $query->with(['fieldCoordinator.user', 'parkingLocations.roadSection'])
            ->limit(10) // Batasi hasil untuk performa
            ->get();

        $results = [];
        foreach ($agreements as $agreement) {
            $text = $agreement->agreement_number . ' (Korlap: ' . ($agreement->fieldCoordinator->user->name ?? 'N/A') . ')';
            if ($agreement->parkingLocations->isNotEmpty()) {
                $text .= ' - Lokasi: ' . $agreement->parkingLocations->pluck('name')->join(', ');
            }
            $results[] = [
                'id' => $agreement->id,
                'text' => $text,
                'daily_deposit_amount' => $agreement->daily_deposit_amount // <-- Data ini sudah diambil
            ];
        }

        Log::info('DepositTransactionController@searchActiveAgreements: Returning ' . count($results) . ' results.');
        return response()->json(['results' => $results]);
    }

    public function generatePdf(DepositTransaction $depositTransaction)
    {
        // Eager load semua relasi yang dibutuhkan
        // $depositTransaction->load(['agreement.fieldCoordinator.user', 'creator']);
        $depositTransaction->load(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator']);
        $uptProfile = UptProfile::firstOrFail();

        // ✅ LOGIKA BARU: Hitung detail bulan berdasarkan tanggal setoran
        $depositDate = Carbon::parse($depositTransaction->deposit_date)->addMonth();

        $daysInMonth = $depositDate->daysInMonth;
        $monthName = $depositDate->translatedFormat('F');
        $year = $depositDate->year;


        // Generate PDF
        $pdf = Pdf::loadView('pdf.deposit_receipt', compact(
            'depositTransaction',
            'uptProfile',
            'daysInMonth',
            'monthName',
            'year'
        ));

        // Tampilkan PDF di browser
        return $pdf->stream('bukti_setor_' . $depositTransaction->referral_code . '.pdf');
    }

    public function checkExistingTransaction(Agreement $agreement)
    {
        $now = Carbon::now();

        $existingTransaction = DepositTransaction::where('agreement_id', $agreement->id)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->first();

        if ($existingTransaction) {
            return response()->json([
                'exists' => true,
                'transaction' => [
                    'agreement_number' => $agreement->agreement_number,
                    'referral_code' => $existingTransaction->referral_code,
                    'show_url' => route('masterdata.deposit-transactions.show', $existingTransaction->id)
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }
}
