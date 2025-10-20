<?php
namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

// Pastikan FormRequest ini sudah di-import
use Illuminate\View\View;

class ProfileSettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan profil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna (nama, email, foto).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validasi data profil dasar
        $validatedData = $request->validateWithBag('updateProfile', [
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',                             // Hanya boleh huruf, angka, strip (-), dan underscore (_)
                Rule::unique('users')->ignore($user->id), // Cek unik, tapi abaikan user saat ini
            ],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'img'      => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:1024'],
        ],
            // ✅ Tambahkan pesan error custom
            [
                'username.unique'     => 'Username ini sudah digunakan oleh pengguna lain.',
                'username.alpha_dash' => 'Username hanya boleh mengandung huruf, angka, strip, dan underscore.',
            ]);

        // Update nama dan email
        $user->fill($request->except('img'));

        // Cek jika email diganti, reset verifikasi email
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle upload foto profil baru
        if ($request->hasFile('img')) {
            // Hapus foto lama jika ada
            if ($user->img) {
                Storage::disk('public')->delete($user->img);
            }
            // Simpan foto baru dan update path di database
            $path      = $request->file('img')->store('avatars', 'public');
            $user->img = $path;
        }

        $user->save();

        return Redirect::route('profile.settings')->with('status', 'profile-updated');
    }

    /**
     * Memperbarui kata sandi pengguna.
     * Menggunakan FormRequest untuk validasi.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        // Validasi sudah otomatis dijalankan oleh UpdatePasswordRequest.
        $validated = $request->validated();

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('profile.settings')->with('status', 'password-updated');
    }

    /**
     * Menghapus foto profil pengguna.
     */
    public function deleteImage(): JsonResponse
    {
        $user = Auth::user();

        if ($user->img) {
            Storage::disk('public')->delete($user->img);
            $user->img = null;
            $user->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada foto untuk dihapus.']);
    }
}
