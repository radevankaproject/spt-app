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
use App\Models\ParkingLocationHistory; // ✅ JANGAN LUPA IMPORT INI
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
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AgreementController extends Controller
{
    /**
     * Menampilkan daftar perjanjian.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'all'); // Default 'all'

        // ✅ 1. TANGKAP INPUT TAHUN DARI URL (Default: kosong/semua)
        $year = $request->input('year');

        // ✅ 2. AMBIL DAFTAR TAHUN UNTUK DROPDOWN FILTER
        $availableYears = Agreement::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $query = Agreement::with(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations']);

        // LOGIKA FILTER TAB
        if ($tab === 'active') {
            $query->whereIn('status', ['active', 'pending_renewal']);
        } elseif ($tab === 'inactive') {
            $query->whereIn('status', ['expired', 'terminated']);
        }

        // ✅ 3. LOGIKA FILTER TAHUN
        if ($year) {
            $query->whereYear('start_date', $year);
        }

        // LOGIKA SEARCH
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('agreement_number', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereHas('leader.user', fn ($subq) => $subq->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('fieldCoordinator.user', fn ($subq) => $subq->where('name', 'like', '%'.$search.'%'));
            });
        }

        // PAGINASI
        $agreements = $query->latest()->paginate(10);

        // MENGHITUNG TOTAL UNTUK BADGE DI TAB (Disesuaikan dengan Tahun jika difilter)
        $baseCountQuery = Agreement::query();
        if ($year) {
            $baseCountQuery->whereYear('start_date', $year);
        } // Badge menyesuaikan tahun

        $countAll = (clone $baseCountQuery)->count();
        $countActive = (clone $baseCountQuery)->whereIn('status', ['active', 'pending_renewal'])->count();
        $countInactive = (clone $baseCountQuery)->whereIn('status', ['expired', 'terminated'])->count();

        return view('staff.agreements.index', compact(
            'agreements', 'search', 'tab', 'year', 'availableYears', 'countAll', 'countActive', 'countInactive'
        ));
    }

    /**
     * Menampilkan form untuk membuat perjanjian baru.
     */
    public function create()
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        // ✅ 1. HANYA AMBIL PIMPINAN YANG AKTIF
        $leaders = Leader::with('user')
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            })->get();

        // ✅ 2. HANYA AMBIL KORLAP YANG AKTIF & TIDAK SEDANG MEMILIKI PKS AKTIF
        $fieldCoordinators = FieldCoordinator::with('user')
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            })
            ->whereDoesntHave('agreements', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        // ✅ 3. AMBIL DATA LOKASI YANG DIPILIH SEBELUMNYA (JIKA ADA VALIDATION ERROR)
        $oldLocationIds = old('parking_location_ids', []);
        $oldLocations = new \stdClass();
        if (!empty($oldLocationIds)) {
            $locations = ParkingLocation::with('roadSection')->whereIn('id', $oldLocationIds)->get();
            $oldLocations = $locations->mapWithKeys(function ($loc) {
                return [$loc->id => [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'daily_deposit' => $loc->daily_deposit,
                    'road_section_name' => $loc->roadSection->name ?? 'N/A'
                ]];
            });
        }

        return view('staff.agreements.create', compact('leaders', 'fieldCoordinators', 'oldLocations'));
    }

    public function getRoadSectionsByZone($zone)
    {
        $roadSections = RoadSection::where('zone', $zone)
            ->whereHas('parkingLocations', fn ($q) => $q->where('status', 'tersedia'))
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json($roadSections);
    }

    public function getParkingLocationsByRoadSection(Request $request, $roadSectionId)
    {
        $locations = ParkingLocation::where('road_section_id', $roadSectionId)
            ->where('status', 'tersedia')
            ->whereDoesntHave('agreements', function ($query) {
                $query->where('agreement_parking_locations.status', 'active');
            })
            ->get(['id', 'name', 'status', 'road_section_id', 'daily_deposit']);

        return response()->json($locations);
    }

    /**
     * Menyimpan perjanjian baru ke database.
     */
    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $messages = [
            'agreement_number.required' => 'Nomor PKS wajib diisi.',
            'agreement_number.unique' => 'Nomor PKS ini sudah terdaftar.',
            'leader_id.required' => 'Pimpinan wajib dipilih.',
            'field_coordinator_id.required' => 'Koordinator Lapangan wajib dipilih.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
            'daily_deposit_amount.required' => 'Setoran harian wajib diisi.',
            'daily_deposit_amount.min' => 'Setoran harian tidak boleh kurang dari 1.',
            'jenis.required' => 'Jenis PKS wajib dipilih.',
            'jenis.in' => 'Pilihan jenis PKS tidak valid.',
            'status.required' => 'Status PKS wajib dipilih.',
            'status.in' => 'Pilihan status PKS tidak valid.',
            'signed_date.required' => 'Tanggal tanda tangan wajib diisi.',
            'parking_location_ids.required' => 'Minimal satu titik lokasi parkir harus dipilih.',
            'parking_location_ids.min' => 'Minimal satu titik lokasi parkir harus dipilih.',
        ];

        $validatedData = $request->validate([
            'agreement_number' => 'required|string|max:255|unique:agreements,agreement_number',
            'leader_id' => 'required|exists:leaders,id',
            'field_coordinator_id' => 'required|exists:field_coordinators,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'daily_deposit_amount' => 'required|numeric|min:1',
            'jenis' => 'required|in:draft,sementara,rilis',
            'status' => 'required|in:active,pending',
            'signed_date' => 'required|date',
            'parking_location_ids' => 'required|array|min:1',
            'parking_location_ids.*' => 'exists:parking_locations,id',
        ], $messages);

        $dailyAmount = (float) $validatedData['daily_deposit_amount'];
        $startDate = Carbon::parse($validatedData['start_date']);
        $endDate = Carbon::parse($validatedData['end_date']);
        $durationInDays = $endDate->diffInDays($startDate) + 1;

        $agreementData = $validatedData;
        $agreementData['monthly_deposit_target'] = $dailyAmount * 30;
        $agreementData['total_deposit_target'] = $dailyAmount * $durationInDays;
        $agreementData['verification_code'] = Str::uuid()->toString();

        DB::beginTransaction();
        try {
            $agreement = Agreement::create($agreementData);

            $parkingLocationsToAttach = [];
            foreach ($validatedData['parking_location_ids'] as $locationId) {
                $parkingLocationsToAttach[$locationId] = ['assigned_date' => now(), 'status' => 'active'];
                ParkingLocation::where('id', $locationId)->update(['status' => 'tidak_tersedia']);
            }
            $agreement->parkingLocations()->attach($parkingLocationsToAttach);

            $freshAgreement = Agreement::with(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection'])->find($agreement->id);

            // ✅ 1. CATAT HISTORI KEPEMILIKAN KE LOKASI PARKIR (CREATE)
            $korlapName = $freshAgreement->fieldCoordinator->user->name ?? 'N/A';
            foreach ($validatedData['parking_location_ids'] as $locationId) {
                ParkingLocationHistory::create([
                    'parking_location_id' => $locationId,
                    'user_id' => Auth::id(),
                    'action' => 'owner_changed',
                    'description' => "Lokasi diserahkan ke Koordinator: {$korlapName} (PKS: {$freshAgreement->agreement_number}). Menunggu setoran pertama.",
                ]);
            }

            $this->generateAndStorePdfHistory($freshAgreement, 'Perjanjian awal dibuat (Status: Pending Setoran)');

            DB::commit();

            return redirect()->route('masterdata.agreements.index')
                ->with('success', 'Perjanjian "'.$agreement->agreement_number.'" berhasil ditambahkan! Status saat ini PENDING hingga setoran pertama divalidasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AgreementController@store: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan perjanjian: '.$e->getMessage());
        }
    }

    public function show(Agreement $agreement)
    {
        $agreement->load(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection', 'depositTransactions', 'histories.changer']);

        // 1. Total Setoran Tahun Ini
        $totalDepositThisYear = $agreement->depositTransactions
            ->where('is_validated', true)
            ->where('deposit_date.year', now()->year)
            ->sum('amount');

        // 2. Data untuk Grafik (Di-group per bulan)
        $monthlyDeposits = $agreement->depositTransactions()
            ->where('is_validated', true)
            ->whereYear('deposit_date', now()->year)
            ->selectRaw('MONTH(deposit_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartLabels = [];
        $chartData = [];
        $bulanIndo = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        // Loop hanya bulan yang ada datanya
        foreach ($monthlyDeposits as $data) {
            $chartLabels[] = $bulanIndo[$data->month];
            $chartData[] = (int) $data->total;
        }

        // 3. Lokasi Parkir yang aktif
        $locationsByRoadSection = $agreement->activeParkingLocations->groupBy('roadSection.name');

        return view('staff.agreements.show', compact(
            'agreement', 'totalDepositThisYear', 'locationsByRoadSection', 'chartLabels', 'chartData'
        ));
    }

    public function edit(Agreement $agreement)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $agreement->load('leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection');

        // ✅ 1. HANYA AMBIL PIMPINAN YANG AKTIF UNTUK PILIHAN DROPDOWN
        $leaders = Leader::with('user')
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            })->get();

        // ✅ 2. SISTEM ANTI-BUG: Jika pimpinan lama (yang teken PKS ini) sudah nonaktif,
        // tetap sisipkan dia ke dalam list agar dropdown tidak blank/error!
        if (! $leaders->contains($agreement->leader_id)) {
            $leaders->push($agreement->leader);
        }

        $currentParkingLocationIds = $agreement->activeParkingLocations->pluck('id')->toArray();
        $firstLocation = $agreement->activeParkingLocations->first();
        $initialZone = $firstLocation ? $firstLocation->roadSection->zone : null;

        $parkingLocationsForCheckboxes = ParkingLocation::with('roadSection')
            ->where(function ($q) use ($initialZone, $currentParkingLocationIds) {
                if ($initialZone) {
                    // Ambil lokasi tersedia di initialZone
                    $q->where(function ($subQ) use ($initialZone) {
                        $subQ->whereHas('roadSection', function ($roadQ) use ($initialZone) {
                            $roadQ->where('zone', $initialZone);
                        })->where('status', 'tersedia');
                    });
                }
                
                // ATAU lokasi yang saat ini sudah masuk ke PKS (zona manapun)
                if (!empty($currentParkingLocationIds)) {
                    $q->orWhereIn('id', $currentParkingLocationIds);
                }
            })
            ->get();

        $allRoadSections = $initialZone ? RoadSection::where('zone', $initialZone)->orderBy('name')->get() : collect();

        return view('staff.agreements.edit', compact(
            'agreement',
            'leaders',
            'currentParkingLocationIds',
            'initialZone',
            'allRoadSections',
            'parkingLocationsForCheckboxes'
        ));
    }

    public function update(Request $request, Agreement $agreement)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $messages = [
            'agreement_number.required' => 'Nomor PKS wajib diisi.',
            'agreement_number.unique' => 'Nomor PKS ini sudah terdaftar.',
            'leader_id.required' => 'Pimpinan wajib dipilih.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
            'daily_deposit_amount.required' => 'Setoran harian wajib diisi.',
            'daily_deposit_amount.min' => 'Setoran harian tidak boleh kurang dari 0.',
            'jenis.required' => 'Jenis PKS wajib dipilih.',
            'jenis.in' => 'Pilihan jenis PKS tidak valid.',
            'status.required' => 'Status PKS wajib dipilih.',
            'status.in' => 'Pilihan status PKS tidak valid.',
            'signed_date.required' => 'Tanggal tanda tangan wajib diisi.',
        ];

        $validatedData = $request->validate([
            'agreement_number' => ['required', 'string', 'max:255', Rule::unique('agreements', 'agreement_number')->ignore($agreement->id)],
            'leader_id' => 'required|exists:leaders,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'daily_deposit_amount' => 'required|numeric|min:0',
            'jenis' => 'required|in:draft,sementara,rilis',
            'status' => 'required|string|in:active,pending,expired,terminated,pending_renewal',
            'signed_date' => 'required|date',
            'parking_location_ids' => 'nullable|array',
            'parking_location_ids.*' => 'exists:parking_locations,id',
        ], $messages);

        $dailyAmount = (float) $validatedData['daily_deposit_amount'];
        $startDate = Carbon::parse($validatedData['start_date']);
        $endDate = Carbon::parse($validatedData['end_date']);
        $durationInDays = $endDate->diffInDays($startDate) + 1;

        $agreementData = $validatedData;
        $agreementData['monthly_deposit_target'] = $dailyAmount * 30;
        $agreementData['total_deposit_target'] = $dailyAmount * $durationInDays;

        DB::beginTransaction();
        try {
            $oldData = $agreement->fresh()->load('leader.user', 'activeParkingLocations', 'fieldCoordinator.user');
            $agreement->update($agreementData);
            $agreement->load('leader.user');

            $newLocationIds = $validatedData['parking_location_ids'] ?? [];
            $currentActiveLocationIds = $oldData->activeParkingLocations->pluck('id')->toArray();
            $allRelatedLocations = $oldData->parkingLocations()->get()->keyBy('id');
            $korlapName = $oldData->fieldCoordinator->user->name ?? 'N/A'; // Ambil nama korlap
            $userId = Auth::id();

            $historyRecords = [];
            $now = now();

            if ($oldData->leader_id != $agreement->leader_id) {
                $historyRecords[] = ['notes' => 'Pimpinan diubah dari "'.($oldData->leader->user->name ?? 'N/A').'" menjadi "'.($agreement->leader->user->name ?? 'N/A').'".'];
            }
            if (! $oldData->start_date->isSameDay($agreement->start_date)) {
                $historyRecords[] = ['notes' => 'Tanggal mulai diubah dari "'.$oldData->start_date->translatedFormat('d M Y').'" menjadi "'.$agreement->start_date->translatedFormat('d M Y').'".'];
            }
            if (! $oldData->end_date->isSameDay($agreement->end_date)) {
                $historyRecords[] = ['notes' => 'Tanggal selesai diubah dari "'.$oldData->end_date->translatedFormat('d M Y').'" menjadi "'.$agreement->end_date->translatedFormat('d M Y').'".'];
            }
            if ($oldData->daily_deposit_amount != $agreement->daily_deposit_amount) {
                $historyRecords[] = ['event_type' => 'deposit_changed', 'notes' => 'Setoran diubah dari Rp '.number_format($oldData->daily_deposit_amount).' menjadi Rp '.number_format($agreement->daily_deposit_amount).'.'];
            }
            if ($oldData->jenis != $agreement->jenis) {
                $historyRecords[] = ['event_type' => 'details_updated', 'notes' => 'Jenis PKS diubah dari "'.ucfirst($oldData->jenis).'" menjadi "'.ucfirst($agreement->jenis).'".'];
            }
            if ($oldData->status != $agreement->status) {
                $historyRecords[] = ['event_type' => 'status_changed', 'notes' => 'Status diubah dari "'.ucfirst($oldData->status).'" menjadi "'.ucfirst($agreement->status).'".'];
            }

            // Lokasi yang dinonaktifkan
            $locationsToDeactivate = array_diff($currentActiveLocationIds, $newLocationIds);
            if (! empty($locationsToDeactivate)) {
                foreach ($locationsToDeactivate as $locationId) {
                    $agreement->parkingLocations()->updateExistingPivot($locationId, ['status' => 'inactive', 'removed_date' => now()]);
                    $locationName = $allRelatedLocations[$locationId]->name ?? 'N/A';
                    $historyRecords[] = ['event_type' => 'location_removed', 'notes' => 'Lokasi "'.$locationName.'" dikeluarkan.'];

                    // ✅ 2A. CATAT HISTORI KEPEMILIKAN: DIKELUARKAN
                    ParkingLocationHistory::create([
                        'parking_location_id' => $locationId,
                        'user_id' => $userId,
                        'action' => 'owner_changed',
                        'description' => "Lokasi dikeluarkan dari (PKS: {$agreement->agreement_number}) milik Koordinator {$korlapName}.",
                    ]);
                }
                ParkingLocation::whereIn('id', $locationsToDeactivate)->update(['status' => 'tersedia']);
            }

            // Lokasi yang ditambahkan atau diaktifkan kembali
            $attachData = [];
            foreach ($newLocationIds as $locationId) {
                if (isset($allRelatedLocations[$locationId])) {
                    if ($allRelatedLocations[$locationId]->pivot->status === 'inactive') {
                        $agreement->parkingLocations()->updateExistingPivot($locationId, ['status' => 'active', 'assigned_date' => now(), 'removed_date' => null]);
                        $historyRecords[] = ['event_type' => 'location_added', 'notes' => 'Lokasi "'.$allRelatedLocations[$locationId]->name.'" diaktifkan kembali.'];

                        // ✅ 2B. CATAT HISTORI KEPEMILIKAN: DIAKTIFKAN KEMBALI
                        ParkingLocationHistory::create([
                            'parking_location_id' => $locationId,
                            'user_id' => $userId,
                            'action' => 'owner_changed',
                            'description' => "Lokasi dimasukkan kedalam PKS milik Koordinator: {$korlapName} (PKS: {$agreement->agreement_number}).",
                        ]);
                    }
                } else {
                    $attachData[$locationId] = ['status' => 'active', 'assigned_date' => now()];
                }
            }

            // Jalankan attach HANYA untuk lokasi yang benar-benar baru
            if (! empty($attachData)) {
                $agreement->parkingLocations()->attach($attachData);
                $addedLocationsDetails = ParkingLocation::whereIn('id', array_keys($attachData))->pluck('name');
                foreach ($addedLocationsDetails as $name) {
                    $historyRecords[] = ['event_type' => 'location_added', 'notes' => 'Lokasi "'.$name.'" ditambahkan.'];
                }

                // ✅ 2C. CATAT HISTORI KEPEMILIKAN: BARU DITAMBAHKAN
                foreach (array_keys($attachData) as $locId) {
                    ParkingLocationHistory::create([
                        'parking_location_id' => $locId,
                        'user_id' => $userId,
                        'action' => 'owner_changed',
                        'description' => "Lokasi diserahkan ke Koordinator: {$korlapName} (PKS: {$agreement->agreement_number}).",
                    ]);
                }
            }

            // Set semua lokasi yang terpilih menjadi 'tidak_tersedia'
            if (! empty($newLocationIds)) {
                ParkingLocation::whereIn('id', $newLocationIds)->update(['status' => 'tidak_tersedia']);
            }

            // Simpan semua catatan riwayat
            if (! empty($historyRecords)) {
                foreach ($historyRecords as &$record) {
                    $record['agreement_id'] = $agreement->id;
                    $record['changed_by_user_id'] = Auth::id();
                    $record['created_at'] = $now;
                    $record['updated_at'] = $now;
                    if (! isset($record['event_type'])) {
                        $record['event_type'] = 'details_updated';
                    }
                }
                AgreementHistory::insert($historyRecords);

                // Generate PDF versi terbaru
                $pdfNotes = implode('; ', Arr::pluck($historyRecords, 'notes'));
                $freshAgreement = Agreement::with(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection'])->find($agreement->id);
                $this->generateAndStorePdfHistory($freshAgreement, $pdfNotes);
            }

            DB::commit();

            return redirect()->route('masterdata.agreements.index')
                ->with('success', 'Perjanjian berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AgreementController@update: Error updating agreement: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui perjanjian: '.$e->getMessage());
        }
    }

    public function destroy(Agreement $agreement)
    {
        DB::beginTransaction();
        try {
            $activeLocationIds = $agreement->activeParkingLocations()->pluck('parking_locations.id')->toArray();

            if (! empty($activeLocationIds)) {
                ParkingLocation::whereIn('id', $activeLocationIds)->update(['status' => 'tersedia']);

                // ✅ 3. CATAT HISTORI KEPEMILIKAN: PKS DIHAPUS
                foreach ($activeLocationIds as $locId) {
                    ParkingLocationHistory::create([
                        'parking_location_id' => $locId,
                        'user_id' => Auth::id(),
                        'action' => 'owner_changed',
                        'description' => "Lokasi dikeluarkan karena PKS {$agreement->agreement_number} dibatalkan/dihapus.",
                    ]);
                }
            }
            $agreement->delete();
            DB::commit();

            return redirect()->route('masterdata.agreements.index')->with('success', 'Perjanjian berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('AgreementController@destroy: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal menghapus perjanjian.');
        }
    }

    public function detachParkingLocation(Agreement $agreement, ParkingLocation $parkingLocation)
    {
        DB::beginTransaction();
        try {
            $agreement->parkingLocations()->updateExistingPivot($parkingLocation->id, [
                'status' => 'inactive',
                'removed_date' => now(),
            ]);

            $parkingLocation->update(['status' => 'tersedia']);

            // ✅ 4. CATAT HISTORI KEPEMILIKAN: DIKELUARKAN MANUAL
            ParkingLocationHistory::create([
                'parking_location_id' => $parkingLocation->id,
                'user_id' => Auth::id(),
                'action' => 'owner_changed',
                'description' => "Lokasi dikeluarkan/ditarik secara manual dari PKS: {$agreement->agreement_number}.",
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Lokasi parkir "'.$parkingLocation->name.'" berhasil dikeluarkan dari perjanjian.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error detaching parking location: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Gagal mengeluarkan lokasi parkir: '.$e->getMessage());
        }
    }

    // ====================================================================
    // 1. FUNGSI UTAMA GENERATE PDF UNTUK DITAMPILKAN KE BROWSER
    // ====================================================================
    public function generatePdf(Agreement $agreement)
    {
        $user = Auth::user();
        $agreement->load(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection']);

        // ✅ KEAMANAN TAMBAHAN: Cegah Korlap melihat PDF PKS milik orang lain!
        if ($user->hasRole('field_coordinator')) {
            if ($agreement->field_coordinator_id !== $user->fieldCoordinator->id) {
                abort(403, 'Akses Ditolak! Anda tidak berhak melihat Dokumen PKS ini.');
            }
        }

        $activeBankAccount = BludBankAccount::where('is_active', true)->first();
        $uptProfile = UptProfile::first();

        // ✅ PANGGIL FUNGSI HELPER QR CODE
        $qrCodeImage = $this->generateQrCodeImage($agreement);

        $pdf = Pdf::loadView('pdf.agreement', compact('agreement', 'activeBankAccount', 'uptProfile', 'qrCodeImage'));

        return $pdf->stream(str_replace('/', '_', $agreement->agreement_number).'_'.date('dmY', strtotime($agreement->start_date)).'-'.date('dmy', strtotime($agreement->end_date)).'.pdf');
    }

    // ====================================================================
    // 2. FUNGSI UNTUK GENERATE & SIMPAN PDF KE DALAM HISTORY (STORAGE)
    // ====================================================================
    private function generateAndStorePdfHistory(Agreement $agreement, string $notes)
    {
        try {
            $activeBankAccount = BludBankAccount::where('is_active', true)->first();
            if (! $activeBankAccount) {
                Log::warning("No active BLUD bank account found for generating PDF for agreement ID: {$agreement->id}");
            }
            $uptProfile = UptProfile::first();

            // ✅ PANGGIL FUNGSI HELPER QR CODE JUGA DI SINI! (Biar nggak Error Undefined Variable)
            $qrCodeImage = $this->generateQrCodeImage($agreement);

            $pdf = Pdf::loadView('pdf.agreement', compact('agreement', 'activeBankAccount', 'uptProfile', 'qrCodeImage'));
            $pdfContent = $pdf->output();

            $fileName = 'PKS_'.str_replace('/', '_', $agreement->agreement_number).'_'.time().'.pdf';
            $filePath = 'agreements_history/'.$fileName;

            Storage::disk('public')->put($filePath, $pdfContent);

            AgreementPdfHistory::create([
                'agreement_id' => $agreement->id,
                'file_path' => $filePath,
                'notes' => $notes,
                'generated_by_user_id' => Auth::id(),
            ]);

            Log::info("Successfully generated PDF history for agreement ID: {$agreement->id}");
        } catch (\Exception $e) {
            Log::error("Failed to generate PDF history for agreement ID {$agreement->id}: ".$e->getMessage());
            throw $e;
        }
    }

    // ====================================================================
    // 3. MENAMPILKAN DAFTAR HISTORY PDF
    // ====================================================================
    public function showPdfHistory(Agreement $agreement)
    {
        $histories = $agreement->pdfHistories()->with('generator')->paginate(10);

        return view('staff.agreements.pdf_history', compact('agreement', 'histories'));
    }

    // ====================================================================
    // 4. PRIVATE HELPER: GENERATOR QR CODE PREMIUM (GRAYSCALE LOGO)
    // ====================================================================
    private function generateQrCodeImage(Agreement $agreement)
    {
        $qrCodeImage = null;

        if ($agreement->verification_code) {
            $dishubLogoPath = storage_path('images/dishub.png');
            $url = route('public.agreement.verify', $agreement->verification_code);

            if (file_exists($dishubLogoPath)) {
                try {
                    $img = imagecreatefrompng($dishubLogoPath);
                    imagepalettetotruecolor($img);

                    // Ubah Logo Menjadi Grayscale
                    imagefilter($img, IMG_FILTER_GRAYSCALE);

                    // Buat Canvas Putih
                    $width = imagesx($img);
                    $height = imagesy($img);
                    $canvasSize = max($width, $height) + 20;

                    $canvas = imagecreatetruecolor($canvasSize, $canvasSize);
                    $white = imagecolorallocate($canvas, 255, 255, 255);
                    imagefill($canvas, 0, 0, $white);

                    // Tempelkan logo
                    $dstX = ($canvasSize - $width) / 2;
                    $dstY = ($canvasSize - $height) / 2;
                    imagecopy($canvas, $img, $dstX, $dstY, 0, 0, $width, $height);

                    ob_start();
                    imagepng($canvas);
                    $logoString = ob_get_clean();

                    imagedestroy($img);
                    imagedestroy($canvas);

                    // Merge Canvas Putih Berlogo dengan QR Code
                    $qrCodeImage = base64_encode(
                        QrCode::format('png')
                            ->errorCorrection('H')
                            ->size(200)
                            ->mergeString($logoString, 0.26)
                            ->generate($url)
                    );

                } catch (\Exception $e) {
                    Log::warning('Gagal merge logo QR Code: '.$e->getMessage());
                    $qrCodeImage = base64_encode(QrCode::format('png')->errorCorrection('H')->size(200)->generate($url));
                }
            } else {
                $qrCodeImage = base64_encode(QrCode::format('png')->errorCorrection('H')->size(200)->generate($url));
            }
        }

        return $qrCodeImage;
    }

    /**
     * Handle the upload of a signed agreement document and compress it.
     */
    public function uploadSignedDocument(Request $request, Agreement $agreement)
    {
        $request->validate([
            'signed_document' => 'required|file|mimes:pdf|max:10240', // Max 10MB input
        ]);

        $file = $request->file('signed_document');
        $originalFilename = 'scan_' . time() . '_' . $file->getClientOriginalName();
        $inputPath = $file->path();
        
        // Define storage path
        $storageDir = 'agreements/scans';
        if (!Storage::disk('public')->exists($storageDir)) {
            Storage::disk('public')->makeDirectory($storageDir);
        }
        
        $outputFilename = 'compressed_' . $originalFilename;
        $outputFilePath = storage_path('app/public/' . $storageDir . '/' . $outputFilename);

        try {
            // Compress using Ghostscript
            $process = new Process([
                'gs',
                '-sDEVICE=pdfwrite',
                '-dCompatibilityLevel=1.4',
                '-dPDFSETTINGS=/screen', // Lowest quality / smallest size (72 dpi)
                '-dNOPAUSE',
                '-dQUIET',
                '-dBATCH',
                '-sOutputFile=' . $outputFilePath,
                $inputPath
            ]);

            $process->setTimeout(60); // 60 seconds timeout
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Delete old file if it exists
            if ($agreement->signed_document_path && Storage::disk('public')->exists($agreement->signed_document_path)) {
                Storage::disk('public')->delete($agreement->signed_document_path);
            }

            // Save new path
            $agreement->signed_document_path = $storageDir . '/' . $outputFilename;
            $agreement->save();

            // Calculate new size for response
            $newSize = filesize($outputFilePath);
            $newSizeMb = number_format($newSize / 1048576, 2);

            return response()->json([
                'success' => true,
                'message' => "Dokumen berhasil diupload dan dikompresi ({$newSizeMb} MB).",
                'path' => Storage::url($agreement->signed_document_path)
            ]);

        } catch (\Exception $e) {
            \Log::error('PDF Compression failed: ' . $e->getMessage());
            
            // Fallback: just store the original file if compression fails
            $path = $file->storeAs($storageDir, $originalFilename, 'public');
            
            // Delete old file if it exists
            if ($agreement->signed_document_path && Storage::disk('public')->exists($agreement->signed_document_path)) {
                Storage::disk('public')->delete($agreement->signed_document_path);
            }
            
            $agreement->signed_document_path = $path;
            $agreement->save();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupload (Tanpa Kompresi, karena terjadi kesalahan pada server).',
                'path' => Storage::url($agreement->signed_document_path)
            ]);
        }
    }
}
