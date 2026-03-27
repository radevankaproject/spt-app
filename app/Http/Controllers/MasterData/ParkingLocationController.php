<?php
namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Imports\ParkingLocationsImport;
use App\Models\Agreement;
use App\Models\ParkingLocation;
use App\Models\RoadSection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ParkingLocation::with([
            'roadSection',
            'agreements' => function ($query) {
                $query->where('agreements.status', 'active')->with('fieldCoordinator.user');
            },
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

        $parkingLocations = $query->latest()->paginate(10);

        return view('staff.parking_locations.index', compact('parkingLocations', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
        $validatedData = $request->validate([
            'road_section_id'          => 'required|exists:road_sections,id',
            'name'                     => [
                'required',
                'string',
                'max:255',
                Rule::unique('parking_locations')->where(function ($query) use ($request) {
                    return $query->where('road_section_id', $request->road_section_id);
                }),
            ],
            'daily_deposit'            => 'required|numeric|min:0',
            'latitude'                 => 'nullable|string|max:255',
            'longitude'                => 'nullable|string|max:255',
            'image'                    => 'nullable|image|mimes:jpeg,png,jpg|max:300',
            'proposal_document'        => 'nullable|file|mimes:pdf|max:2048',
            'official_report_document' => 'nullable|file|mimes:pdf|max:2048',
        ],
            [
                'road_section_id.required'       => 'Ruas jalan wajib dipilih.',
                'road_section_id.exists'         => 'Ruas jalan yang dipilih tidak valid.',
                'name.required'                  => 'Nama lokasi parkir wajib diisi.',
                'name.string'                    => 'Nama lokasi parkir harus berupa teks.',
                'name.max'                       => 'Nama lokasi parkir tidak boleh lebih dari 255 karakter.',
                'name.unique'                    => 'Nama lokasi parkir sudah ada di ruas jalan ini.',
                'daily_deposit.required'         => 'Setoran harian wajib diisi.',
                'daily_deposit.numeric'          => 'Setoran harian harus berupa angka.',
                'daily_deposit.min'              => 'Setoran harian tidak boleh minus.',
                'image.image'                    => 'File harus berupa gambar.',
                'image.mimes'                    => 'Gambar harus berformat JPEG, PNG, atau JPG.',
                'image.max'                      => 'Ukuran gambar tidak boleh lebih dari 300 KB.',
                'proposal_document.file'         => 'Dokumen pengajuan harus berupa file.',
                'proposal_document.mimes'        => 'Dokumen pengajuan harus berformat PDF.',
                'proposal_document.max'          => 'Ukuran dokumen pengajuan tidak boleh lebih dari 2 MB.',
                'official_report_document.file'  => 'Dokumen berita acara harus berupa file.',
                'official_report_document.mimes' => 'Dokumen berita acara harus berformat PDF.',
                'official_report_document.max'   => 'Ukuran dokumen berita acara tidak boleh lebih dari 2 MB.',
            ]);

        $dataToStore           = Arr::except($validatedData, ['image', 'proposal_document', 'official_report_document']);
        $dataToStore['status'] = 'tersedia';

        $uniqueSuffix = Str::random(8);

        if ($request->hasFile('image')) {
            $imageName            = time() . '_' . $uniqueSuffix . '.' . $request->image->extension();
            $dataToStore['image'] = $request->file('image')
                ->storeAs('uploads/locations/images', $imageName, 'public');
        }

        if ($request->hasFile('proposal_document')) {
            $proposalName                     = time() . '_' . $uniqueSuffix . '.' . $request->proposal_document->extension();
            $dataToStore['proposal_document'] = $request->file('proposal_document')
                ->storeAs('uploads/locations/proposals', $proposalName, 'public');
        }

        if ($request->hasFile('official_report_document')) {
            $reportName                              = time() . '_' . $uniqueSuffix . '.' . $request->official_report_document->extension();
            $dataToStore['official_report_document'] = $request->file('official_report_document')
                ->storeAs('uploads/locations/reports', $reportName, 'public');
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
        $parkingLocation->load(['roadSection']);

        $activeAgreement = Agreement::whereHas('parkingLocations', function ($query) use ($parkingLocation) {
            $query->where('parking_location_id', $parkingLocation->id)->where('agreement_parking_locations.status', 'active');
        })
            ->where('status', 'active')
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
        $validatedData = $request->validate([
            'road_section_id'          => 'required|exists:road_sections,id',
            'name'                     => [
                'required',
                'string',
                'max:255',
                Rule::unique('parking_locations')->where(function ($query) use ($request) {
                    return $query->where('road_section_id', $request->road_section_id);
                })->ignore($parkingLocation->id),
            ],
            'daily_deposit'            => 'required|numeric|min:0',
            'latitude'                 => 'nullable|string|max:255',
            'longitude'                => 'nullable|string|max:255',
            'image'                    => 'nullable|image|mimes:jpeg,png,jpg|max:300',
            'proposal_document'        => 'nullable|file|mimes:pdf|max:2048',
            'official_report_document' => 'nullable|file|mimes:pdf|max:2048',
        ],
            [
                'road_section_id.required'       => 'Ruas jalan wajib dipilih.',
                'road_section_id.exists'         => 'Ruas jalan yang dipilih tidak valid.',
                'name.required'                  => 'Nama lokasi parkir wajib diisi.',
                'name.string'                    => 'Nama lokasi parkir harus berupa teks.',
                'name.max'                       => 'Nama lokasi parkir tidak boleh lebih dari 255 karakter.',
                'name.unique'                    => 'Nama lokasi parkir sudah ada di ruas jalan ini.',
                'daily_deposit.required'         => 'Setoran harian wajib diisi.',
                'daily_deposit.numeric'          => 'Setoran harian harus berupa angka.',
                'daily_deposit.min'              => 'Setoran harian tidak boleh minus.',
                'image.image'                    => 'File harus berupa gambar.',
                'image.mimes'                    => 'Gambar harus berformat JPEG, PNG, atau JPG.',
                'image.max'                      => 'Ukuran gambar tidak boleh lebih dari 300 KB.',
                'proposal_document.file'         => 'Dokumen pengajuan harus berupa file.',
                'proposal_document.mimes'        => 'Dokumen pengajuan harus berformat PDF.',
                'proposal_document.max'          => 'Ukuran dokumen pengajuan tidak boleh lebih dari 2 MB.',
                'official_report_document.file'  => 'Dokumen berita acara harus berupa file.',
                'official_report_document.mimes' => 'Dokumen berita acara harus berformat PDF.',
                'official_report_document.max'   => 'Ukuran dokumen berita acara tidak boleh lebih dari 2 MB.',
            ]);

        $dataToUpdate = Arr::except($validatedData, ['image', 'proposal_document', 'official_report_document']);

        $deleteOldFile = function ($filePath) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
        };

        $uniqueSuffix = Str::random(8);

        if ($request->hasFile('image')) {
            $deleteOldFile($parkingLocation->image); // Hapus file lama
            $imageName             = time() . '_' . $uniqueSuffix . '.' . $request->image->extension();
            $dataToUpdate['image'] = $request->file('image')
                ->storeAs('uploads/locations/images', $imageName, 'public');
        }

        if ($request->hasFile('proposal_document')) {
            $deleteOldFile($parkingLocation->proposal_document); // Hapus file lama
            $proposalName                      = time() . '_' . $uniqueSuffix . '.' . $request->proposal_document->extension();
            $dataToUpdate['proposal_document'] = $request->file('proposal_document')
                ->storeAs('uploads/locations/proposals', $proposalName, 'public');
        }

        if ($request->hasFile('official_report_document')) {
            $deleteOldFile($parkingLocation->official_report_document); // Hapus file lama
            $reportName                               = time() . '_' . $uniqueSuffix . '.' . $request->official_report_document->extension();
            $dataToUpdate['official_report_document'] = $request->file('official_report_document')
                ->storeAs('uploads/locations/reports', $reportName, 'public');
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
            if ($parkingLocation->image) {
                Storage::disk('public')->delete($parkingLocation->image);
            }
            if ($parkingLocation->proposal_document) {
                Storage::disk('public')->delete($parkingLocation->proposal_document);
            }
            if ($parkingLocation->official_report_document) {
                Storage::disk('public')->delete($parkingLocation->official_report_document);
            }
            $parkingLocation->delete(); // Soft delete
        } catch (\Exception $e) {
            Log::error('ParkingLocationController@destroy: Error deleting parking location: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus lokasi parkir: ' . $e->getMessage());
        }

        return redirect()->route('masterdata.parking-locations.index')->with('success', 'Lokasi parkir berhasil dihapus!');
    }

    public function getParkingLocationsByRoadSection(Request $request, $roadSectionId)
    {
        // Ambil lokasi parkir yang statusnya 'tersedia'
        // dan belum terikat perjanjian aktif
        $parkingLocations = ParkingLocation::where('road_section_id', $roadSectionId)
            ->where('status', 'tersedia')
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
            'import_file'     => 'required|file|mimes:csv,txt,xlsx,xls',
        ], [
            'road_section_id.required' => 'Anda harus memilih Ruas Jalan.',
            'import_file.required'     => 'Anda harus mengupload file.',
            'import_file.mimes'        => 'File harus berformat CSV, TXT, XLSX, atau XLS.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            // Proses Import
            // Menggunakan class import yang sudah kita perbaiki sebelumnya (tanpa Queue agar bisa ditangkap errornya langsung)
            Excel::import(new ParkingLocationsImport((int) $request->road_section_id), $request->file('import_file'));

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil diimpor ke database!',
            ]);

        } catch (ValidationException $e) {
            // Tangkap error validasi baris per baris dari Excel
            $failures      = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Nilai: ' . json_encode($failure->values()) . ')';
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Terdapat data yang tidak valid dalam file.',
                'errors'  => ['file' => $errorMessages],
            ], 422);

        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem saat memproses file: ' . $e->getMessage(),
            ], 500);
        }
    }
}
