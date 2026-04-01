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
use Illuminate\View\View;

class ProfileSettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan profil disesuaikan dengan Role.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // ✅ LOGIKA LOAD RELASI BERDASARKAN ROLE (Eager Loading)
        if ($user->role === 'field_coordinator') {
            $user->load('fieldCoordinator');
        } elseif ($user->role === 'leader') {
            $user->load('leader');
        } elseif ($user->role === 'bendahara' || $user->role === 'staff_keu') {
            // Asumsi model bendahara berelasi dengan user
            if (method_exists($user, 'treasurer')) {
                $user->load('treasurer');
            }
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna (nama, email, foto).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validatedData = $request->validateWithBag('updateProfile', [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'img' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:1024'],
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh pengguna lain.',
            'username.alpha_dash' => 'Username hanya boleh mengandung huruf, angka, strip, dan underscore.',
            'img.max' => 'Ukuran foto maksimal 1 MB.',
        ]);

        $user->fill($request->except('img'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('img')) {
            if ($user->img) {
                Storage::disk('public')->delete($user->img);
            }
            $path = $request->file('img')->store('avatars', 'public');
            $user->img = $path;
        }

        $user->save();

        return Redirect::route('profile.settings')->with('status', 'profile-updated');
    }

    /**
     * Memperbarui kata sandi pengguna.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
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
