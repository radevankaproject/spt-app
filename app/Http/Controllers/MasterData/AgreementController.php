<?php
namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\AgreementHistory;
use App\Models\AgreementPdfHistory;
use App\Models\BludBankAccount;
use App\Models\FieldCoordinator;
use App\Models\Leader;
use App\Models\ParkingLocation;
use App\Models\RoadSection;
use App\Models\UptProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AgreementController extends Controller
{
    /**
     * Menampilkan daftar perjanjian.
     */

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = Agreement::with(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('agreement_number', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhereHas('leader.user', fn($subq) => $subq->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('fieldCoordinator.user', fn($subq) => $subq->where('name', 'like', '%' . $search . '%'));
            });
        }

        $agreements = $query->latest()->paginate(10);
        return view('staff.agreements.index', compact('agreements', 'search'));
    }

    /**
     * Menampilkan form untuk membuat perjanjian baru.
     */
    public function create()
    {
        $leaders = Leader::with('user')->get();
        // Ambil hanya korlap yang belum punya PKS aktif
        $fieldCoordinators = FieldCoordinator::with('user')
            ->whereDoesntHave('agreements', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        // TIDAK PERLU mengirim data lokasi atau ruas jalan dari sini lagi.
        return view('staff.agreements.create', compact('leaders', 'fieldCoordinators'));
    }

    /**
     * ✅ METHOD BARU untuk mengambil Ruas Jalan berdasarkan Zona.
     */
    public function getRoadSectionsByZone($zone)
    {
        $roadSections = RoadSection::where('zone', $zone)
        // Penting: Hanya ambil ruas jalan yang memiliki lokasi parkir tersedia
            ->whereHas('parkingLocations', fn($q) => $q->where('status', 'tersedia'))
            ->orderBy('name', 'asc')
            ->get(['id', 'name']); // Kirim hanya ID dan Nama untuk efisiensi

        return response()->json($roadSections);
    }

    public function getParkingLocationsByRoadSection(Request $request, $roadSectionId)
    {
        // Ambil lokasi parkir yang statusnya 'tersedia'
        // dan belum terikat perjanjian aktif
        $locations = ParkingLocation::where('road_section_id', $roadSectionId)
            ->where('status', 'tersedia')
            ->whereDoesntHave('agreements', function ($query) {
                $query->where('agreement_parking_locations.status', 'active');
            })
        // ✅ Pastikan 'daily_deposit' ada di sini
            ->get(['id', 'name', 'status', 'road_section_id', 'daily_deposit']);

        return response()->json($locations);
    }
    /**
     * Menyimpan perjanjian baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'agreement_number'       => 'required|string|max:255|unique:agreements,agreement_number',
            'leader_id'              => 'required|exists:leaders,id',
            'field_coordinator_id'   => 'required|exists:field_coordinators,id',
            'start_date'             => 'required|date',
            'end_date'               => 'required|date|after_or_equal:start_date',
            'daily_deposit_amount'   => 'required|numeric|min:1', // Minimal 1 agar tidak nol
            'status'                 => 'required|string|in:active,expired,terminated,pending_renewal',
            'signed_date'            => 'required|date',
            'parking_location_ids'   => 'required|array|min:1',
            'parking_location_ids.*' => 'exists:parking_locations,id',
        ],
            [
                'agreement_number.required'     => 'Nomor perjanjian wajib diisi.',
                'agreement_number.unique'       => 'Nomor perjanjian ini sudah terdaftar.',
                'leader_id.required'            => 'Pimpinan (Pihak Pertama) wajib dipilih.',
                'field_coordinator_id.required' => 'Koordinator lapangan wajib dipilih.',
                'start_date.required'           => 'Tanggal mulai berlaku wajib diisi.',
                'end_date.required'             => 'Tanggal selesai berlaku wajib diisi.',
                'end_date.after_or_equal'       => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
                'daily_deposit_amount.min'      => 'Total setoran harian harus lebih dari nol.',
                'signed_date.required'          => 'Tanggal TTD wajib diisi.',
                'parking_location_ids.required' => 'Minimal satu lokasi parkir harus dipilih.',
                'parking_location_ids.min'      => 'Minimal satu lokasi parkir harus dipilih.',
            ]);

        $dailyAmount    = (float) $validatedData['daily_deposit_amount'];
        $startDate      = Carbon::parse($validatedData['start_date']);
        $endDate        = Carbon::parse($validatedData['end_date']);
        $durationInDays = $endDate->diffInDays($startDate) + 1;

        $agreementData                           = $validatedData;
        $agreementData['monthly_deposit_target'] = $dailyAmount * 30;
        $agreementData['total_deposit_target']   = $dailyAmount * $durationInDays;
        $agreementData['verification_code']      = Str::uuid()->toString();

        DB::beginTransaction();
        try {
            $agreement = Agreement::create($agreementData);

            $parkingLocationsToAttach = [];
            foreach ($validatedData['parking_location_ids'] as $locationId) {
                $parkingLocationsToAttach[$locationId] = ['assigned_date' => now(), 'status' => 'active'];
                ParkingLocation::where('id', $locationId)->update(['status' => 'tidak_tersedia']);
            }
            $agreement->parkingLocations()->attach($parkingLocationsToAttach);

            // ✅ PERBAIKAN: Ambil data agreement yang paling fresh dari database untuk memastikan semua relasi termuat
            $freshAgreement = Agreement::with(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection'])->find($agreement->id);
            $this->generateAndStorePdfHistory($freshAgreement, 'Perjanjian awal dibuat');

            DB::commit();

            return redirect()->route('masterdata.agreements.index')
                ->with('success', 'Perjanjian "' . $agreement->agreement_number . '" berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AgreementController@store: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan perjanjian: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail satu perjanjian.
     */
    public function show(Agreement $agreement)
    {
        // Memuat semua relasi yang dibutuhkan
        $agreement->load(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection', 'depositTransactions', 'histories.changer']);

        // Menghitung total setoran HANYA untuk tahun berjalan
        $totalDepositThisYear = $agreement->depositTransactions
            ->where('is_validated', true) // Hanya hitung yang sudah tervalidasi
            ->where('deposit_date.year', now()->year)
            ->sum('amount');

        $locationsByRoadSection = $agreement->activeParkingLocations->groupBy('roadSection.name');

        // Kirim semua data yang dibutuhkan ke view
        return view('staff.agreements.show', compact('agreement', 'totalDepositThisYear', 'locationsByRoadSection'));
    }

    /**
     * Menampilkan form untuk mengedit perjanjian.
     */
    public function edit(Agreement $agreement)
    {
        // Memuat relasi yang dibutuhkan
        $agreement->load('leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection');
        $leaders = Leader::with('user')->get();

        // 1. Ambil semua ID lokasi parkir yang saat ini aktif untuk PKS ini
        $currentParkingLocationIds = $agreement->activeParkingLocations->pluck('id')->toArray();

        // 2. Tentukan zona dari PKS ini (berdasarkan lokasi pertama yang terikat)
        $firstLocation = $agreement->activeParkingLocations->first();
        // Jika tidak ada lokasi sama sekali, zona akan null
        $initialZone = $firstLocation ? $firstLocation->roadSection->zone : null;

        $parkingLocationsForCheckboxes = collect();
        if ($initialZone) {
            // 3. Ambil semua lokasi yang bisa dipilih di zona ini:
            //    - yang statusnya 'tersedia'
            //    - ATAU yang sudah terpilih di PKS ini (meskipun statusnya 'tidak_tersedia')
            $parkingLocationsForCheckboxes = ParkingLocation::with('roadSection')
                ->whereHas('roadSection', function ($q) use ($initialZone) {
                    $q->where('zone', $initialZone);
                })
                ->where(function ($q) use ($currentParkingLocationIds) {
                    $q->where('status', 'tersedia')
                        ->orWhereIn('id', $currentParkingLocationIds);
                })
                ->get();
        }

        // 4. Ambil semua ruas jalan untuk dropdown filter
        $allRoadSections = $initialZone ? RoadSection::where('zone', $initialZone)->orderBy('name')->get() : collect();

        return view('staff.agreements.edit', compact(
            'agreement',
            'leaders',
            'currentParkingLocationIds',
            'initialZone',
            'allRoadSections',
            'parkingLocationsForCheckboxes' // Kirim data ini ke view
        ));
    }

    /**
     * Update data perjanjian di database.
     */
    public function update(Request $request, Agreement $agreement)
    {
        $validatedData = $request->validate([
            'agreement_number'       => ['required', 'string', 'max:255', Rule::unique('agreements', 'agreement_number')->ignore($agreement->id)],
            'leader_id'              => 'required|exists:leaders,id',
            // field_coordinator_id sengaja tidak divalidasi karena tidak bisa diubah
            'start_date'             => 'required|date',
            'end_date'               => 'required|date|after_or_equal:start_date',
            'daily_deposit_amount'   => 'required|numeric|min:0',
            'status'                 => 'required|string|in:active,expired,terminated,pending_renewal',
            'signed_date'            => 'required|date',
            'parking_location_ids'   => 'nullable|array',
            'parking_location_ids.*' => 'exists:parking_locations,id',
        ], [
            'agreement_number.required'     => 'Nomor perjanjian wajib diisi.',
            'agreement_number.unique'       => 'Nomor perjanjian ini sudah terdaftar.',
            'leader_id.required'            => 'Pimpinan (Pihak Pertama) wajib dipilih.',
            // 'field_coordinator_id.required' => 'Koordinator lapangan wajib dipilih.',
            'start_date.required'           => 'Tanggal mulai berlaku wajib diisi.',
            'end_date.required'             => 'Tanggal selesai berlaku wajib diisi.',
            'end_date.after_or_equal'       => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'daily_deposit_amount.min'      => 'Total setoran harian harus lebih dari nol.',
            'signed_date.required'          => 'Tanggal TTD wajib diisi.',
            'parking_location_ids.required' => 'Minimal satu lokasi parkir harus dipilih.',
            'parking_location_ids.min'      => 'Minimal satu lokasi parkir harus dipilih.',
        ]);

        $dailyAmount    = (float) $validatedData['daily_deposit_amount'];
        $startDate      = Carbon::parse($validatedData['start_date']);
        $endDate        = Carbon::parse($validatedData['end_date']);
        $durationInDays = $endDate->diffInDays($startDate) + 1;

        $agreementData                           = $validatedData;
        $agreementData['monthly_deposit_target'] = $dailyAmount * 30;
        $agreementData['total_deposit_target']   = $dailyAmount * $durationInDays;

        DB::beginTransaction();
        try {
            $oldData = $agreement->fresh()->load('leader.user', 'activeParkingLocations');
            $agreement->update($agreementData);
            $agreement->load('leader.user'); // Muat ulang relasi setelah update

            $newLocationIds           = $validatedData['parking_location_ids'] ?? [];
            $currentActiveLocationIds = $oldData->activeParkingLocations->pluck('id')->toArray();
            $allRelatedLocations      = $oldData->parkingLocations()->get()->keyBy('id');

            $historyRecords = [];
            $now            = now();

            // --- MULAI LOGIKA PERBANDINGAN DAN PENCATATAN RIWAYAT (SUDAH DIRAPIKAN) ---

            // 1. Cek perubahan data dasar (Pimpinan, Tanggal, Setoran, Status)
            if ($oldData->leader_id != $agreement->leader_id) {
                $historyRecords[] = ['notes' => 'Pimpinan diubah dari "' . ($oldData->leader->user->name ?? 'N/A') . '" menjadi "' . ($agreement->leader->user->name ?? 'N/A') . '".'];
            }
            if (! $oldData->start_date->isSameDay($agreement->start_date)) {
                $historyRecords[] = ['notes' => 'Tanggal mulai diubah dari "' . $oldData->start_date->translatedFormat('d M Y') . '" menjadi "' . $agreement->start_date->translatedFormat('d M Y') . '".'];
            }
            if (! $oldData->end_date->isSameDay($agreement->end_date)) {
                $historyRecords[] = ['notes' => 'Tanggal selesai diubah dari "' . $oldData->end_date->translatedFormat('d M Y') . '" menjadi "' . $agreement->end_date->translatedFormat('d M Y') . '".'];
            }
            if ($oldData->daily_deposit_amount != $agreement->daily_deposit_amount) {
                $historyRecords[] = ['event_type' => 'deposit_changed', 'notes' => 'Setoran diubah dari Rp ' . number_format($oldData->daily_deposit_amount) . ' menjadi Rp ' . number_format($agreement->daily_deposit_amount) . '.'];
            }
            if ($oldData->status != $agreement->status) {
                $historyRecords[] = ['event_type' => 'status_changed', 'notes' => 'Status diubah dari "' . ucfirst($oldData->status) . '" menjadi "' . ucfirst($agreement->status) . '".'];
            }

            // --- BLOK LOGIKA TUNGGAL UNTUK PERUBAHAN LOKASI ---

            // Lokasi yang dinonaktifkan (dulu aktif, sekarang tidak ada di form)
            $locationsToDeactivate = array_diff($currentActiveLocationIds, $newLocationIds);
            if (! empty($locationsToDeactivate)) {
                foreach ($locationsToDeactivate as $locationId) {
                    $agreement->parkingLocations()->updateExistingPivot($locationId, ['status' => 'inactive', 'removed_date' => now()]);
                    $locationName     = $allRelatedLocations[$locationId]->name ?? 'N/A';
                    $historyRecords[] = ['event_type' => 'location_removed', 'notes' => 'Lokasi "' . $locationName . '" dikeluarkan.'];
                }
                ParkingLocation::whereIn('id', $locationsToDeactivate)->update(['status' => 'tersedia']);
            }

            // Lokasi yang ditambahkan atau diaktifkan kembali
            $attachData = [];
            foreach ($newLocationIds as $locationId) {
                // Jika lokasi sudah pernah terikat sebelumnya
                if (isset($allRelatedLocations[$locationId])) {
                    // Jika statusnya tidak aktif, aktifkan kembali
                    if ($allRelatedLocations[$locationId]->pivot->status === 'inactive') {
                        $agreement->parkingLocations()->updateExistingPivot($locationId, ['status' => 'active', 'assigned_date' => now(), 'removed_date' => null]);
                        $historyRecords[] = ['event_type' => 'location_added', 'notes' => 'Lokasi "' . $allRelatedLocations[$locationId]->name . '" diaktifkan kembali.'];
                    }
                } else { // Jika ini lokasi yang benar-benar baru untuk PKS ini
                    $attachData[$locationId] = ['status' => 'active', 'assigned_date' => now()];
                }
            }

            // Jalankan attach HANYA untuk lokasi yang benar-benar baru
            if (! empty($attachData)) {
                $agreement->parkingLocations()->attach($attachData);
                $addedLocationsDetails = ParkingLocation::whereIn('id', array_keys($attachData))->pluck('name');
                foreach ($addedLocationsDetails as $name) {
                    $historyRecords[] = ['event_type' => 'location_added', 'notes' => 'Lokasi "' . $name . '" ditambahkan.'];
                }
            }

            // Set semua lokasi yang terpilih menjadi 'tidak_tersedia'
            if (! empty($newLocationIds)) {
                ParkingLocation::whereIn('id', $newLocationIds)->update(['status' => 'tidak_tersedia']);
            }

            // --- SELESAI LOGIKA PERUBAHAN LOKASI ---

            // Simpan semua catatan riwayat
            if (! empty($historyRecords)) {
                foreach ($historyRecords as &$record) {
                    $record['agreement_id']       = $agreement->id;
                    $record['changed_by_user_id'] = Auth::id();
                    $record['created_at']         = $now;
                    $record['updated_at']         = $now;
                    if (! isset($record['event_type'])) {
                        $record['event_type'] = 'details_updated';
                    }
                }
                AgreementHistory::insert($historyRecords);

                // Generate PDF versi terbaru
                $pdfNotes       = implode('; ', Arr::pluck($historyRecords, 'notes'));
                $freshAgreement = Agreement::with(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection'])->find($agreement->id);
                $this->generateAndStorePdfHistory($freshAgreement, $pdfNotes);
            }

            DB::commit();

            return redirect()->route('masterdata.agreements.index')
                ->with('success', 'Perjanjian berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AgreementController@update: Error updating agreement: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui perjanjian: ' . $e->getMessage());
        }
    }

    public function destroy(Agreement $agreement)
    {
        DB::beginTransaction();
        try {
            // ✅ Mengambil HANYA lokasi yang statusnya sedang 'active' di perjanjian ini
            $activeLocationIds = $agreement->activeParkingLocations()->pluck('parking_locations.id')->toArray();

            // Logika selanjutnya tidak perlu diubah
            if (! empty($activeLocationIds)) {
                ParkingLocation::whereIn('id', $activeLocationIds)->update(['status' => 'tersedia']);
            }
            $agreement->delete();
            DB::commit();

            return redirect()->route('masterdata.agreements.index')->with('success', 'Perjanjian berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AgreementController@destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus perjanjian.');
        }
    }

    public function detachParkingLocation(Agreement $agreement, ParkingLocation $parkingLocation)
    {
        DB::beginTransaction();
        try {
            // Update status pivot menjadi 'inactive' untuk histori
            $agreement->parkingLocations()->updateExistingPivot($parkingLocation->id, [
                'status'       => 'inactive',
                'removed_date' => now(),
            ]);

            // Update status lokasi parkir menjadi 'tersedia'
            $parkingLocation->update(['status' => 'tersedia']);

            DB::commit();

            return redirect()->back()->with('success', 'Lokasi parkir "' . $parkingLocation->name . '" berhasil dikeluarkan dari perjanjian.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error detaching parking location: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Gagal mengeluarkan lokasi parkir: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF untuk perjanjian.
     */
    public function generatePdf(Agreement $agreement)
    {
        // Ambil rekening bank yang sedang aktif
        $activeBankAccount = BludBankAccount::where('is_active', true)->first();
        //
        $uptProfile = UptProfile::first();

        $agreement->load(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection']);

        $pdf = Pdf::loadView('pdf.agreement', compact('agreement', 'activeBankAccount', 'uptProfile'));
        return $pdf->stream(str_replace('/', '_', $agreement->agreement_number) . '_' . date('dmY', strtotime($agreement->start_date)) . '-' . date('dmy', strtotime($agreement->end_date)) . '.pdf');
    }

    private function generateAndStorePdfHistory(Agreement $agreement, string $notes)
    {
        try {
            $activeBankAccount = BludBankAccount::where('is_active', true)->first();
            if (! $activeBankAccount) {
                Log::warning("No active BLUD bank account found for generating PDF for agreement ID: {$agreement->id}");
            }
            $uptProfile = UptProfile::first();

            $pdf        = Pdf::loadView('pdf.agreement', compact('agreement', 'activeBankAccount', 'uptProfile'));
            $pdfContent = $pdf->output();

            $fileName = 'PKS_' . str_replace('/', '_', $agreement->agreement_number) . '_' . time() . '.pdf';
            $filePath = 'agreements_history/' . $fileName;

            Storage::disk('public')->put($filePath, $pdfContent);

            AgreementPdfHistory::create([
                'agreement_id'         => $agreement->id,
                'file_path'            => $filePath,
                'notes'                => $notes,
                'generated_by_user_id' => Auth::id(),
            ]);

            Log::info("Successfully generated PDF history for agreement ID: {$agreement->id}");
        } catch (\Exception $e) {
            // Re-throw exception agar proses utama tahu ada yang gagal
            Log::error("Failed to generate PDF history for agreement ID {$agreement->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function showPdfHistory(Agreement $agreement)
    {
        // ✅ PERBAIKAN: Relasi ke generator adalah 'generator', bukan 'generator.user'
        $histories = $agreement->pdfHistories()->with('generator')->paginate(10);
        return view('staff.agreements.pdf_history', compact('agreement', 'histories'));
    }
}
