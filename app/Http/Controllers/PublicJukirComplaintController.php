<?php

namespace App\Http\Controllers;

use App\Models\Jukir;
use App\Models\JukirComplaint;
use Illuminate\Http\Request;

class PublicJukirComplaintController extends Controller
{
    public function show($id_jukir)
    {
        $jukir = Jukir::where('id_jukir', $id_jukir)->with('parkingLocation.roadSection')->firstOrFail();
        return view('public.jukir_details', compact('jukir'));
    }

    public function create($id_jukir)
    {
        $jukir = Jukir::where('id_jukir', $id_jukir)->with('parkingLocation.roadSection')->firstOrFail();
        return view('public.jukir_complaint', compact('jukir'));
    }

    public function store(Request $request, $id_jukir)
    {
        $jukir = Jukir::where('id_jukir', $id_jukir)->firstOrFail();

        $request->validate([
            'reporter_name' => 'required|string|max:255',
            'reporter_phone' => 'nullable|string|max:20',
            'category' => 'required|string|in:tarif,pelayanan,keamanan,kebersihan,lainnya',
            'description' => 'required|string',
        ], [
            'reporter_name.required' => 'Nama pelapor wajib diisi.',
            'category.required' => 'Kategori pengaduan wajib dipilih.',
            'description.required' => 'Deskripsi pengaduan wajib diisi.',
        ]);

        JukirComplaint::create([
            'jukir_id' => $jukir->id,
            'reporter_name' => $request->reporter_name,
            'reporter_phone' => $request->reporter_phone,
            'category' => $request->category,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('public.jukir.complaint.success', $id_jukir)->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function success($id_jukir)
    {
        $jukir = Jukir::where('id_jukir', $id_jukir)->firstOrFail();
        return view('public.jukir_complaint_success', compact('jukir'));
    }
}
