<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\YearlyDepositTarget;
use App\Models\MonthlyDepositTarget;
use Illuminate\Http\Request;

class DepositTargetController extends Controller
{
    public function index()
    {
        // Ambil data tahunan beserta anak bulanannya
        $targets = YearlyDepositTarget::with('monthlyTargets')->orderBy('year', 'desc')->get();
        return view('admin.deposit_targets.index', compact('targets'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $request->validate([
            'year'          => 'required|digits:4|integer',
            'month'         => 'required|integer|min:1|max:12',
            'target_amount' => 'required|numeric|min:0'
        ]);

        try {
            // 1. Cari atau buat tahunnya (kalau 2026 belum ada, otomatis dibikin)
            $yearly = YearlyDepositTarget::firstOrCreate(['year' => $request->year]);

            // 2. Simpan atau Update bulannya (kalau bulan 5 sudah ada, nimpa yang lama)
            MonthlyDepositTarget::updateOrCreate(
                ['yearly_deposit_target_id' => $yearly->id, 'month' => $request->month],
                ['target_amount' => $request->target_amount]
            );
            // Begitu kode di atas dieksekusi, total tahunan otomatis terupdate dari model!

            return redirect()->back()->with('success', 'Target Setoran berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan target: ' . $e->getMessage());
        }
    }
}
