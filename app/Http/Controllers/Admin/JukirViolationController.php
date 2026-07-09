<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jukir;
use App\Models\JukirViolation;
use App\Models\JukirHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JukirViolationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jukir_id' => 'required|exists:jukirs,id',
            'description' => 'required|string',
            'violation_date' => 'required|date',
        ]);

        $jukir = Jukir::findOrFail($request->jukir_id);

        $violation = JukirViolation::create([
            'jukir_id' => $jukir->id,
            'user_id' => Auth::id(),
            'description' => $request->description,
            'violation_date' => $request->violation_date,
        ]);

        // Catat riwayat
        JukirHistory::create([
            'jukir_id' => $jukir->id,
            'user_id' => Auth::id(),
            'parking_location_id' => $jukir->parking_location_id,
            'action' => 'Violation',
            'description' => 'Mencatat pelanggaran: ' . $request->description,
        ]);

        // Cek jumlah pelanggaran
        $violationCount = $jukir->violations()->count();
        if ($violationCount > 5 && !$jukir->is_blacklisted) {
            $jukir->update([
                'is_blacklisted' => true,
                'is_active' => false,
            ]);

            JukirHistory::create([
                'jukir_id' => $jukir->id,
                'user_id' => Auth::id(),
                'parking_location_id' => $jukir->parking_location_id,
                'action' => 'Blacklist',
                'description' => 'Otomatis di-blacklist karena telah mencapai lebih dari 5 pelanggaran.',
            ]);

            return redirect()->back()->with('warning', 'Pelanggaran berhasil dicatat. Jukir telah mencapai lebih dari 5 pelanggaran dan otomatis di-blacklist (Nonaktif).');
        }

        return redirect()->back()->with('success', 'Pelanggaran jukir berhasil dicatat.');
    }

    public function destroy(JukirViolation $jukirViolation)
    {
        $jukirId = $jukirViolation->jukir_id;
        $jukirViolation->delete();

        // Optional: un-blacklist if violations drop to <= 5 (not requested, but typically good. The user requested auto-blacklist only, let's keep it simple).

        return redirect()->back()->with('success', 'Data pelanggaran berhasil dihapus.');
    }
}
