<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil semua data setting dan ubah menjadi format array key => value
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        // Ambil semua input kecuali token dan file logo
        $data = $request->except(['_token', '_method', 'app_logo']);

        // Looping untuk simpan/update data teks
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Eksekusi khusus untuk Logo / Favicon
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');

            // 1. Simpan path logo ke database
            $logoName = 'logo.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/logo'), $logoName);
            Setting::updateOrCreate(
                ['key' => 'app_logo'],
                ['value' => 'uploads/logo/'.$logoName]
            );

            // 2. Timpa langsung file favicon.ico di root public
            // Browser modern bisa membaca format gambar apapun yang direname menjadi .ico
            File::copy(public_path('uploads/logo/'.$logoName), public_path('favicon.ico'));
        }

        return back()->with('success', 'Pengaturan aplikasi berhasil diperbarui!');
    }
}
