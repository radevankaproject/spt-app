<?php
namespace App\Http\Controllers;

use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class AppVersionController extends Controller
{
    /**
     * Mengambil semua versi untuk ditampilkan di modal.
     */
    public function index()
    {
        $versions = AppVersion::latest('release_date')->get()->map(function ($version) {
            // Convert raw markdown to HTML so the modal displays it perfectly
            $version->changelog = \Illuminate\Support\Str::markdown($version->changelog);
            return $version;
        });
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
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $request->validate([
            'version'      => 'required|string|max:255|unique:app_versions,version',
            'release_date' => 'required|date',
            'changelog'    => 'required|string',
        ], [
            'version.required'      => 'Versi aplikasi wajib diisi.',
            'version.string'        => 'Versi aplikasi harus berupa teks.',
            'version.max'           => 'Versi aplikasi tidak boleh lebih dari 255 karakter.',
            'version.unique'        => 'Versi aplikasi ini sudah ada, silakan gunakan versi lain.',
            'release_date.required' => 'Tanggal rilis wajib diisi.',
            'release_date.date'     => 'Format tanggal rilis tidak valid.',
            'changelog.required'    => 'Catatan perubahan (changelog) wajib diisi.',
            'changelog.string'      => 'Catatan perubahan (changelog) harus berupa teks.',
        ]);

        try {
            AppVersion::create($request->all());
            
            // Membersihkan cache view agar footer yang baru langsung tampil
            Cache::forget('version');
            Artisan::call('view:clear');

            return redirect()->route('admin.app-versions.manage')->with('success', 'Versi baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Lempar error detail ke laravel.log
            \Illuminate\Support\Facades\Log::error('Error saat menyimpan AppVersion: ' . $e->getMessage());
            
            // Jika di environment production, tampilkan halaman 500 formal
            if (app()->environment('production')) {
                abort(500, 'Mohon maaf, terjadi kesalahan pada server saat memproses penyimpanan data Anda. Laporan kesalahan telah dicatat secara otomatis dan akan segera ditindaklanjuti oleh tim teknis kami.');
            }
            
            // Jika local/development, tetap tampilkan error aslinya (Whoops/Ignition)
            throw $e;
        }
    }

    /**
     * ✅ METHOD BARU: Memperbarui versi di database.
     */
    public function update(Request $request, AppVersion $appVersion)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $request->validate([
            'version'      => 'required|string|max:255|unique:app_versions,version,' . $appVersion->id,
            'release_date' => 'required|date',
            'changelog'    => 'required|string',
        ], [
            'version.required'      => 'Versi aplikasi wajib diisi.',
            'version.string'        => 'Versi aplikasi harus berupa teks.',
            'version.max'           => 'Versi aplikasi tidak boleh lebih dari 255 karakter.',
            'version.unique'        => 'Versi aplikasi ini sudah ada, silakan gunakan versi lain.',
            'release_date.required' => 'Tanggal rilis wajib diisi.',
            'release_date.date'     => 'Format tanggal rilis tidak valid.',
            'changelog.required'    => 'Catatan perubahan (changelog) wajib diisi.',
            'changelog.string'      => 'Catatan perubahan (changelog) harus berupa teks.',
        ]);

        try {
            $appVersion->update($request->all());
            
            Cache::forget('version');
            Artisan::call('view:clear');

            return redirect()->route('admin.app-versions.manage')->with('success', 'Versi aplikasi berhasil diperbarui!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat mengupdate AppVersion: ' . $e->getMessage());
            if (app()->environment('production')) {
                abort(500, 'Mohon maaf, terjadi kesalahan pada server saat memproses pembaruan data Anda. Laporan kesalahan telah dicatat secara otomatis dan akan segera ditindaklanjuti oleh tim teknis kami.');
            }
            throw $e;
        }
    }

    /**
     * ✅ METHOD BARU: Menghapus versi dari database.
     */
    public function destroy(AppVersion $appVersion)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        try {
            $appVersion->delete();
            
            Cache::forget('version');
            Artisan::call('view:clear');

            return redirect()->route('admin.app-versions.manage')->with('success', 'Versi aplikasi berhasil dihapus!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat menghapus AppVersion: ' . $e->getMessage());
            if (app()->environment('production')) {
                abort(500, 'Mohon maaf, terjadi kesalahan pada server saat menghapus data Anda.');
            }
            throw $e;
        }
    }
}
