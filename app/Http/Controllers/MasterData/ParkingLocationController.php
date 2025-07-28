<?php

namespace App\Http\Controllers\MasterData; // Namespace yang sudah kita sepakati

use App\Http\Controllers\Controller;
use App\Models\ParkingLocation;
use App\Models\RoadSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ParkingLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ParkingLocation::with([
            'roadSection',
            'agreements' => function ($query) {
                // ✅ PERBAIKAN DI SINI: Tentukan nama tabel secara eksplisit
                $query->where('agreements.status', 'active')->with('fieldCoordinator.user');
            }
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhereHas('roadSection', function ($roadSectionQuery) use ($search) {
                        $roadSectionQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $parkingLocations = $query->latest()->paginate(15);

        return view('staff.parking_locations.index', compact('parkingLocations', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roadSections = RoadSection::orderBy('name')->get(); // Ambil semua ruas jalan untuk dropdown
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
        $validatedData = $request->validate([
            'road_section_id' => 'required|exists:road_sections,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('parking_locations')->where(function ($query) use ($request) {
                    return $query->where('road_section_id', $request->road_section_id);
                }),
            ],
            'daily_deposit' => 'required|numeric|min:0',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:300', // Maks 300KB
            'proposal_document' => 'nullable|file|mimes:pdf|max:2048', // Maks 2MB
            'official_report_document' => 'nullable|file|mimes:pdf|max:2048', // Maks 2MB
            // Status tidak perlu di form, defaultnya 'tersedia'
        ]);

        $dataToStore = Arr::except($validatedData, ['image', 'proposal_document', 'official_report_document']);
        $dataToStore['status'] = 'tersedia'; // Status default

        // Handle upload gambar lokasi
        if ($request->hasFile('image')) {
            $imageName = time() . '_location.' . $request->image->extension();
            $request->image->move(public_path('uploads/locations/images'), $imageName);
            $dataToStore['image'] = 'uploads/locations/images/' . $imageName;
        }

        // Handle upload PDF Pengajuan
        if ($request->hasFile('proposal_document')) {
            $proposalName = time() . '_proposal.' . $request->proposal_document->extension();
            $request->proposal_document->move(public_path('uploads/locations/proposals'), $proposalName);
            $dataToStore['proposal_document'] = 'uploads/locations/proposals/' . $proposalName;
        }

        // Handle upload PDF Berita Acara
        if ($request->hasFile('official_report_document')) {
            $reportName = time() . '_report.' . $request->official_report_document->extension();
            $request->official_report_document->move(public_path('uploads/locations/reports'), $reportName);
            $dataToStore['official_report_document'] = 'uploads/locations/reports/' . $reportName;
        }

        ParkingLocation::create($dataToStore);

        return redirect()->route('masterdata.parking-locations.index')
            ->with('success', 'Lokasi parkir ' . $validatedData['name'] . ' berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ParkingLocation $parkingLocation)
    {
        // Untuk saat ini, kita tidak akan membuat halaman show terpisah
        // Mungkin akan diarahkan ke halaman edit atau detail di modal.
        return redirect()->route('masterdata.parking-locations.edit', $parkingLocation);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParkingLocation $parkingLocation)
    {
        // Eager load relasi untuk mendapatkan nama zona
        $parkingLocation->load('roadSection');

        // Ambil semua ruas jalan yang berada di zona yang sama dengan lokasi ini
        // untuk mengisi dropdown saat halaman pertama kali dimuat.
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
        $validatedData = $request->validate([
            'road_section_id' => 'required|exists:road_sections,id',
            'name' => ['required', 'string', 'max:255', Rule::unique('parking_locations')->ignore($parkingLocation->id)],
            'daily_deposit' => 'required|numeric|min:0',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:300',
            'proposal_document' => 'nullable|file|mimes:pdf|max:2048',
            'official_report_document' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $dataToUpdate = Arr::except($validatedData, ['image', 'proposal_document', 'official_report_document']);

        // Fungsi untuk menghapus file lama jika ada
        $deleteOldFile = function ($filePath) {
            if ($filePath && file_exists(public_path($filePath))) {
                unlink(public_path($filePath));
            }
        };

        // Handle upload gambar lokasi baru
        if ($request->hasFile('image')) {
            $deleteOldFile($parkingLocation->image);
            $imageName = time() . '_location.' . $request->image->extension();
            $request->image->move(public_path('uploads/locations/images'), $imageName);
            $dataToUpdate['image'] = 'uploads/locations/images/' . $imageName;
        }

        // Handle upload PDF Pengajuan baru
        if ($request->hasFile('proposal_document')) {
            $deleteOldFile($parkingLocation->proposal_document);
            $proposalName = time() . '_proposal.' . $request->proposal_document->extension();
            $request->proposal_document->move(public_path('uploads/locations/proposals'), $proposalName);
            $dataToUpdate['proposal_document'] = 'uploads/locations/proposals/' . $proposalName;
        }

        // Handle upload PDF Berita Acara baru
        if ($request->hasFile('official_report_document')) {
            $deleteOldFile($parkingLocation->official_report_document);
            $reportName = time() . '_report.' . $request->official_report_document->extension();
            $request->official_report_document->move(public_path('uploads/locations/reports'), $reportName);
            $dataToUpdate['official_report_document'] = 'uploads/locations/reports/' . $reportName;
        }

        $parkingLocation->update($dataToUpdate);

        return redirect()->route('masterdata.parking-locations.index')
            ->with('success', 'Lokasi parkir berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParkingLocation $parkingLocation)
    {
        try {
            $parkingLocation->delete(); // Soft delete
        } catch (\Exception $e) {
            Log::error('ParkingLocationController@destroy: Error deleting parking location: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus lokasi parkir: ' . $e->getMessage());
        }

        return redirect()->route('masterdata.parking-locations.index')->with('success', 'Lokasi parkir berhasil dihapus!');
    }

    /**
     * Get parking locations by road section ID for AJAX requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $roadSectionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParkingLocationsByRoadSection(Request $request, $roadSectionId)
    {
        // Ambil lokasi parkir yang statusnya 'tersedia'
        // dan belum terikat perjanjian aktif
        $parkingLocations = ParkingLocation::where('road_section_id', $roadSectionId)
            ->where('status', 'tersedia')
            ->whereDoesntHave('agreements', function ($query) {
                $query->where('agreement_parking_locations.status', 'active'); // Perbaikan wherePivot
            })
            ->get(['id', 'name', 'status', 'road_section_id']); // Hanya ambil kolom yang dibutuhkan

        return response()->json($parkingLocations);
    }
}
