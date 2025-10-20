<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UptProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UptProfileController extends Controller
{
    /**
     * Terapkan middleware untuk memastikan hanya Admin yang bisa mengakses.
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('role:admin');
    // }

    /**
     * Menampilkan halaman utama untuk profil UPT.
     */
    public function index()
    {
        // Ambil data profil pertama, atau buat baru jika belum ada.
        $profile = UptProfile::firstOrCreate(
            ['id' => 1],                                  // Kunci untuk memastikan hanya ada 1 baris
            ['name' => 'UPT Perparkiran Dishub Pekanbaru']// Nilai default jika baru dibuat
        );
        return view('admin.upt_profile.index', compact('profile'));
    }

    /**
     * Memperbarui data profil UPT.
     */
    public function update(Request $request)
    {
        $profile = UptProfile::firstOrCreate(['id' => 1]);

        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'app_name' => 'required|string|max:255',
            'address'  => 'nullable|string',
            'logo'     => 'nullable|image|mimes:png,jpg,jpeg|max:512',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
            'website'  => 'nullable|url|max:255',
        ], [
            'name.required'     => 'Nama Instansi (UPT) wajib diisi.',
            'name.max'          => 'Nama Instansi tidak boleh lebih dari 255 karakter.',
            'app_name.required' => 'Nama Aplikasi wajib diisi.',
            'app_name.max'      => 'Nama Aplikasi tidak boleh lebih dari 255 karakter.',
            'logo.image'        => 'File yang diupload harus berupa gambar.',
            'logo.mimes'        => 'Logo harus berformat PNG, JPG, atau JPEG.',
            'logo.max'          => 'Ukuran logo tidak boleh lebih dari 512 KB.',
            'phone.max'         => 'Nomor Telepon tidak boleh lebih dari 20 karakter.',
            'email.email'       => 'Format alamat email yang Anda masukkan tidak valid.',
            'website.url'       => 'Format URL website tidak valid (contoh: https://website.com).',
        ]);

        try {
            if ($request->hasFile('logo')) {
                // Hapus logo lama dari storage jika ada
                if ($profile->logo) {
                    Storage::disk('public')->delete($profile->logo);
                }
                // Simpan logo baru ke storage/app/public/logos
                $path                  = $request->file('logo')->storeAs('logos', 'upt_logo.png', 'public');
                $validatedData['logo'] = $path;
            }

            $profile->update($validatedData);

            return redirect()->route('admin.upt-profile.index')
                ->with('success', 'Profil UPT berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating UPT profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui profil.');
        }
    }
}
