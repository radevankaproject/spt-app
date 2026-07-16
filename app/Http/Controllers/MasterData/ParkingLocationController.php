<?php

namespace App\Http\Controllers\MasterData;

use App\Exports\ParkingLocationsExport;
use App\Http\Controllers\Controller;
use App\Imports\ParkingLocationsImport;
use App\Models\Agreement;
use App\Models\FieldCoordinator;
use App\Models\ParkingLocation;
use App\Models\ParkingLocationHistory;
use App\Models\RoadSection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ParkingLocationController extends Controller
{
    /**
     * Filter Report Helper
     */
    private function applyReportFilters(Request $request, $query)
    {
        if ($request->filled('selected_locations') && is_array($request->selected_locations)) {
            $query->whereIn('parking_locations.id', $request->selected_locations);
            return $query;
        }

        if ($request->filled('korlap_id')) {
            $query->whereHas('agreements', function ($q) use ($request) {
                $q->whereIn('agreements.status', ['active', 'pending_renewal', 'expired'])
                    ->where('agreements.field_coordinator_id', $request->korlap_id);
            });
        }

        if ($request->filled('road_section_id')) {
            $roadSectionIds = is_array($request->road_section_id) ? $request->road_section_id : [$request->road_section_id];
            $query->whereIn('road_section_id', $roadSectionIds);
        }

        if ($request->filled('zone')) {
            $query->whereHas('roadSection', function ($q) use ($request) {
                $q->where('zone', $request->zone);
            });
        }

        if ($request->filled('no_agreement') && $request->no_agreement == '1') {
            $query->whereDoesntHave('agreements', function ($q) {
                $q->where('agreement_parking_locations.status', 'active');
            });
        }

        if ($request->filled('surveyor')) {
            $query->whereHas('latestSurvey', function ($q) use ($request) {
                $q->where('surveyor', 'like', '%' . $request->surveyor . '%');
            });
        }

        if ($request->filled('survey_status')) {
            if ($request->survey_status == 'sudah') {
                $query->has('latestSurvey');
            } elseif ($request->survey_status == 'belum') {
                $query->doesntHave('latestSurvey');
            }
        }

        return $query;
    }

    /**
     * Laporan Titik Lokasi Parkir
     */
    public function report(Request $request)
    {
        $query = ParkingLocation::with(['latestSurvey', 'roadSection', 'agreements' => function ($q) {
            $q->whereIn('agreements.status', ['active', 'pending_renewal', 'expired'])
                ->where('agreement_parking_locations.status', 'active')
                ->with('fieldCoordinator.user');
        }]);

        $query = $this->applyReportFilters($request, $query);

        $parkingLocations = $query->latest()->paginate(20);

        $roadSections = RoadSection::orderBy('name')->get();
        $zones = RoadSection::select('zone')->distinct()->pluck('zone');
        $korlaps = FieldCoordinator::with('user')->get();

        return view('staff.parking_locations.report', compact('parkingLocations', 'roadSections', 'zones', 'korlaps'));
    }

    /**
     * Export Laporan PDF
     */
    public function exportPdf(Request $request)
    {
        $query = ParkingLocation::with(['latestSurvey.jukir', 'roadSection', 'agreements' => function ($q) {
            $q->whereIn('agreements.status', ['active', 'pending_renewal', 'expired'])
                ->where('agreement_parking_locations.status', 'active')
                ->with('fieldCoordinator.user');
        }]);

        $query = $this->applyReportFilters($request, $query);

        $parkingLocations = $query->latest()->get();

        $pdf = Pdf::loadView('staff.parking_locations.report_pdf', compact('parkingLocations', 'request'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan_titik_lokasi_parkir.pdf');
    }

    /**
     * Export Laporan Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new ParkingLocationsExport($request), 'laporan_titik_lokasi_parkir.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roadSectionId = $request->input('road_section_id');
        $status = $request->input('status'); // ✅ Tangkap input status

        $query = ParkingLocation::with([
            'roadSection',
            'agreements' => function ($query) {
                $query->whereIn('agreements.status', ['active', 'pending_renewal', 'expired'])
                    ->where('agreement_parking_locations.status', 'active')
                    ->with('fieldCoordinator.user');
            },
        ]);

        // ✅ Filter berdasarkan Ruas Jalan
        if ($roadSectionId) {
            $query->where('road_section_id', $roadSectionId);
        }

        // ✅ Filter berdasarkan Status
        if ($status) {
            $query->where('status', $status);
        }

        // ✅ Filter berdasarkan Search (Teks)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    // Pencarian status dihilangkan dari sini karena sudah pakai dropdown khusus
                    ->orWhereHas('roadSection', function ($roadSectionQuery) use ($search) {
                        $roadSectionQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $parkingLocations = $query->latest()->paginate(10);

        $roadSections = RoadSection::orderBy('name')->get();

        // ✅ Jangan lupa lempar variabel $status ke view
        return view('staff.parking_locations.index', compact('parkingLocations', 'search', 'roadSections', 'roadSectionId', 'status'));
    }

    /**
     * Tampilkan Peta Seluruh Wilayah Parkir
     */
    public function mapView()
    {
        $totalParkingLocations = ParkingLocation::count();
        $totalMappedLocations = ParkingLocation::whereNotNull('latitude')->whereNotNull('longitude')->count();

        $parkingLocations = ParkingLocation::with([
            'roadSection',
            'agreements' => function ($query) {
                $query->whereIn('agreements.status', ['active', 'pending_renewal', 'expired'])
                    ->where('agreement_parking_locations.status', 'active')
                    ->with('fieldCoordinator.user');
            },
        ])->whereNotNull('latitude')->whereNotNull('longitude')->get();

        return view('staff.parking_locations.map', compact('parkingLocations', 'totalParkingLocations', 'totalMappedLocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $roadSections = RoadSection::orderBy('name')->get();

        return view('staff.parking_locations.create', compact('roadSections'));
    }

    public function getRoadSectionsByZone($zone)
    {
        $roadSections = RoadSection::where('zone', $zone)->orderBy('name', 'asc')->get();

        return response()->json($roadSections);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'road_section_id' => 'required|exists:road_sections,id',
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('parking_locations')->where(function ($query) use ($request) {
                    return $query->where('road_section_id', $request->road_section_id);
                }),
            ],
            'daily_deposit' => 'required|numeric|min:0',
            'estimated_area' => 'nullable|numeric|min:0',
            'estimated_srp_r2' => 'nullable|integer|min:0',
            'estimated_srp_r4' => 'nullable|integer|min:0',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // Limit 10MB, dikompres di client
            'proposal_document' => 'nullable|file|mimes:pdf|max:2048',
            'official_report_document' => 'nullable|file|mimes:pdf|max:2048',
            'is_active' => 'nullable',
            'keterangan' => 'required_without:is_active|nullable|string',
        ], [
            'road_section_id.required' => 'Ruas jalan wajib dipilih.',
            'road_section_id.exists' => 'Ruas jalan yang dipilih tidak valid.',
            'name.required' => 'Nama lokasi parkir wajib diisi.',
            'name.string' => 'Nama lokasi parkir harus berupa teks.',
            'name.max' => 'Nama lokasi parkir tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama lokasi parkir sudah ada di ruas jalan ini.',
            'daily_deposit.required' => 'Setoran harian wajib diisi.',
            'daily_deposit.numeric' => 'Setoran harian harus berupa angka.',
            'daily_deposit.min' => 'Setoran harian tidak boleh minus.',
            'estimated_area.numeric' => 'Estimasi luas wilayah harus berupa angka.',
            'estimated_area.min' => 'Estimasi luas wilayah tidak boleh minus.',
            'estimated_srp_r2.integer' => 'Estimasi SRP R2 harus berupa bilangan bulat.',
            'estimated_srp_r2.min' => 'Estimasi SRP R2 tidak boleh minus.',
            'estimated_srp_r4.integer' => 'Estimasi SRP R4 harus berupa bilangan bulat.',
            'estimated_srp_r4.min' => 'Estimasi SRP R4 tidak boleh minus.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPEG, PNG, atau JPG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 10 MB.',
            'proposal_document.file' => 'Dokumen pengajuan harus berupa file.',
            'proposal_document.mimes' => 'Dokumen pengajuan harus berformat PDF.',
            'proposal_document.max' => 'Ukuran dokumen pengajuan tidak boleh lebih dari 2 MB.',
            'official_report_document.file' => 'Dokumen berita acara harus berupa file.',
            'official_report_document.mimes' => 'Dokumen berita acara harus berformat PDF.',
            'official_report_document.max' => 'Ukuran dokumen berita acara tidak boleh lebih dari 2 MB.',
            'keterangan.required_without' => 'Keterangan wajib diisi jika lokasi sudah tutup/tidak berpotensi.',
        ]);

        $dataToStore = Arr::except($validatedData, ['image', 'proposal_document', 'official_report_document']);
        $dataToStore['status'] = 'tersedia';
        $dataToStore['is_active'] = $request->has('is_active');
        if ($dataToStore['is_active']) {
            $dataToStore['keterangan'] = null;
        }

        $safeName = Str::slug($request->name); // Bikin nama lokasi jadi aman buat URL/File
        $randomNum = mt_rand(100, 999);         // 3 Angka Acak

        if ($request->hasFile('image')) {
            $imageName = time().'_'.Str::random(8).'.'.$request->image->extension();
            $dataToStore['image'] = $request->file('image')->storeAs('uploads/locations/images', $imageName, 'public');
        }

        if ($request->hasFile('proposal_document')) {
            $proposalName = "pengajuan_{$safeName}_{$randomNum}.".$request->proposal_document->extension();
            $dataToStore['proposal_document'] = $request->file('proposal_document')->storeAs('uploads/locations/proposals', $proposalName, 'public');
        }

        if ($request->hasFile('official_report_document')) {
            $reportName = "berita_acara_{$safeName}_{$randomNum}.".$request->official_report_document->extension();
            $dataToStore['official_report_document'] = $request->file('official_report_document')->storeAs('uploads/locations/reports', $reportName, 'public');
        }

        $parkingLocation = ParkingLocation::create($dataToStore);

        ParkingLocationHistory::create([
            'parking_location_id' => $parkingLocation->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'Lokasi parkir baru didaftarkan ke dalam sistem.',
            'new_values' => $dataToStore,
        ]);

        return redirect()->route('masterdata.parking-locations.index')
            ->with('success', 'Lokasi parkir '.$validatedData['name'].' berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ParkingLocation $parkingLocation)
    {
        $parkingLocation->load(['roadSection', 'histories.user', 'latestSurvey.jukir']);

        if (Auth::user()->role === 'field_coordinator') {
            $isOwned = Agreement::where('field_coordinator_id', Auth::user()->fieldCoordinator->id)
                ->whereIn('status', ['active', 'pending_renewal', 'expired'])
                ->whereHas('parkingLocations', function($query) use ($parkingLocation) {
                    $query->where('parking_location_id', $parkingLocation->id)
                          ->where('agreement_parking_locations.status', 'active');
                })->exists();

            abort_if(!$isOwned, 403, 'Akses ditolak! Anda hanya dapat melihat detail lokasi parkir yang termasuk dalam PKS Anda.');
        }

        $activeAgreement = Agreement::whereHas('parkingLocations', function ($query) use ($parkingLocation) {
            $query->where('parking_location_id', $parkingLocation->id)
                ->where('agreement_parking_locations.status', 'active');
        })
            // ✅ FIX: Gunakan whereIn untuk memasukkan 'pending_renewal' juga
            ->whereIn('status', ['active', 'pending_renewal', 'expired'])
            ->with(['fieldCoordinator.user', 'leader.user'])
            ->latest('start_date')
            ->first();

        return view('staff.parking_locations.show', compact('parkingLocation', 'activeAgreement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParkingLocation $parkingLocation)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        // ✅ 1. CEGAH FORCED BROWSING VIA URL (Keamanan Lapis Baja)
        // Fitur baru: Lokasi tidak_tersedia sekarang dapat di-edit sebagian.

        // ✅ 2. LOGIKA ASLI ANTUM (Aman dari error tampilan)
        $parkingLocation->load('roadSection');
        $roadSectionsInZone = RoadSection::where('zone', $parkingLocation->roadSection->zone)
            ->orderBy('name', 'asc')
            ->get();

        return view('staff.parking_locations.edit', compact('parkingLocation', 'roadSectionsInZone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ParkingLocation $parkingLocation)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'road_section_id' => 'required|exists:road_sections,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('parking_locations')->where(function ($query) use ($request) {
                    return $query->where('road_section_id', $request->road_section_id);
                })->ignore($parkingLocation->id),
            ],
            'daily_deposit' => 'required|numeric|min:0',
            'estimated_area' => 'nullable|numeric|min:0',
            'estimated_srp_r2' => 'nullable|integer|min:0',
            'estimated_srp_r4' => 'nullable|integer|min:0',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'proposal_document' => 'nullable|file|mimes:pdf|max:2048',
            'official_report_document' => 'nullable|file|mimes:pdf|max:2048',
            'is_active' => 'nullable',
            'keterangan' => 'required_without:is_active|nullable|string',
        ],
            [
                'road_section_id.required' => 'Ruas jalan wajib dipilih.',
                'road_section_id.exists' => 'Ruas jalan yang dipilih tidak valid.',
                'name.required' => 'Nama lokasi parkir wajib diisi.',
                'name.string' => 'Nama lokasi parkir harus berupa teks.',
                'name.max' => 'Nama lokasi parkir tidak boleh lebih dari 255 karakter.',
                'name.unique' => 'Nama lokasi parkir sudah ada di ruas jalan ini.',
                'daily_deposit.required' => 'Setoran harian wajib diisi.',
                'daily_deposit.numeric' => 'Setoran harian harus berupa angka.',
                'daily_deposit.min' => 'Setoran harian tidak boleh minus.',
                'estimated_area.numeric' => 'Estimasi luas wilayah harus berupa angka.',
                'estimated_area.min' => 'Estimasi luas wilayah tidak boleh minus.',
                'estimated_srp_r2.integer' => 'Estimasi SRP R2 harus berupa bilangan bulat.',
                'estimated_srp_r2.min' => 'Estimasi SRP R2 tidak boleh minus.',
                'estimated_srp_r4.integer' => 'Estimasi SRP R4 harus berupa bilangan bulat.',
                'estimated_srp_r4.min' => 'Estimasi SRP R4 tidak boleh minus.',
                'image.image' => 'File harus berupa gambar.',
                'image.mimes' => 'Gambar harus berformat JPEG, PNG, atau JPG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 10 MB.',
                'proposal_document.file' => 'Dokumen pengajuan harus berupa file.',
                'proposal_document.mimes' => 'Dokumen pengajuan harus berformat PDF.',
                'proposal_document.max' => 'Ukuran dokumen pengajuan tidak boleh lebih dari 2 MB.',
                'official_report_document.file' => 'Dokumen berita acara harus berupa file.',
                'official_report_document.mimes' => 'Dokumen berita acara harus berformat PDF.',
                'official_report_document.max' => 'Ukuran dokumen berita acara tidak boleh lebih dari 2 MB.',
                'keterangan.required_without' => 'Keterangan wajib diisi jika lokasi sudah tutup/tidak berpotensi.',
            ]);

        $dataToUpdate = Arr::except($validatedData, ['image', 'proposal_document', 'official_report_document']);
        $dataToUpdate['is_active'] = $request->has('is_active');
        if ($dataToUpdate['is_active']) {
            $dataToUpdate['keterangan'] = null;
        }

        $deleteOldFile = function ($filePath) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
        };

        $safeName = Str::slug($request->name);
        $randomNum = mt_rand(100, 999);

        if ($request->hasFile('image')) {
            $deleteOldFile($parkingLocation->image);
            $imageName = time().'_'.Str::random(8).'.'.$request->image->extension();
            $dataToUpdate['image'] = $request->file('image')->storeAs('uploads/locations/images', $imageName, 'public');
        }

        if ($request->hasFile('proposal_document')) {
            $deleteOldFile($parkingLocation->proposal_document);
            $proposalName = "pengajuan_{$safeName}_{$randomNum}.".$request->proposal_document->extension();
            $dataToUpdate['proposal_document'] = $request->file('proposal_document')->storeAs('uploads/locations/proposals', $proposalName, 'public');
        }

        if ($request->hasFile('official_report_document')) {
            $deleteOldFile($parkingLocation->official_report_document);
            $reportName = "berita_acara_{$safeName}_{$randomNum}.".$request->official_report_document->extension();
            $dataToUpdate['official_report_document'] = $request->file('official_report_document')->storeAs('uploads/locations/reports', $reportName, 'public');
        }

        $oldData = $parkingLocation->getOriginal();

        $parkingLocation->update($dataToUpdate);

        $changes = $parkingLocation->getChanges();

        // ✅ FIX 1: Buang 'updated_at' dari array dan simpan ke variabel baru
        $actualChanges = Arr::except($changes, ['updated_at']);

        // Cek jika array yang sudah dibersihkan ini tidak kosong
        if (! empty($actualChanges)) {

            // Merangkai kalimat deskripsi yang pintar
            $descParts = [];
            if (isset($actualChanges['name'])) {
                $descParts[] = 'Nama';
            }

            if (isset($actualChanges['road_section_id'])) {
                $descParts[] = 'Ruas Jalan';
            }

            if (isset($actualChanges['daily_deposit'])) {
                $descParts[] = 'Setoran Harian';
            }

            if (isset($actualChanges['latitude']) || isset($actualChanges['longitude'])) {
                $descParts[] = 'Titik Koordinat';
            }

            if (isset($actualChanges['estimated_area']) || isset($actualChanges['estimated_srp_r2']) || isset($actualChanges['estimated_srp_r4'])) {
                $descParts[] = 'Estimasi Luas/SRP';
            }

            if (isset($actualChanges['is_active']) || isset($actualChanges['keterangan'])) {
                $descParts[] = 'Status Aktif/Keterangan';
            }

            if (isset($actualChanges['image']) || isset($actualChanges['proposal_document']) || isset($actualChanges['official_report_document'])) {
                $descParts[] = 'Dokumen/Foto';
            }

            $descText = count($descParts) > 0
                ? 'Memperbarui data: '.implode(', ', $descParts).'.'
                : 'Memperbarui data lokasi.';

            // ✅ CATAT SEJARAH: UPDATED
            ParkingLocationHistory::create([
                'parking_location_id' => $parkingLocation->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => $descText,

                // ✅ FIX 1: Gunakan $actualChanges agar updated_at tidak ikut masuk
                'old_values' => Arr::only($oldData, array_keys($actualChanges)),
                'new_values' => $actualChanges,
            ]);
        }

        return redirect()->route('masterdata.parking-locations.index')
            ->with('success', 'Lokasi parkir berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParkingLocation $parkingLocation)
    {
        try {
            if ($parkingLocation->image) {
                Storage::disk('public')->delete($parkingLocation->image);
            }
            if ($parkingLocation->proposal_document) {
                Storage::disk('public')->delete($parkingLocation->proposal_document);
            }
            if ($parkingLocation->official_report_document) {
                Storage::disk('public')->delete($parkingLocation->official_report_document);
            }
            ParkingLocationHistory::create([
                'parking_location_id' => $parkingLocation->id,
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Lokasi parkir dihapus dari sistem.',
            ]);
            $parkingLocation->delete();
        } catch (\Exception $e) {
            Log::error('ParkingLocationController@destroy: Error deleting parking location: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal menghapus lokasi parkir: '.$e->getMessage());
        }

        return redirect()->route('masterdata.parking-locations.index')->with('success', 'Lokasi parkir berhasil dihapus!');
    }

    /**
     * Toggle status lokasi parkir (antara tersedia dan tidak_tersedia).
     */
    public function toggleStatus(ParkingLocation $parkingLocation)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        // Pastikan tidak terikat ke PKS aktif
        $hasActiveAgreement = Agreement::whereHas('parkingLocations', function ($query) use ($parkingLocation) {
            $query->where('parking_location_id', $parkingLocation->id)
                ->where('agreement_parking_locations.status', 'active');
        })->whereIn('status', ['active', 'pending_renewal', 'expired'])->exists();

        if ($hasActiveAgreement) {
            return redirect()->back()->with('error', 'Gagal! Lokasi ini sedang terikat dengan kontrak yang aktif.');
        }

        $parkingLocation->status = $parkingLocation->status === 'tersedia' ? 'tidak_tersedia' : 'tersedia';
        $parkingLocation->save();

        $statusLabel = $parkingLocation->status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia';

        ParkingLocationHistory::create([
            'parking_location_id' => $parkingLocation->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'description' => "Status lokasi diubah menjadi {$statusLabel}.",
        ]);

        return redirect()->back()->with('success', "Status lokasi {$parkingLocation->name} berhasil diubah menjadi {$statusLabel}.");
    }

    /**
     * Hapus massal lokasi parkir yang dipilih dan berstatus 'tersedia'.
     */
    public function bulkDeleteUnused(Request $request)
    {
        $selectedIds = json_decode($request->input('selected_ids', '[]'), true);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada lokasi parkir yang dipilih.');
        }

        // Cari lokasi parkir yang dipilih dan statusnya 'tersedia'
        $locationsToDelete = ParkingLocation::whereIn('id', $selectedIds)
            ->where('status', 'tersedia')
            ->get();

        if ($locationsToDelete->isEmpty()) {
            return redirect()->back()->with('error', 'Data yang dipilih sudah terikat PKS atau tidak valid untuk dihapus.');
        }

        $count = 0;
        $userId = Auth::id();

        foreach ($locationsToDelete as $location) {
            // Catat history terlebih dahulu
            ParkingLocationHistory::create([
                'parking_location_id' => $location->id,
                'user_id' => $userId,
                'action' => 'deleted',
                'description' => 'Lokasi parkir dihapus secara massal (belum terikat PKS).',
            ]);

            if ($location->image) {
                Storage::disk('public')->delete($location->image);
            }
            if ($location->proposal_document) {
                Storage::disk('public')->delete($location->proposal_document);
            }
            if ($location->official_report_document) {
                Storage::disk('public')->delete($location->official_report_document);
            }

            $location->delete();
            $count++;
        }

        $request->session()->flash('success', "Berhasil menghapus {$count} data lokasi parkir yang terpilih.");

        return redirect()->back();
    }

    public function getParkingLocationsByRoadSection(Request $request, $roadSectionId)
    {
        // Ambil lokasi parkir yang statusnya 'tersedia'
        // dan belum terikat perjanjian aktif
        $parkingLocations = ParkingLocation::where('road_section_id', $roadSectionId)
            ->where('status', 'tersedia')
            ->where('is_active', true)
            ->whereDoesntHave('agreements', function ($query) {
                $query->where('agreement_parking_locations.status', 'active'); // Perbaikan wherePivot
            })
            ->get(['id', 'name', 'status', 'road_section_id', 'daily_deposit']); // Hanya ambil kolom yang dibutuhkan

        return response()->json($parkingLocations);
    }

    /**
     * Menampilkan halaman/form untuk impor.
     */
    public function importCreate()
    {
        // PERUBAHAN: Hanya ambil ruas jalan dari Zona 2 dan 3
        $roadSections = RoadSection::whereIn('zone', ['Zona 2', 'Zona 3'])
            ->orderBy('zone')
            ->orderBy('name')
            ->get();

        // Grouping untuk dikirim ke JS: {'Zona 2': [...], 'Zona 3': [...]}
        $roadSectionsByZone = $roadSections->groupBy('zone')->map(function ($items) {
            return $items->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            });
        });

        // Pastikan kita selalu punya key untuk kedua zona, walau datanya kosong
        if (! isset($roadSectionsByZone['Zona 2'])) {
            $roadSectionsByZone['Zona 2'] = [];
        }
        if (! isset($roadSectionsByZone['Zona 3'])) {
            $roadSectionsByZone['Zona 3'] = [];
        }

        return view('staff.parking_locations.import', compact('roadSectionsByZone'));
    }

    /**
     * Memproses file yang di-upload.
     */
    public function importStore(Request $request)
    {
        // Validasi Request (tambahkan .txt untuk handle isu CSV MS-DOS)
        $validator = Validator::make($request->all(), [
            'road_section_id' => 'required|integer|exists:road_sections,id',
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ], [
            'road_section_id.required' => 'Anda harus memilih Ruas Jalan.',
            'import_file.required' => 'Anda harus mengupload file.',
            'import_file.mimes' => 'File harus berformat CSV, TXT, XLSX, atau XLS.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            // Proses Import
            // Menggunakan class import yang sudah kita perbaiki sebelumnya (tanpa Queue agar bisa ditangkap errornya langsung)
            $import = new ParkingLocationsImport((int) $request->road_section_id);
            Excel::import($import, $request->file('import_file'));

            $request->session()->flash('success', "Berhasil! {$import->rowCount} data lokasi parkir telah ditambahkan.");

            return response()->json([
                'status' => 'success',
                'redirect' => route('masterdata.parking-locations.index'),
            ]);

        } catch (ValidationException $e) {
            // Tangkap error validasi baris per baris dari Excel
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris '.$failure->row().': '.implode(', ', $failure->errors()).' (Nilai: '.json_encode($failure->values()).')';
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Terdapat data yang tidak valid dalam file.',
                'errors' => ['file' => $errorMessages],
            ], 422);

        } catch (QueryException $e) {
            Log::error('Import DB Error: '.$e->getMessage());

            // Kode error 1062 adalah duplicate entry pada MySQL
            if ($e->errorInfo[1] == 1062) {
                preg_match("/Duplicate entry '(.*)' for key/", $e->getMessage(), $matches);

                // MySQL menggabungkan column dengan '-' jika unique constraint lebih dari 1 kolom
                // Karena unique key kita adalah ['road_section_id', 'name'], formatnya: '36-Nama Lokasi'
                $rawDuplicate = $matches[1] ?? '';
                $parts = explode('-', $rawDuplicate, 2);
                $duplicateValue = count($parts) > 1 ? trim($parts[1]) : $rawDuplicate;

                $request->session()->flash('error', "Lokasi <strong>'{$duplicateValue}'</strong> duplikat mohon hapus salah satu sebelum import data.");
            } else {
                $request->session()->flash('error', 'Terjadi kesalahan database saat mengimpor data.');
            }

            return response()->json([
                'status' => 'error',
                'redirect' => route('masterdata.parking-locations.importCreate'),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Import Error: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat memproses file: '.$e->getMessage(),
            ], 500);
        }
    }
}
