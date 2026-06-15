<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\AgreementHistory;
use App\Models\DepositTransaction;
use App\Models\Treasurer;
use App\Models\UptProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DepositTransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchDate = $request->input('search_date');
        $searchMonth = $request->input('search_month');
        $searchYear = $request->input('search_year');
        $startDateRange = $request->input('start_date_range');
        $endDateRange = $request->input('end_date_range');

        $query = DepositTransaction::with(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator']);

        $currentYear = Carbon::now()->year;
        $query->whereHas('agreement', function ($agreementQuery) use ($currentYear) {
            // ✅ Ambil yang active maupun pending (karena PKS baru menunggu setoran pertama)
            $agreementQuery->whereIn('status', ['active', 'pending'])
                ->whereYear('start_date', '<=', $currentYear)
                ->whereYear('end_date', '>=', $currentYear);
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deposit_date', 'like', '%'.$search.'%')
                    ->orWhere('amount', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%')
                    ->orWhere('referral_code', 'like', '%'.$search.'%')
                    ->orWhereHas('agreement', function ($agreementQuery) use ($search) {
                        $agreementQuery->where('agreement_number', 'like', '%'.$search.'%')
                            ->orWhereHas('fieldCoordinator.user', function ($fcUserQuery) use ($search) {
                                $fcUserQuery->where('name', 'like', '%'.$search.'%');
                            })
                            ->orWhereHas('leader.user', function ($leaderUserQuery) use ($search) {
                                $leaderUserQuery->where('name', 'like', '%'.$search.'%');
                            });
                    })
                    ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                        $creatorQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($searchDate) {
            $query->whereDate('deposit_date', $searchDate);
        }
        if ($searchMonth) {
            $query->whereMonth('deposit_date', $searchMonth);
        }
        if ($searchYear) {
            $query->whereYear('deposit_date', $searchYear);
        }
        if ($startDateRange && $endDateRange) {
            $query->whereBetween('deposit_date', [$startDateRange, $endDateRange]);
        } elseif ($startDateRange) {
            $query->whereDate('deposit_date', '>=', $startDateRange);
        } elseif ($endDateRange) {
            $query->whereDate('deposit_date', '<=', $endDateRange);
        }

        $depositTransactions = $query->latest('deposit_date')->paginate(10);

        return view('masterdata.deposit_transactions.index', compact(
            'depositTransactions', 'search', 'searchDate', 'searchMonth', 'searchYear', 'startDateRange', 'endDateRange'
        ));
    }

    public function create()
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        // Cari Bendahara yang sedang Aktif Menjabat
        $activeTreasurer = Treasurer::with('user')->whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->first();

        // ❌ GERBANG AUDIT: Tolak jika tidak ada bendahara
        if (! $activeTreasurer) {
            return redirect()->route('masterdata.deposit-transactions.index')
                ->with('error', 'AKSES DITOLAK: Tidak dapat mencatat setoran karena belum ada Bendahara Penerimaan yang Aktif di sistem. Harap tugaskan Bendahara terlebih dahulu!');
        }

        $activeAgreements = collect();

        return view('masterdata.deposit_transactions.create', compact('activeAgreements', 'activeTreasurer'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        // ✅ PESAN ERROR BAHASA INDONESIA
        $messages = [
            'agreement_id.required' => 'Perjanjian kerjasama wajib dipilih.',
            'agreement_id.exists' => 'Data perjanjian tidak ditemukan di sistem.',
            'deposit_date.required' => 'Tanggal setoran wajib diisi.',
            'deposit_date.date' => 'Format tanggal setoran tidak valid.',
            'deposit_date.before_or_equal' => 'Tanggal setoran pada struk tidak boleh melebihi hari ini (dari masa depan).',
            'amount.required' => 'Jumlah setoran wajib diisi.',
            'amount.numeric' => 'Jumlah setoran harus berupa angka yang valid.',
            'amount.min' => 'Jumlah setoran tidak boleh minus.',
            'notes.max' => 'Catatan tambahan maksimal 255 karakter.',
            'proof_of_transfer.image' => 'File bukti transfer harus berupa gambar.',
            'proof_of_transfer.mimes' => 'Format gambar bukti transfer harus berupa jpeg, png, atau jpg.',
            'proof_of_transfer.max' => 'Ukuran gambar bukti transfer tidak boleh lebih dari 1MB.',
            'discount_amount.numeric' => 'Nominal potongan harus berupa angka.',
            'discount_amount.min' => 'Nominal potongan tidak boleh kurang dari 0.',
            'discount_notes.required_with' => 'Alasan potongan wajib diisi jika Anda memasukkan nominal potongan/keringanan.',
        ];

        $validatedData = $request->validate([
            'agreement_id' => 'required|exists:agreements,id',
            // ✅ LOGIKA DIPERBAIKI: Tanggal struk boleh hari ini atau masa lalu (kemarin), tapi gak boleh besok!
            'deposit_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_notes' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('discount_amount') > 0 && empty($value)) {
                        $fail('Alasan potongan wajib diisi jika Anda memasukkan nominal potongan/keringanan.');
                    }
                },
            ],
            'notes' => 'nullable|string|max:255',
            'proof_of_transfer' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ], $messages);

        try {
            $transactionData = Arr::except($validatedData, ['proof_of_transfer']);
            $transactionData['created_by_user_id'] = Auth::id();
            // ✅ Suntik otomatis Bendahara yang sedang aktif
            $activeTreasurer = Treasurer::whereHas('user', function ($q) {
                $q->where('is_active', true);
            })->first();

            if (! $activeTreasurer) {
                return redirect()->back()->with('error', 'Gagal: Bendahara mendadak tidak aktif.')->withInput();
            }
            $transactionData['treasurer_id'] = $activeTreasurer->id;
            $transactionData['is_validated'] = false;
            $transactionData['referral_code'] = 'TRXPRK-'.now()->format('YmdHis').'-'.strtoupper(Str::random(6));

            if (!empty($transactionData['discount_amount']) && $transactionData['discount_amount'] > 0) {
                $transactionData['discount_approved_by_user_id'] = Auth::id();
            } else {
                $transactionData['discount_amount'] = 0;
                $transactionData['discount_notes'] = null;
                $transactionData['discount_approved_by_user_id'] = null;
            }

            if ($request->hasFile('proof_of_transfer')) {
                $imageName = time().'_proof.'.$request->proof_of_transfer->extension();
                $transactionData['proof_of_transfer'] = $request->file('proof_of_transfer')->storeAs('uploads/proofs', $imageName, 'public');
            }

            DepositTransaction::create($transactionData);

            return redirect()->route('masterdata.deposit-transactions.index')->with('success', 'Setoran berhasil dicatat! Menunggu validasi.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan setoran: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data ke database.')->withInput();
        }
    }

    public function show(DepositTransaction $depositTransaction)
    {
        $depositTransaction->load(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator', 'treasurer.user']);
        $uptProfile = UptProfile::firstOrFail();

        // ✅ LOGIKA BARU: Tentukan Bulan Target berdasarkan Urutan Setoran (Sequence)
        $sequence = DepositTransaction::where('agreement_id', $depositTransaction->agreement_id)
            ->where('id', '<=', $depositTransaction->id)
            ->count();

        $targetDate = Carbon::parse($depositTransaction->agreement->start_date)
            ->startOfMonth()
            ->addMonths($sequence - 1);

        $daysInMonth = $targetDate->daysInMonth;
        $monthName = $targetDate->translatedFormat('F');
        $year = $targetDate->year;

        return view('masterdata.deposit_transactions.show', compact('depositTransaction', 'uptProfile', 'daysInMonth', 'monthName', 'year'));
    }

    public function edit(DepositTransaction $depositTransaction)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        if ($depositTransaction->is_validated && Auth::user()->hasRole('staff_keu')) {
            return redirect()->route('masterdata.deposit-transactions.index')->with('error', 'Transaksi yang sudah divalidasi tidak dapat diedit.');
        }

        return view('masterdata.deposit_transactions.edit', compact('depositTransaction'));
    }

    public function update(Request $request, DepositTransaction $depositTransaction)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        if ($depositTransaction->is_validated && Auth::user()->hasRole('staff_keu')) {
            return redirect()->route('masterdata.deposit-transactions.index')->with('error', 'Transaksi yang sudah divalidasi tidak dapat diedit.');
        }

        // ✅ PESAN ERROR BAHASA INDONESIA
        $messages = [
            'agreement_id.required' => 'Perjanjian kerjasama wajib dipilih.',
            'agreement_id.exists' => 'Data perjanjian tidak ditemukan di sistem.',
            'deposit_date.required' => 'Tanggal setoran wajib diisi.',
            'deposit_date.date' => 'Format tanggal setoran tidak valid.',
            'deposit_date.before_or_equal' => 'Tanggal setoran pada struk tidak boleh melebihi hari ini.',
            'deposit_date.unique' => 'Transaksi untuk tanggal dan PKS tersebut sudah pernah dicatat sebelumnya.',
            'amount.required' => 'Jumlah setoran wajib diisi.',
            'amount.numeric' => 'Jumlah setoran harus berupa angka yang valid.',
            'amount.min' => 'Jumlah setoran tidak boleh minus.',
            'notes.max' => 'Catatan tambahan maksimal 255 karakter.',
            'proof_of_transfer.image' => 'File bukti transfer harus berupa gambar.',
            'proof_of_transfer.mimes' => 'Format gambar bukti transfer harus berupa jpeg, png, atau jpg.',
            'proof_of_transfer.max' => 'Ukuran gambar bukti transfer tidak boleh lebih dari 1MB.',
            'discount_amount.numeric' => 'Nominal potongan harus berupa angka.',
            'discount_amount.min' => 'Nominal potongan tidak boleh kurang dari 0.',
            'discount_notes.required_with' => 'Alasan potongan wajib diisi jika Anda memasukkan nominal potongan/keringanan.',
        ];

        $validatedData = $request->validate([
            'agreement_id' => ['required', 'exists:agreements,id'],
            'deposit_date' => [
                'required',
                'date',
                'before_or_equal:today', // ✅ Logika diperbaiki
                Rule::unique('deposit_transactions')->where('agreement_id', $request->agreement_id)->ignore($depositTransaction->id),
            ],
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_notes' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('discount_amount') > 0 && empty($value)) {
                        $fail('Alasan potongan wajib diisi jika Anda memasukkan nominal potongan/keringanan.');
                    }
                },
            ],
            'notes' => 'nullable|string|max:255',
            'proof_of_transfer' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ], $messages);

        $transactionData = Arr::except($validatedData, ['proof_of_transfer']);

        if (!empty($transactionData['discount_amount']) && $transactionData['discount_amount'] > 0) {
            $transactionData['discount_approved_by_user_id'] = Auth::id();
        } else {
            $transactionData['discount_amount'] = 0;
            $transactionData['discount_notes'] = null;
            $transactionData['discount_approved_by_user_id'] = null;
        }

        if ($request->hasFile('proof_of_transfer')) {
            if ($depositTransaction->proof_of_transfer && Storage::disk('public')->exists($depositTransaction->proof_of_transfer)) {
                Storage::disk('public')->delete($depositTransaction->proof_of_transfer);
            }
            $imageName = time().'_proof.'.$request->proof_of_transfer->extension();
            $transactionData['proof_of_transfer'] = $request->file('proof_of_transfer')->storeAs('uploads/proofs', $imageName, 'public');
        }

        $depositTransaction->update($transactionData);

        return redirect()->route('masterdata.deposit-transactions.index')->with('success', 'Setoran berhasil diperbarui.');
    }

    public function destroy(DepositTransaction $depositTransaction)
    {
        if (! Auth::user()->hasRole('admin')) {
            return redirect()->route('masterdata.deposit-transactions.index')->with('error', 'Aksi ditolak.');
        }

        try {
            if ($depositTransaction->proof_of_transfer && Storage::disk('public')->exists($depositTransaction->proof_of_transfer)) {
                Storage::disk('public')->delete($depositTransaction->proof_of_transfer);
            }
            $depositTransaction->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus setoran.');
        }

        return redirect()->route('masterdata.deposit-transactions.index')->with('success', 'Setoran berhasil dihapus!');
    }

    public function validateDeposit(DepositTransaction $depositTransaction)
    {
        // 1. Gerbang Keamanan
        if (! Auth::user()->hasRole('admin') && ! Auth::user()->hasRole('staff_keu') && ! Auth::user()->hasRole('bendahara')) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        $isFirstDeposit = false;
        $agreement = $depositTransaction->agreement;

        DB::beginTransaction();
        try {
            // 2. Update Status Transaksi Setoran
            $depositTransaction->update([
                'is_validated' => true,
                'validation_date' => now(),
                'validated_by_user_id' => Auth::id(),
            ]);

            // 3. LOGIKA AKTIVASI PKS Otomatis
            if ($agreement && $agreement->status === 'pending') {
                $agreement->update(['status' => 'active']);
                $isFirstDeposit = true; // Tandai bahwa ini adalah momen PKS aktif

                // ✅ KODE YANG DIPERBAIKI SESUAI MODEL AGREEMENT HISTORY
                AgreementHistory::create([
                    'agreement_id' => $agreement->id,
                    'event_type' => 'status_changed', // Tadi salah ketik 'action'
                    'changed_by_user_id' => Auth::id(),       // Tadi salah ketik 'user_id'
                    'old_value' => ['status' => 'pending'], // Tambahan info log
                    'new_value' => ['status' => 'active'],  // Tambahan info log
                    'notes' => 'PKS otomatis diaktifkan setelah validasi setoran pertama oleh Bendahara/Keuangan.',
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memvalidasi setoran: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memvalidasi setoran.');
        }

        // ✅ 4. INTEGRASI WHATSAPP FONNTE (Eksekusi setelah DB aman)
        if ($isFirstDeposit) {
            try {
                $uptProfile = UptProfile::first();
                $token = $uptProfile->api_token_fonnte ?? null;
                $phone = $agreement->fieldCoordinator->phone_number ?? null;

                if ($token && $phone) {
                    $korlapName = $agreement->fieldCoordinator->user->name ?? 'Bapak/Ibu';
                    $agreementNumber = $agreement->agreement_number;
                    $startDate = Carbon::parse($agreement->start_date)->translatedFormat('d F Y');
                    $endDate = Carbon::parse($agreement->end_date)->translatedFormat('d F Y');
                    $amount = number_format($depositTransaction->amount, 0, ',', '.');
                    $uptName = $uptProfile->name ?? 'UPT Perparkiran';

                    // 📝 Pesan Formal Instansi
                    $message = "*PEMBERITAHUAN RESMI {$uptName}*\n";
                    $message .= "-------------------------------------------------------\n\n";
                    $message .= "Yth. {$korlapName},\n\n";
                    $message .= "Bersama pesan ini, kami sampaikan bahwa Setoran Pembayaran Pertama Anda telah *BERHASIL DIVALIDASI*.\n\n";
                    $message .= "Dengan demikian, Perjanjian Kerjasama (PKS) Anda kini dinyatakan *AKTIF* dan sah berlaku.\n\n";
                    $message .= "*Rincian PKS:*\n";
                    $message .= "- No. Dokumen: {$agreementNumber}\n";
                    $message .= "- Masa Berlaku: {$startDate} s/d {$endDate}\n";
                    $message .= "- Setoran Pertama: Rp {$amount}\n\n";
                    $message .= "Anda sudah dapat melaksanakan tata kelola perparkiran sesuai dengan titik lokasi yang telah ditetapkan. Harap senantiasa mematuhi seluruh peraturan dan Standar Operasional Prosedur (SOP) yang berlaku.\n\n";
                    $message .= "Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.\n\n";
                    $message .= "Hormat kami,\n";
                    $message .= "*{$uptName}*";

                    // 🚀 Tembak API Fonnte
                    $response = Http::withHeaders([
                        'Authorization' => $token,
                    ])->post('https://api.fonnte.com/send', [
                        'target' => $phone,
                        'message' => $message,
                        'countryCode' => '62', // Opsional, jaga-jaga kalau format 08
                    ]);

                    if (! $response->successful()) {
                        Log::warning("Fonnte WA gagal terkirim ke {$phone}: ".$response->body());
                    }
                }
            } catch (\Exception $e) {
                // Jangan gagalkan aplikasi kalau WA error
                Log::error('Error mengirim WA Fonnte: '.$e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Setoran berhasil divalidasi'.($isFirstDeposit ? ', status PKS AKTIF, dan Notifikasi WA telah dikirim ke Korlap!' : '!'));
    }

    public function searchActiveAgreements(Request $request)
    {
        $search = $request->input('term');

        // ✅ Tampilkan Active dan Pending (yang belum bayar setoran pertama)
        $query = Agreement::whereIn('status', ['active', 'pending']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('agreement_number', 'like', '%'.$search.'%')
                    ->orWhereHas('fieldCoordinator.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $agreements = $query->with(['fieldCoordinator.user', 'parkingLocations.roadSection'])->limit(10)->get();

        $results = [];
        foreach ($agreements as $agreement) {
            $text = $agreement->agreement_number.' (Korlap: '.($agreement->fieldCoordinator->user->name ?? 'N/A').')';
            $results[] = ['id' => $agreement->id, 'text' => $text];
        }

        return response()->json(['results' => $results]);
    }

    public function checkExistingTransaction(Agreement $agreement)
    {
        // ✅ LOGIKA BARU: KUNCI WAKTU PEMBAYARAN & HITUNG BULAN TARGET
        $paidMonthsCount = DepositTransaction::where('agreement_id', $agreement->id)->count();

        // Target Bulan Pembayaran
        $startDate = Carbon::parse($agreement->start_date)->startOfMonth();
        $targetDate = $startDate->copy()->addMonths($paidMonthsCount);
        $endDate = Carbon::parse($agreement->end_date)->endOfMonth();

        // Cek apakah PKS sudah lunas sampai akhir kontrak
        if ($targetDate->gt($endDate)) {
            return response()->json([
                'can_pay' => false,
                'message' => 'Semua kewajiban setoran untuk PKS ini (hingga <strong>'.$endDate->translatedFormat('F Y').'</strong>) sudah terpenuhi dan Lunas.',
            ]);
        }

        $canPay = true;
        $message = '';
        $today = Carbon::today();

        if ($paidMonthsCount > 0) {
            // Aturan 10 hari sebelum bulan target dimulai
            $allowedPaymentMonth = $targetDate->copy()->subMonth();
            $daysInAllowedMonth = $allowedPaymentMonth->daysInMonth;

            // Tanggal buka form = Total Hari di Bulan Sebelumnya dikurangi 10 (H-10)
            $startAllowedDate = $allowedPaymentMonth->copy()->startOfMonth()->addDays($daysInAllowedMonth - 10);

            if ($today->lt($startAllowedDate)) {
                $canPay = false;
                $formattedStartAllowed = $startAllowedDate->translatedFormat('d F Y');
                $targetMonthName = $targetDate->translatedFormat('F Y');
                $message = "Pembayaran untuk bulan <strong>{$targetMonthName}</strong> belum dibuka. <br>Form pembayaran baru dapat diakses mulai tanggal <strong>{$formattedStartAllowed}</strong> (10 Hari sebelum bulan target).";
            }
        }

        if (! $canPay) {
            return response()->json(['can_pay' => false, 'message' => $message]);
        }

        // Kalkulasi Total Setoran
        $daysInTargetMonth = $targetDate->daysInMonth;
        $dailyAmount = $agreement->daily_deposit_amount ?? 0;
        $totalAmount = $daysInTargetMonth * $dailyAmount;

        return response()->json([
            'can_pay' => true,
            'target_month_name' => $targetDate->translatedFormat('F Y'),
            'days_in_month' => $daysInTargetMonth,
            'daily_amount' => $dailyAmount,
            'total_amount' => $totalAmount,
        ]);
    }

    public function generatePdf(DepositTransaction $depositTransaction)
    {
        $depositTransaction->load(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator', 'treasurer.user']);
        $uptProfile = UptProfile::firstOrFail();

        // Tentukan Bulan Target berdasarkan Urutan
        $sequence = DepositTransaction::where('agreement_id', $depositTransaction->agreement_id)
            ->where('id', '<=', $depositTransaction->id)
            ->count();

        $targetDate = Carbon::parse($depositTransaction->agreement->start_date)->startOfMonth()->addMonths($sequence - 1);
        $daysInMonth = $targetDate->daysInMonth;
        $monthName = $targetDate->translatedFormat('F');
        $year = $targetDate->year;

        $pdf = Pdf::loadView('pdf.deposit_receipt', compact('depositTransaction', 'uptProfile', 'daysInMonth', 'monthName', 'year'));

        return $pdf->stream('bukti_setor_'.$depositTransaction->referral_code.'.pdf');
    }
}
