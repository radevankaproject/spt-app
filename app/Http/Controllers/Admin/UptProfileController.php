<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UptProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $profile = UptProfile::firstOrCreate(['id' => 1]);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'app_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'phone_number_admin' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'login_greetings' => 'nullable|string',
            'api_token_fonnte' => 'nullable|string',
            'about_us' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            'social_fb' => 'nullable|string|max:100',
            'social_ig' => 'nullable|string|max:100',
            'social_tiktok' => 'nullable|string|max:100',
            'social_x' => 'nullable|string|max:100',
            'social_youtube' => 'nullable|string|max:100',
            'complaint_website_link' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        try {
            // ✅ CEK LOGO SECARA AGRESIF
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');

                // Cek apakah file valid (tidak korup / putus di jalan)
                if (! $file->isValid()) {
                    throw new \Exception('File upload gagal. Kode Error PHP: '.$file->getError());
                }

                $fileName = 'logo.png';
                $publicPath = public_path();

                // 🚨 CEK PERMISSION FOLDER PUBLIC
                if (! is_writable($publicPath)) {
                    throw new \Exception("Folder public/ TIDAK BISA DITULIS (Permission Denied). Silakan jalankan perintah 'chmod 775 public' atau 'chmod 777 public' di terminal server Anda.");
                }

                // Pindahkan file logo.png menimpa yang lama
                $file->move($publicPath, $fileName);
                $validatedData['logo'] = $fileName;

                // Otomatis update favicon
                $this->generateFavicon($publicPath.'/'.$fileName);
            }

            $profile->update($validatedData);

            return redirect()->route('admin.upt-profile.index')
                ->with('success', 'Profil UPT berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error update profil: '.$e->getMessage());

            // 🚨 TAMPILKAN ERROR EKSTREM KE LAYAR
            return redirect()->back()
                ->withInput()
                ->with('error', '🛑 GAGAL MENYIMPAN: '.$e->getMessage());
        }
    }

    /**
     * Helper untuk generate dan replace favicon.ico
     * Membutuhkan ekstensi PHP GD.
     */
    private function generateFavicon($sourcePath)
    {
        if (! extension_loaded('gd')) {
            Log::warning('Ekstensi PHP GD tidak aktif. Favicon tidak diperbarui.');

            return;
        }

        // Path tujuan (Di root public)
        $faviconPath = public_path('favicon.ico');

        [$width, $height, $type] = getimagesize($sourcePath);

        // Ukuran standar favicon adalah 32x32
        $thumb = imagecreatetruecolor(32, 32);

        // Pertahankan transparansi (untuk PNG)
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, 32, 32, $transparent);

        $source = null;
        if ($type == IMAGETYPE_JPEG) {
            $source = imagecreatefromjpeg($sourcePath);
        } elseif ($type == IMAGETYPE_PNG) {
            $source = imagecreatefrompng($sourcePath);
        }

        if ($source) {
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, 32, 32, $width, $height);
            // Simpan gambar dengan ektensi .ico (browser modern membaca stream PNG dalam file .ico dengan baik)
            imagepng($thumb, $faviconPath);

            // Opsional: Jika template antum menyimpan favicon di path lain, timpah juga
            $templateFavicon = public_path('assets/img/favicon/favicon.ico');
            if (file_exists($templateFavicon)) {
                copy($faviconPath, $templateFavicon);
            }

            // Note: imagedestroy() is deprecated since PHP 8.0/8.5. Let GC handle it.
        }
    }
}
