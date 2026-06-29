<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jukir;
use Illuminate\Support\Facades\Storage;

class JukirController extends Controller
{
    public function index(Request $request)
    {
        $jukirs = Jukir::with('parkingLocation')->latest()->get();
        $parkingLocations = \App\Models\ParkingLocation::with(['roadSection', 'agreements' => function($q) {
            $q->wherePivot('status', 'active')->with('leader');
        }])->orderBy('name')->get();
        return view('admin.jukirs.index', compact('jukirs', 'parkingLocations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jukir' => 'required|string|max:255',
            'parking_location_id' => 'required|exists:parking_locations,id',
            'no_ktp' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('jukir_images', 'public');
        }

        Jukir::create([
            'nama_jukir' => $request->nama_jukir,
            'parking_location_id' => $request->parking_location_id,
            'no_ktp' => $request->no_ktp,
            'phone_number' => $request->phone_number,
            'image' => $imagePath,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.jukirs.index')->with('success', 'Data Jukir berhasil ditambahkan.');
    }

    public function update(Request $request, Jukir $jukir)
    {
        $request->validate([
            'nama_jukir' => 'required|string|max:255',
            'parking_location_id' => 'required|exists:parking_locations,id',
            'no_ktp' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        $imagePath = $jukir->image;
        if ($request->hasFile('image')) {
            if ($jukir->image) {
                Storage::disk('public')->delete($jukir->image);
            }
            $imagePath = $request->file('image')->store('jukir_images', 'public');
        }

        $jukir->update([
            'nama_jukir' => $request->nama_jukir,
            'parking_location_id' => $request->parking_location_id,
            'no_ktp' => $request->no_ktp,
            'phone_number' => $request->phone_number,
            'image' => $imagePath,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.jukirs.index')->with('success', 'Data Jukir berhasil diupdate.');
    }

    public function destroy(Jukir $jukir)
    {
        if ($jukir->image) {
            Storage::disk('public')->delete($jukir->image);
        }
        $jukir->delete();

        return redirect()->route('admin.jukirs.index')->with('success', 'Data Jukir berhasil dihapus.');
    }
}
