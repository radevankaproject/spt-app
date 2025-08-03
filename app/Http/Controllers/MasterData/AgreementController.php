<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use App\Models\Agreement;
use App\Models\Leader;
use App\Models\FieldCoordinator;
use App\Models\ParkingLocation;
use App\Models\RoadSection;
use App\Models\AgreementHistory;
use App\Models\BludBankAccount;
use App\Models\UptProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AgreementController extends Controller
{
    /**
     * Menampilkan daftar perjanjian.
     */

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Agreement::with(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations']);

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
            'agreement_number' => 'required|string|max:255|unique:agreements,agreement_number',
            'leader_id' => 'required|exists:leaders,id',
            'field_coordinator_id' => 'required|exists:field_coordinators,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'daily_deposit_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,expired,terminated,pending_renewal',
            'signed_date' => 'required|date',
            'parking_location_ids' => 'required|array|min:1', // Validasi minimal 1 lokasi
            'parking_location_ids.*' => 'exists:parking_locations,id',
        ]);
        // ✅ Logika Kalkulasi
        $dailyAmount = (float) $validatedData['daily_deposit_amount'];
        $startDate = Carbon::parse($validatedData['start_date']);
        $endDate = Carbon::parse($validatedData['end_date']);
        $durationInDays = $endDate->diffInDays($startDate) + 1;

        $agreementData = $validatedData;
        $agreementData['monthly_deposit_target'] = $dailyAmount * 30; // Asumsi 30 hari/bulan
        $agreementData['total_deposit_target'] = $dailyAmount * $durationInDays;
        $agreementData['verification_code'] = Str::uuid()->toString();

        DB::beginTransaction();
        try {
            $agreement = Agreement::create($agreementData);

            $parkingLocationsToAttach = [];
            foreach ($validatedData['parking_location_ids'] as $locationId) {
                // Saat membuat, semua lokasi yang dilampirkan otomatis 'active'
                $parkingLocationsToAttach[$locationId] = ['assigned_date' => now(), 'status' => 'active'];
                ParkingLocation::where('id', $locationId)->update(['status' => 'tidak_tersedia']);
            }
            $agreement->parkingLocations()->attach($parkingLocationsToAttach);

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

        // Kirim semua data yang dibutuhkan ke view
        return view('staff.agreements.show', compact('agreement', 'totalDepositThisYear'));
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
            'agreement_number' => ['required', 'string', 'max:255', Rule::unique('agreements', 'agreement_number')->ignore($agreement->id)],
            'leader_id' => 'required|exists:leaders,id',
            'field_coordinator_id' => 'required|exists:field_coordinators,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'daily_deposit_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,expired,terminated,pending_renewal',
            'signed_date' => 'required|date',
            'parking_location_ids' => 'nullable|array',
            'parking_location_ids.*' => 'exists:parking_locations,id',
        ]);

        // ✅ Logika Kalkulasi
        $dailyAmount = (float) $validatedData['daily_deposit_amount'];
        $startDate = Carbon::parse($validatedData['start_date']);
        $endDate = Carbon::parse($validatedData['end_date']);
        $durationInDays = $endDate->diffInDays($startDate) + 1;

        $agreementData = $validatedData;
        $agreementData['monthly_deposit_target'] = $dailyAmount * 30;
        $agreementData['total_deposit_target'] = $dailyAmount * $durationInDays;

        //code verification
        // $agreementData['verification_code'] = Str::uuid()->toString();

        DB::beginTransaction();
        try {
            $oldData = $agreement->fresh();
            $agreement->update($agreementData);

            $newLocationIds = $validatedData['parking_location_ids'] ?? [];
            $allRelatedLocations = $oldData->parkingLocations()->get()->keyBy('id');
            $currentActiveLocationIds = $oldData->activeParkingLocations()->pluck('parking_locations.id')->toArray();

            // Siapkan array untuk menampung semua catatan riwayat
            $historyRecords = [];

            // A. Catat perubahan data utama
            if ($oldData->daily_deposit_amount != $agreement->daily_deposit_amount) {
                $historyRecords[] = ['agreement_id' => $agreement->id, 'event_type' => 'deposit_changed', 'changed_by_user_id' => Auth::id(), 'notes' => 'Setoran diubah dari Rp ' . number_format($oldData->daily_deposit_amount) . ' ke Rp ' . number_format($agreement->daily_deposit_amount) . '.', 'created_at' => now(), 'updated_at' => now()];
            }
            if ($oldData->status != $agreement->status) {
                $historyRecords[] = ['agreement_id' => $agreement->id, 'event_type' => 'status_changed', 'changed_by_user_id' => Auth::id(), 'notes' => 'Status diubah dari "' . ucfirst($oldData->status) . '" ke "' . ucfirst($agreement->status) . '".', 'created_at' => now(), 'updated_at' => now()];
            }

            // B. Proses lokasi yang dinonaktifkan
            $locationsToDeactivate = array_diff($currentActiveLocationIds, $newLocationIds);
            if (!empty($locationsToDeactivate)) {
                $deactivatedLocationsDetails = ParkingLocation::whereIn('id', $locationsToDeactivate)->get();
                foreach ($deactivatedLocationsDetails as $location) {
                    $agreement->parkingLocations()->updateExistingPivot($location->id, ['status' => 'inactive', 'removed_date' => now()]);
                    // Kumpulkan data riwayat, jangan langsung create
                    $historyRecords[] = ['agreement_id' => $agreement->id, 'event_type' => 'location_removed', 'changed_by_user_id' => Auth::id(), 'notes' => 'Lokasi "' . $location->name . '" dikeluarkan.', 'created_at' => now(), 'updated_at' => now()];
                }
                ParkingLocation::whereIn('id', $locationsToDeactivate)->update(['status' => 'tersedia']);
            }

            // C. Proses lokasi yang diaktifkan kembali atau baru ditambahkan
            $attachData = [];
            foreach ($newLocationIds as $locationId) {
                if (isset($allRelatedLocations[$locationId])) {
                    if ($allRelatedLocations[$locationId]->pivot->status === 'inactive') {
                        $agreement->parkingLocations()->updateExistingPivot($locationId, ['status' => 'active', 'assigned_date' => now(), 'removed_date' => null]);
                        $historyRecords[] = ['agreement_id' => $agreement->id, 'event_type' => 'location_added', 'changed_by_user_id' => Auth::id(), 'notes' => 'Lokasi "' . $allRelatedLocations[$locationId]->name . '" diaktifkan kembali.', 'created_at' => now(), 'updated_at' => now()];
                    }
                } else {
                    $attachData[$locationId] = ['status' => 'active', 'assigned_date' => now()];
                }
            }

            if (!empty($attachData)) {
                $agreement->parkingLocations()->attach($attachData);
                $addedLocationsDetails = ParkingLocation::whereIn('id', array_keys($attachData))->pluck('name');
                foreach ($addedLocationsDetails as $name) {
                    $historyRecords[] = ['agreement_id' => $agreement->id, 'event_type' => 'location_added', 'changed_by_user_id' => Auth::id(), 'notes' => 'Lokasi "' . $name . '" ditambahkan.', 'created_at' => now(), 'updated_at' => now()];
                }
            }

            // Simpan semua catatan riwayat dalam satu kali perintah query
            if (!empty($historyRecords)) {
                AgreementHistory::insert($historyRecords);
            }

            // Update status lokasi yang aktif
            if (!empty($newLocationIds)) {
                ParkingLocation::whereIn('id', $newLocationIds)->update(['status' => 'tidak_tersedia']);
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

    /**
     * Menghapus perjanjian (soft delete).
     */
    public function destroy(Agreement $agreement)
    {
        DB::beginTransaction();
        try {
            $activeLocationIds = $agreement->parkingLocations()->pluck('parking_locations.id')->toArray();
            if (!empty($activeLocationIds)) {
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
                'status' => 'inactive',
                'removed_date' => now()
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
}
