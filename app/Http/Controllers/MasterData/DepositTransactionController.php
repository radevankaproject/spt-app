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
        $status = $request->input('status', 'jatuh_tempo');

        $arrears = collect();
        if ($status === 'jatuh_tempo') {
            $allAgreements = Agreement::with(['fieldCoordinator.user', 'leader.user'])->get();
            $items = [];
            $searchLower = $search ? strtolower($search) : null;
            
            foreach ($allAgreements as $agr) {
                // Filter pencarian
                if ($searchLower) {
                    $match = false;
                    if (str_contains(strtolower($agr->agreement_number), $searchLower)) $match = true;
                    if ($agr->fieldCoordinator && $agr->fieldCoordinator->user && str_contains(strtolower($agr->fieldCoordinator->user->name), $searchLower)) $match = true;
                    if (!$match) continue;
                }

                // Kita buat fungsi khusus checkExistingTransaction() berjalan
                $calc = $this->checkExistingTransaction($agr);
                $resp = json_decode($calc->getContent(), true);
                if (isset($resp['can_pay']) && $resp['can_pay'] && !empty($resp['available_months'])) {
                    $items[] = (object) [
                        'agreement' => $agr,
                        'month_label' => $resp['available_months'][0]['label'],
                        'amount' => $resp['available_months'][0]['amount']
                    ];
                }
            }
            $arrears = collect($items);
            $depositTransactions = collect(); // empty for this tab
        } else {
            $query = DepositTransaction::with(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator']);

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

        if ($status === 'pending') {
            $query->where('is_validated', 0);
        } elseif ($status === 'validated') {
            $query->where('is_validated', 1);
        }

        if ($searchDate) {
            $query->whereDate('deposit_date', $searchDate);
        }
        if ($searchMonth) {
            $query->whereMonth('transaction_month', $searchMonth);
        }
        if ($searchYear) {
            $query->whereYear('transaction_month', $searchYear);
        }
            if ($startDateRange && $endDateRange) {
                $query->whereBetween('deposit_date', [$startDateRange, $endDateRange]);
            } elseif ($startDateRange) {
                $query->whereDate('deposit_date', '>=', $startDateRange);
            } elseif ($endDateRange) {
                $query->whereDate('deposit_date', '<=', $endDateRange);
            }

            $depositTransactions = $query->latest('deposit_date')->paginate(10);
        }

        return view('masterdata.deposit_transactions.index', compact(
            'depositTransactions', 'arrears', 'search', 'searchDate', 'searchMonth', 'searchYear', 'startDateRange', 'endDateRange', 'status'
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
        $targetAgreement = null;
        if (request()->has('target_agreement_id')) {
            $targetAgreement = \App\Models\Agreement::with('fieldCoordinator.user')->find(request('target_agreement_id'));
        }

        return view('masterdata.deposit_transactions.create', compact('activeAgreements', 'activeTreasurer', 'targetAgreement'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        // ✅ PESAN ERROR BAHASA INDONESIA
        $messages = [
            'agreement_id.required' => 'Perjanjian kerjasama wajib dipilih.',
            'agreement_id.exists' => 'Data perjanjian tidak ditemukan di sistem.',
            'transaction_month.required' => 'Bulan setoran wajib dipilih.',
            'transaction_month.date' => 'Format bulan setoran tidak valid.',
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

        if ($request->has('target_agreement_id') && !empty($request->target_agreement_id)) {
            $request->merge(['agreement_id' => $request->target_agreement_id]);
        }

        if ($request->has('transaction_month') && strlen($request->transaction_month) === 7) {
            $request->merge(['transaction_month' => $request->transaction_month . '-01']);
        }

        $validatedData = $request->validate([
            'agreement_id' => 'required|exists:agreements,id',
            'transaction_month' => [
                'required',
                'date',
                Rule::unique('deposit_transactions')->where('agreement_id', $request->agreement_id),
            ],
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
            $agreement = Agreement::findOrFail($validatedData['agreement_id']);
            $pendingTransactionsCount = DepositTransaction::whereHas('agreement', function($q) use ($agreement) {
                $q->where('field_coordinator_id', $agreement->field_coordinator_id);
            })->where('is_validated', 0)->count();

            if ($pendingTransactionsCount > 0) {
                return redirect()->back()->with('error', 'Gagal: Terdapat setoran dari Koordinator ini yang masih Menunggu Validasi. Sistem dikunci sementara sampai Bendahara memvalidasi setoran sebelumnya.')->withInput();
            }

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

            $newDeposit = DepositTransaction::create($transactionData);
            // Kirim Notifikasi ke Staff Keuangan
            $staffKeus = \App\Models\User::whereIn('role', ['admin', 'staff_keu'])->get();
            \Illuminate\Support\Facades\Notification::send($staffKeus, new \App\Notifications\DepositPendingNotification($newDeposit));

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

        if ($depositTransaction->transaction_month) {
            $targetDate = Carbon::parse($depositTransaction->transaction_month);
        } else {
            $sequence = DepositTransaction::where('agreement_id', $depositTransaction->agreement_id)
                ->where('id', '<=', $depositTransaction->id)
                ->count();
            $targetDate = Carbon::parse($depositTransaction->agreement->start_date)
                ->startOfMonth()
                ->addMonths($sequence - 1);
        }

        $daysInMonth = $targetDate->daysInMonth;
        $monthName = $targetDate->translatedFormat('F');
        $year = $targetDate->year;

        return view('masterdata.deposit_transactions.show', compact('depositTransaction', 'uptProfile', 'daysInMonth', 'monthName', 'year'));
    }

    public function edit(DepositTransaction $depositTransaction)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        if ($depositTransaction->is_validated && !Auth::user()->hasRole('admin')) {
            return redirect()->route('masterdata.deposit-transactions.index')->with('error', 'Transaksi yang sudah divalidasi tidak dapat diedit.');
        }

        return view('masterdata.deposit_transactions.edit', compact('depositTransaction'));
    }

    public function update(Request $request, DepositTransaction $depositTransaction)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        if ($depositTransaction->is_validated && !Auth::user()->hasRole('admin')) {
            return redirect()->route('masterdata.deposit-transactions.index')->with('error', 'Transaksi yang sudah divalidasi tidak dapat diedit.');
        }

        // ✅ PESAN ERROR BAHASA INDONESIA
        $messages = [
            'agreement_id.required' => 'Perjanjian kerjasama wajib dipilih.',
            'agreement_id.exists' => 'Data perjanjian tidak ditemukan di sistem.',
            'transaction_month.required' => 'Bulan setoran wajib dipilih.',
            'transaction_month.date' => 'Format bulan setoran tidak valid.',
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

        if ($request->has('target_agreement_id') && !empty($request->target_agreement_id)) {
            $request->merge(['agreement_id' => $request->target_agreement_id]);
        }

        if ($request->has('transaction_month') && strlen($request->transaction_month) === 7) {
            $request->merge(['transaction_month' => $request->transaction_month . '-01']);
        }

        $validatedData = $request->validate([
            'agreement_id' => ['required', 'exists:agreements,id'],
            'transaction_month' => [
                'required',
                'date',
                Rule::unique('deposit_transactions')->where('agreement_id', $request->agreement_id)->ignore($depositTransaction->id),
            ],
            'deposit_date' => [
                'required',
                'date',
                'before_or_equal:today', // ✅ Logika diperbaiki
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
        if (! Auth::user()->hasRole('admin') && ! Auth::user()->hasRole('treasurer')) {
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
        if (! Auth::user()->hasRole('admin') && ! Auth::user()->hasRole('staff_keu') && ! Auth::user()->hasRole('treasurer')) {
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

            // Kirim Notifikasi ke Bendahara
            if ($depositTransaction->treasurer && $depositTransaction->treasurer->user) {
                $depositTransaction->treasurer->user->notify(new \App\Notifications\DepositValidatedNotification($depositTransaction));
            }

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
        $today = Carbon::today();

        // Cek apakah korlap ini memiliki transaksi yang belum divalidasi
        $pendingTransactionsCount = DepositTransaction::whereHas('agreement', function($q) use ($agreement) {
            $q->where('field_coordinator_id', $agreement->field_coordinator_id);
        })->where('is_validated', 0)->count();

        if ($pendingTransactionsCount > 0) {
            return response()->json([
                'can_pay' => false,
                'message' => 'Terdapat setoran dari Koordinator ini yang masih <strong>Menunggu Validasi</strong>. Sistem dikunci sementara sampai Bendahara memvalidasi setoran sebelumnya.',
            ]);
        }
        
        // Fungsi helper untuk menghitung bulan tersedia
        $calculateAvailableMonths = function($agr, $isTunggakan = false) use ($today) {
            $paid = DepositTransaction::where('agreement_id', $agr->id)
                            ->whereNotNull('transaction_month')
                            ->pluck('transaction_month')
                            ->map(fn($date) => Carbon::parse($date)->format('Y-m'))
                            ->toArray();

            $start = Carbon::parse($agr->start_date)->startOfMonth();
            $end = Carbon::parse($agr->end_date)->endOfMonth();
            $avail = [];
            $curr = $start->copy();
            
            $nextAvailDate = null;
            $nextMnthName = null;
            
            while ($curr->lte($end)) {
                $monthStr = $curr->format('Y-m');
                if (!in_array($monthStr, $paid)) {
                    // Aturan 7 hari sebelum akhir bulan target dikurangi 1 bulan (bulan sebelumnya)
                    $allowedMonth = $curr->copy()->subMonth();
                    $startAllowed = $allowedMonth->copy()->startOfMonth()->addDays($allowedMonth->daysInMonth - 7);
                    
                    if ($today->gte($startAllowed)) {
                        $days = $curr->daysInMonth;
                        $dailyAmt = $agr->daily_deposit_amount ?? 0;
                        $label = $curr->translatedFormat('F Y');
                        if ($isTunggakan) {
                            $label .= " (Tunggakan {$agr->agreement_number})";
                        }
                        
                        $avail[] = [
                            'date' => $curr->format('Y-m-01'),
                            'label' => $label,
                            'days' => $days,
                            'amount' => $days * $dailyAmt,
                            'agreement_id' => $agr->id,
                            'daily_amount' => $dailyAmt
                        ];
                    } else {
                        if (!$nextAvailDate && !$isTunggakan) {
                            $nextAvailDate = $startAllowed;
                            $nextMnthName = $curr->translatedFormat('F Y');
                        }
                        break;
                    }
                }
                $curr->addMonth();
            }
            
            $allMnths = collect(new \DatePeriod($start, new \DateInterval('P1M'), $end->copy()->startOfMonth()->addMonth()))->map(fn($d) => $d->format('Y-m'))->toArray();
            $isFullyPaid = empty(array_diff($allMnths, $paid));
            
            return [
                'available' => $avail,
                'is_fully_paid' => $isFullyPaid,
                'next_date' => $nextAvailDate,
                'next_month' => $nextMnthName
            ];
        };

        // Hitung untuk PKS Aktif/Utama
        $mainCalc = $calculateAvailableMonths($agreement);
        $availableMonths = $mainCalc['available'];
        
        // Cari PKS Expired untuk Korlap yang sama
        $expiredAgreements = Agreement::where('field_coordinator_id', $agreement->field_coordinator_id)
            ->where('status', 'expired')
            ->get();
            
        foreach ($expiredAgreements as $expAgr) {
            $expCalc = $calculateAvailableMonths($expAgr, true);
            if (!empty($expCalc['available'])) {
                $availableMonths = array_merge($availableMonths, $expCalc['available']);
            }
        }

        if (empty($availableMonths)) {
            if ($mainCalc['is_fully_paid']) {
                $endDate = Carbon::parse($agreement->end_date)->endOfMonth();
                return response()->json([
                    'can_pay' => false,
                    'message' => 'Semua kewajiban setoran untuk PKS ini (hingga <strong>'.$endDate->translatedFormat('F Y').'</strong>) sudah terpenuhi dan Lunas.',
                ]);
            }
            
            $msg = 'Belum ada tagihan baru yang dapat dibayarkan saat ini.';
            if ($mainCalc['next_date']) {
                $msg = "Belum Saatnya Membayar.<br>Tagihan untuk bulan <strong>{$mainCalc['next_month']}</strong> baru dapat dibayarkan mulai tanggal <strong>{$mainCalc['next_date']->translatedFormat('d F Y')}</strong> (7 Hari sebelum akhir bulan sebelumnya).";
            }

            return response()->json([
                'can_pay' => false,
                'message' => $msg
            ]);
        }

        // Urutkan berdasarkan tanggal tertua agar frontend bisa memvalidasi tunggakan tertua
        usort($availableMonths, fn($a, $b) => $a['date'] <=> $b['date']);

        return response()->json([
            'can_pay' => true,
            'available_months' => $availableMonths,
            'daily_amount' => $agreement->daily_deposit_amount ?? 0,
        ]);
    }

    public function generatePdf(DepositTransaction $depositTransaction)
    {
        $depositTransaction->load(['agreement.fieldCoordinator.user', 'agreement.leader.user', 'creator', 'treasurer.user']);
        $uptProfile = UptProfile::firstOrFail();

        if ($depositTransaction->transaction_month) {
            $targetDate = Carbon::parse($depositTransaction->transaction_month);
        } else {
            $sequence = DepositTransaction::where('agreement_id', $depositTransaction->agreement_id)
                ->where('id', '<=', $depositTransaction->id)
                ->count();
            $targetDate = Carbon::parse($depositTransaction->agreement->start_date)
                ->startOfMonth()
                ->addMonths($sequence - 1);
        }
        $daysInMonth = $targetDate->daysInMonth;
        $monthName = $targetDate->translatedFormat('F');
        $year = $targetDate->year;

        $pdf = Pdf::loadView('pdf.deposit_receipt', compact('depositTransaction', 'uptProfile', 'daysInMonth', 'monthName', 'year'));

        return $pdf->stream('bukti_setor_'.$depositTransaction->referral_code.'.pdf');
    }
}
