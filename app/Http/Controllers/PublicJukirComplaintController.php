<?php

namespace App\Http\Controllers;

use App\Models\Jukir;
use App\Models\JukirComplaint;
use App\Traits\SendsWhatsApp;
use Illuminate\Http\Request;

class PublicJukirComplaintController extends Controller
{
    use SendsWhatsApp;

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
            'reporter_phone' => 'required|string|max:20',
            'category' => 'required|string|in:tarif,pelayanan,keamanan,kebersihan,lainnya',
            'description' => 'required|string',
            'evidence.*' => 'nullable|file|mimes:jpeg,png,jpg|max:5120',
        ], [
            'reporter_name.required' => 'Nama pelapor wajib diisi.',
            'reporter_phone.required' => 'No HP / WhatsApp wajib diisi.',
            'category.required' => 'Kategori pengaduan wajib dipilih.',
            'description.required' => 'Deskripsi pengaduan wajib diisi.',
        ]);

        $evidencePaths = [];
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $path = $file->store('complaints_evidence', 'public');
                $evidencePaths[] = $path;
            }
        }

        $complaint = JukirComplaint::create([
            'jukir_id' => $jukir->id,
            'reporter_name' => $request->reporter_name,
            'reporter_phone' => $request->reporter_phone,
            'category' => $request->category,
            'description' => $request->description,
            'status' => 'pending',
            'evidence' => count($evidencePaths) > 0 ? $evidencePaths : null,
        ]);

        // Kirim notifikasi WA Fonnte "Laporan Diterima"
        if ($request->reporter_phone) {
            $msg = "Halo *{$request->reporter_name}*,\n\n";
            $msg .= "Laporan pengaduan Anda terhadap Juru Parkir *{$jukir->nama_jukir}* telah kami terima.\n\n";
            $msg .= "Kategori: ".ucfirst($request->category)."\n";
            $msg .= "Status saat ini: *Menunggu Review*\n\n";
            $msg .= "Kami akan segera menindaklanjuti laporan ini. Terima kasih atas kepedulian Anda terhadap layanan perparkiran Kota Pekanbaru.\n\n";
            $msg .= "Hormat kami,\n*UPT Perparkiran Dishub Pekanbaru*";

            $this->sendWhatsAppNotification($request->reporter_phone, $msg);
        }

        return redirect()->route('public.jukir.complaint.success', $id_jukir)->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function success($id_jukir)
    {
        $jukir = Jukir::where('id_jukir', $id_jukir)->firstOrFail();
        return view('public.jukir_complaint_success', compact('jukir'));
    }
}
