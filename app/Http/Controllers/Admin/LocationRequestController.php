<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgreementHistory;
use App\Models\LocationRequest;
use App\Models\LocationRequestReview;
use App\Models\ParkingLocation;
use App\Models\ParkingLocationHistory;
use App\Models\RoadSection; // ✅ WAJIB IMPORT MODEL INI
use App\Models\UptProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationRequestController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $type = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Gunakan Eager Loading
        $query = LocationRequest::with(['agreement.fieldCoordinator.user', 'parkingLocation.roadSection']);

        // 1. SMART SEARCH (Nama Titik, Ruas Jalan, Nama Korlap, No PKS)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('road_section_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('agreement.fieldCoordinator.user', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%"); // Cari Nama Korlap
                    })
                    ->orWhereHas('agreement', function ($q3) use ($search) {
                        $q3->where('agreement_number', 'LIKE', "%{$search}%"); // Cari No PKS
                    });
            });
        }

        // 2. FILTER STATUS
        if ($status) {
            $query->where('status', $status);
        }

        // 3. FILTER TIPE PENGAJUAN
        if ($type) {
            $query->where('request_type', $type);
        }

        // 4. FILTER RENTANG TANGGAL
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Urutkan berdasarkan prioritas status lalu terbaru, dan gunakan PAGINATION (Bukan Get!)
        $requests = $query->orderByRaw("FIELD(status, 'pending', 'surveyed', 'approved', 'rejected')")
            ->latest()
            ->paginate(15);

        return view('admin.location_requests.index', compact('requests', 'search', 'status', 'type', 'startDate', 'endDate'));
    }

    public function show(LocationRequest $locationRequest)
    {
        // Load relasi standar pengajuan
        $locationRequest->load(['agreement.fieldCoordinator.user', 'parkingLocation.roadSection', 'review.reviewer']);

        // ✅ LOGIKA SMART COLLISION DETECTION
        $similarLocations = collect();

        if ($locationRequest->request_type === 'add' && $locationRequest->name) {
            $keyword = $locationRequest->name;

            // Cari lokasi kembar & Eager Load sekalian data Pemilik PKS-nya!
            $similarLocations = ParkingLocation::with([
                'roadSection',
                'agreements' => function ($query) {
                    // Ambil PKS yang sedang aktif mengikat lokasi tersebut
                    $query->whereIn('agreements.status', ['active', 'pending_renewal'])
                        ->where('agreement_parking_locations.status', 'active')
                        ->with('fieldCoordinator.user');
                },
            ])
                ->where('name', 'LIKE', "%{$keyword}%")
                ->limit(10)
                ->get();
        }

        // ✅ PERBAIKAN BUG: Ambil data ruas jalan untuk dropdown di Modal Approve
        $roadSections = RoadSection::orderBy('zone', 'asc')->orderBy('name', 'asc')->get();

        // ✅ Jangan lupa tambahkan 'roadSections' ke dalam compact()
        return view('admin.location_requests.show', compact('locationRequest', 'similarLocations', 'roadSections'));
    }

    // 1. STAFF MENYIMPAN HASIL SURVEY LAPANGAN
    public function storeReview(Request $request, LocationRequest $locationRequest)
    {
        $data = $request->validate([
            'survey_date' => 'required|date',
            'survey_notes' => 'required|string',
            'recommended_deposit' => 'required|numeric|min:0',
            'report_document' => 'nullable|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('report_document')) {
            $data['report_document'] = $request->file('report_document')->store('location_requests/reports', 'public');
        }

        $data['location_request_id'] = $locationRequest->id;
        $data['reviewer_id'] = Auth::id();

        LocationRequestReview::create($data);
        $locationRequest->update(['status' => 'surveyed']);

        // KIRIM NOTIFIKASI WHATSAPP KE KORLAP
        $korlapPhone = $locationRequest->agreement->fieldCoordinator->phone_number ?? null;
        if ($korlapPhone) {
            $jenis = $locationRequest->request_type == 'add' ? 'Penambahan' : 'Pencabutan';
            $msg = "Yth. Bapak/Ibu Mitra Pengelola,\n\n";
            $msg .= "Kami informasikan bahwa pengajuan titik parkir Anda (Tipe: *$jenis*) pada lokasi *{$locationRequest->name}* telah selesai dilakukan survei lapangan oleh tim UPT Perparkiran.\n\n";
            $msg .= 'Rekomendasi Setoran: *Rp '.number_format($request->recommended_deposit, 0, ',', '.')."/hari*\n";
            $msg .= "Catatan Dinas: {$request->survey_notes}\n\n";
            $msg .= "Sistem sedang menunggu finalisasi (Persetujuan) dari Pimpinan. Silakan pantau status pengajuan pada aplikasi Anda.\n\n";
            $msg .= "Hormat kami,\n*UPT Perparkiran*";

            $this->sendWhatsAppNotification($korlapPhone, $msg);
        }

        return redirect()->back()->with('success', 'Hasil survey berhasil disimpan dan notifikasi WA telah dikirim ke Korlap.');
    }

    // 2. KEPALA/ADMIN MENYETUJUI & MENGEKSEKUSI DATA
    public function approve(Request $request, LocationRequest $locationRequest)
    {
        if ($locationRequest->request_type === 'add') {
            $request->validate([
                'road_section_id' => 'required|exists:road_sections,id',
                'estimated_area' => 'nullable|numeric|min:0',
                'estimated_srp_r2' => 'nullable|integer|min:0',
                'estimated_srp_r4' => 'nullable|integer|min:0',
            ]);
        }

        DB::beginTransaction();
        try {
            $agreement = $locationRequest->agreement;
            $review = $locationRequest->review;

            // Siapkan data untuk histori
            $adminId = Auth::id();
            $korlapName = $agreement->fieldCoordinator->user->name ?? 'N/A';
            $oldDeposit = $agreement->daily_deposit_amount;

            if (! $review && $locationRequest->request_type === 'add') {
                throw new \Exception('Pengajuan penambahan harus di-survey terlebih dahulu!');
            }

            // ==========================================
            // --- JIKA PENAMBAHAN TITIK (ADD) ---
            // ==========================================
            if ($locationRequest->request_type === 'add') {

                $newDeposit = $oldDeposit + $review->recommended_deposit;

                // 1. BUAT TITIK PARKIR BARU
                $parkingLocation = ParkingLocation::create([
                    'road_section_id' => $request->road_section_id,
                    'name' => $locationRequest->name,
                    'daily_deposit' => $review->recommended_deposit,
                    'latitude' => $locationRequest->latitude,
                    'longitude' => $locationRequest->longitude,
                    'image' => $locationRequest->image,
                    'proposal_document' => $locationRequest->proposal_document,
                    'official_report_document' => $review->report_document ?? null,
                    'status' => 'tidak_tersedia',
                    'estimated_area' => $request->estimated_area,
                    'estimated_srp_r2' => $request->estimated_srp_r2,
                    'estimated_srp_r4' => $request->estimated_srp_r4,
                ]);

                // 2. ATTACH KE PIVOT
                $agreement->parkingLocations()->attach($parkingLocation->id, [
                    'assigned_date' => now(),
                    'status' => 'active',
                ]);

                // 3. CATAT HISTORI TITIK PARKIR
                ParkingLocationHistory::create([
                    'parking_location_id' => $parkingLocation->id,
                    'user_id' => $adminId,
                    'action' => 'owner_changed',
                    'description' => "Lokasi diserahkan ke Koordinator: {$korlapName} (PKS: {$agreement->agreement_number}).",
                ]);

                // 4. UPDATE NOMINAL PKS
                $agreement->update(['daily_deposit_amount' => $newDeposit]);

                // 5. CATAT HISTORI PERJANJIAN: LOKASI DITAMBAHKAN
                AgreementHistory::create([
                    'agreement_id' => $agreement->id,
                    'event_type' => 'location_added',
                    'changed_by_user_id' => $adminId,
                    'notes' => 'Lokasi "'.$parkingLocation->name.'" ditambahkan.',
                ]);

                // 6. CATAT HISTORI PERJANJIAN: PERUBAHAN SETORAN
                AgreementHistory::create([
                    'agreement_id' => $agreement->id,
                    'event_type' => 'deposit_change',
                    'old_value' => json_encode(['daily_deposit_amount' => $oldDeposit]),
                    'new_value' => json_encode(['daily_deposit_amount' => $newDeposit]),
                    'changed_by_user_id' => $adminId,
                    'notes' => 'Setoran diubah dari Rp '.number_format($oldDeposit, 0, ',', '.').' menjadi Rp '.number_format($newDeposit, 0, ',', '.').'.',
                ]);

            }
            // ==========================================
            // --- JIKA PENCABUTAN TITIK (REMOVE) ---
            // ==========================================
            elseif ($locationRequest->request_type === 'remove') {
                $parkingLocation = $locationRequest->parkingLocation;
                $newDeposit = $oldDeposit - $parkingLocation->daily_deposit;

                // 1. UPDATE PIVOT JADI INACTIVE (✅ Fix Truncated Data)
                $agreement->parkingLocations()->updateExistingPivot($parkingLocation->id, [
                    'status' => 'inactive',
                    'removed_date' => now(),
                ]);

                // 2. CATAT HISTORI TITIK PARKIR
                ParkingLocationHistory::create([
                    'parking_location_id' => $parkingLocation->id,
                    'user_id' => $adminId,
                    'action' => 'owner_changed',
                    'description' => "Lokasi dikeluarkan dari (PKS: {$agreement->agreement_number}) milik Koordinator {$korlapName}.",
                ]);

                // 3. KURANGI NOMINAL PKS & UBAH STATUS TITIK PARKIR
                $agreement->update(['daily_deposit_amount' => $newDeposit]);
                $parkingLocation->update(['status' => 'tersedia']); // ✅ Kembalikan status titik jadi tersedia

                // 4. CATAT HISTORI PERJANJIAN: LOKASI DIKELUARKAN
                AgreementHistory::create([
                    'agreement_id' => $agreement->id,
                    'event_type' => 'location_removed',
                    'changed_by_user_id' => $adminId,
                    'notes' => 'Lokasi "'.$parkingLocation->name.'" dikeluarkan.',
                ]);

                // 5. CATAT HISTORI PERJANJIAN: PERUBAHAN SETORAN
                AgreementHistory::create([
                    'agreement_id' => $agreement->id,
                    'event_type' => 'deposit_change',
                    'old_value' => json_encode(['daily_deposit_amount' => $oldDeposit]),
                    'new_value' => json_encode(['daily_deposit_amount' => $newDeposit]),
                    'changed_by_user_id' => $adminId,
                    'notes' => 'Setoran diubah dari Rp '.number_format($oldDeposit, 0, ',', '.').' menjadi Rp '.number_format($newDeposit, 0, ',', '.').'.',
                ]);
            }

            // AKHIRI PROSES REQUEST
            $locationRequest->update([
                'status' => 'approved',
                'admin_note' => $request->admin_note ?? 'Disetujui dan dieksekusi oleh sistem.',
            ]);

            DB::commit();

            // KIRIM NOTIFIKASI WHATSAPP APPROVAL
            $korlapPhone = $agreement->fieldCoordinator->phone_number ?? null;
            if ($korlapPhone) {
                $jenis = $locationRequest->request_type == 'add' ? 'Penambahan' : 'Pencabutan';
                $namaTitik = $locationRequest->request_type == 'add' ? $locationRequest->name : ($locationRequest->parkingLocation->name ?? '-');

                $msg = "Yth. Bapak/Ibu Mitra Pengelola,\n\n";
                $msg .= "Pengajuan Anda untuk *$jenis* titik parkir pada lokasi *{$namaTitik}* telah *DISETUJUI*.\n\n";
                $msg .= "Kewajiban setoran harian Anda dan daftar titik pada PKS telah diperbarui secara otomatis di dalam sistem.\n\n";
                $msg .= 'Catatan Dinas: '.($request->admin_note ?: '-')."\n\n";
                $msg .= "Hormat kami,\n*UPT Perparkiran*";

                $this->sendWhatsAppNotification($korlapPhone, $msg);
            }

            return redirect()->back()->with('success', 'Pengajuan disetujui! PKS terupdate, histori tercatat, dan WA telah terkirim.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal mengeksekusi data: '.$e->getMessage());
        }
    }

    // 3. ADMIN MENOLAK PENGAJUAN
    public function reject(Request $request, LocationRequest $locationRequest)
    {
        $request->validate(['admin_note' => 'required|string']);

        $locationRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
        ]);

        // KIRIM NOTIFIKASI WHATSAPP REJECTION
        $korlapPhone = $locationRequest->agreement->fieldCoordinator->phone_number ?? null;
        if ($korlapPhone) {
            $jenis = $locationRequest->request_type == 'add' ? 'Penambahan' : 'Pencabutan';
            $namaTitik = $locationRequest->request_type == 'add' ? $locationRequest->name : ($locationRequest->parkingLocation->name ?? '-');

            $msg = "Yth. Bapak/Ibu Mitra Pengelola,\n\n";
            $msg .= "Mohon maaf, pengajuan Anda untuk *$jenis* titik parkir pada lokasi *{$namaTitik}* harus kami *TOLAK*.\n\n";
            $msg .= "Alasan Penolakan:\n_{$request->admin_note}_\n\n";
            $msg .= "Hormat kami,\n*UPT Perparkiran*";

            $this->sendWhatsAppNotification($korlapPhone, $msg);
        }

        return redirect()->back()->with('success', 'Pengajuan telah ditolak dan notifikasi WA telah terkirim.');
    }

    /**
     * PRIVATE METHOD: Mengirim Pesan WhatsApp via Fonnte
     */
    private function sendWhatsAppNotification($phoneNumber, $message)
    {
        try {
            $uptProfile = UptProfile::first();

            if (! $uptProfile || empty($uptProfile->api_token_fonnte) || empty($phoneNumber)) {
                return;
            }

            $phoneFormatted = preg_replace('/[^0-9]/', '', $phoneNumber);

            Http::withHeaders([
                'Authorization' => $uptProfile->api_token_fonnte,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phoneFormatted,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            Log::error('Fonnte WA Error: '.$e->getMessage());
        }
    }
}
