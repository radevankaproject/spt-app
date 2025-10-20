<?php
namespace App\Http\Controllers;

use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class AppVersionController extends Controller
{
    /**
     * Mengambil semua versi untuk ditampilkan di modal.
     */
    public function index()
    {
        $versions = AppVersion::latest('release_date')->get();
        return response()->json($versions);
    }

    /**
     * ✅ METHOD BARU: Menampilkan halaman untuk mengelola (menambah) versi aplikasi.
     */
    public function manage()
    {
        // Mengambil semua versi yang ada untuk ditampilkan dalam daftar
        $versions = AppVersion::latest('release_date')->paginate(5);
        return view('admin.versions.manage', compact('versions'));
    }

    /**
     * ✅ METHOD BARU: Menyimpan versi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'version'      => 'required|string|max:255|unique:app_versions,version',
            'release_date' => 'required|date',
            'changelog'    => 'required|string',
        ]);

        AppVersion::create($request->all());
        // Membersihkan cache view agar footer yang baru langsung tampil
        Cache::forget('latest_app_version');
        Artisan::call('view:clear');

        return redirect()->route('admin.app-versions.manage')->with('success', 'Versi baru berhasil ditambahkan!');
    }
}
