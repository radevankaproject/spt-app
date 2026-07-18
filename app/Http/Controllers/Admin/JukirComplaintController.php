<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JukirComplaint;
use App\Traits\SendsWhatsApp;
use Illuminate\Http\Request;

class JukirComplaintController extends Controller
{
    use SendsWhatsApp;

    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = JukirComplaint::with('jukir.parkingLocation');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reporter_name', 'like', "%{$search}%")
                  ->orWhere('reporter_phone', 'like', "%{$search}%")
                  ->orWhereHas('jukir', function ($qJukir) use ($search) {
                      $qJukir->where('nama_jukir', 'like', "%{$search}%")
                             ->orWhere('id_jukir', 'like', "%{$search}%");
                  });
            });
        }

        $complaints = $query->latest()->paginate(15)->appends($request->all());

        return view('admin.jukir_complaints.index', compact('complaints', 'status', 'search'));
    }

    public function show(JukirComplaint $jukirComplaint)
    {
        $jukirComplaint->load('jukir.parkingLocation');
        return view('admin.jukir_complaints.show', compact('jukirComplaint'));
    }

    public function updateStatus(Request $request, JukirComplaint $jukirComplaint)
    {
        $request->validate([
            'status' => 'required|in:valid,invalid',
            'admin_note' => 'nullable|string',
        ]);

        $jukirComplaint->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        // Kirim Notifikasi WA ke Pelapor
        if ($jukirComplaint->reporter_phone) {
            $statusLabel = $request->status == 'valid' ? 'Valid (Akan Ditindaklanjuti)' : 'Tidak Valid (Ditolak)';
            $msg = "Halo *{$jukirComplaint->reporter_name}*,\n\n";
            $msg .= "Laporan pengaduan Anda terhadap Juru Parkir *{$jukirComplaint->jukir->nama_jukir}* telah selesai direview.\n\n";
            $msg .= "Status: *{$statusLabel}*\n";
            if ($request->admin_note) {
                $msg .= "Catatan: {$request->admin_note}\n\n";
            }
            if ($request->status == 'valid') {
                $msg .= "Terima kasih telah melaporkan kejadian ini. Tim kami akan segera menindaklanjuti.\n\n";
            } else {
                $msg .= "Laporan Anda tidak dapat kami proses lebih lanjut dengan alasan yang disebutkan di atas.\n\n";
            }
            $msg .= "Hormat kami,\n*UPT Perparkiran Dishub Pekanbaru*";

            $this->sendWhatsAppNotification($jukirComplaint->reporter_phone, $msg);
        }

        return redirect()->route('admin.jukir-complaints.show', $jukirComplaint)->with('success', 'Status pengaduan berhasil diperbarui dan notifikasi WA telah dikirim (jika nomor tersedia).');
    }

    public function contactList(Request $request)
    {
        $search = $request->input('search');
        
        $query = JukirComplaint::select('reporter_name', 'reporter_phone')
            ->whereNotNull('reporter_phone')
            ->groupBy('reporter_name', 'reporter_phone');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reporter_name', 'like', "%{$search}%")
                  ->orWhere('reporter_phone', 'like', "%{$search}%");
            });
        }

        $contacts = $query->paginate(15)->appends($request->all());

        return view('admin.jukir_complaints.contacts', compact('contacts', 'search'));
    }
}
