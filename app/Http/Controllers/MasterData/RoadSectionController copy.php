<?php

namespace App\Http\Controllers\MasterData; // <--- Ubah dari App\Http\Controllers\Admin

use App\Http\Controllers\Controller;
use App\Models\RoadSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class RoadSectionController extends Controller
{
    // ... semua method (index, create, store, edit, update, destroy) tetap sama ...
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = RoadSection::query();
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        $roadSections = $query->latest()->paginate(10);
        return view('admin.road-sections.index', compact('roadSections', 'search')); // <-- Tetap mengarah ke view admin karena ini master data
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.road-sections.create'); // <-- Tetap mengarah ke view admin
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ Tambahkan validasi untuk 'zone'
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:road_sections,name',
            'zone' => 'required|string|in:Zona 2,Zona 3',
        ]);

        RoadSection::create($validatedData);

        return redirect()->route('masterdata.road-sections.index') // <--- Ubah nama route
            ->with('success', 'Ruas jalan ' . $validatedData['name'] . ' berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoadSection $roadSection)
    {
        return view('admin.road-sections.edit', compact('roadSection')); // <-- Tetap mengarah ke view admin
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoadSection $roadSection)
    {
        // ✅ Validasi disesuaikan untuk update
        $validatedData = $request->validate([
            // Pastikan nama unik, kecuali untuk data itu sendiri
            'name' => ['required', 'string', 'max:255', Rule::unique('road_sections')->ignore($roadSection->id)],
            'zone' => 'required|string|in:Zona 2,Zona 3',
        ]);

        $roadSection->update($validatedData);

        return redirect()->route('masterdata.road-sections.index')
            ->with('success', 'Ruas Jalan ' . $roadSection->name . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoadSection $roadSection)
    {
        try {
            $roadSection->delete();
        } catch (\Exception $e) {
            Log::error('RoadSectionController@destroy: Error deleting road section: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus ruas jalan: ' . $e->getMessage());
        }
        return redirect()->route('masterdata.road-sections.index')->with('success', 'Ruas jalan berhasil dihapus!'); // <--- Ubah nama route
    }
}
