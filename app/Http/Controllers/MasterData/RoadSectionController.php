<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\RoadSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class RoadSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $zoneFilter = $request->input('zone');

        // ✅ Tambahkan withCount untuk menghitung relasi ParkingLocation
        $query = RoadSection::withCount('parkingLocations');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($zoneFilter) {
            $query->where('zone', $zoneFilter);
        }

        $roadSections = $query->latest()->paginate(10);
        return view('admin.road-sections.index', compact('roadSections', 'search', 'zoneFilter'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:road_sections,name',
            'zone' => 'required|string|in:Zona 2,Zona 3',
        ]);

        RoadSection::create($validatedData);

        return redirect()->route('masterdata.road-sections.index')
            ->with('success', 'Ruas jalan ' . $validatedData['name'] . ' berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoadSection $roadSection)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        // Cek apakah sedang digunakan
        $inUse = $roadSection->parkingLocations()->count() > 0;

        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('road_sections')->ignore($roadSection->id)],
        ];

        // ✅ Logika Keamanan Backend: Hanya izinkan ubah zona jika belum ada titik parkir
        if (!$inUse) {
            $rules['zone'] = 'required|string|in:Zona 2,Zona 3';
        }

        $validatedData = $request->validate($rules);

        $roadSection->update($validatedData);

        return redirect()->route('masterdata.road-sections.index')
            ->with('success', 'Ruas Jalan ' . $roadSection->name . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoadSection $roadSection)
    {
        // ✅ Logika Keamanan Backend: Tolak hapus jika ada titik parkir
        if ($roadSection->parkingLocations()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal: Ruas jalan ini sedang digunakan oleh Titik Lokasi Parkir!');
        }

        try {
            $name = $roadSection->name;
            $roadSection->delete();
            return redirect()->route('masterdata.road-sections.index')->with('success', "Ruas jalan '$name' berhasil dihapus!");
        } catch (\Exception $e) {
            Log::error('RoadSectionController@destroy: Error deleting road section: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus ruas jalan: ' . $e->getMessage());
        }
    }
}
