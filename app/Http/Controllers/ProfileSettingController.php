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
use App\Models\FieldCoordinator;
use App\Models\RoadSection;
use App\Models\ParkingLocationHistory;
use App\Models\AgreementPdfHistory;
use App\Models\DepositTransaction;
use App\Models\Agreement;
use App\Models\Treasurer;
use App\Models\Leader;
use Illuminate\View\View;

class ProfileSettingController extends Controller
{
    /**
     * Menampilkan halaman Profil Saya beserta statistiknya.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $stats = [];
        $recentActivities = collect();

        if (in_array($user->role, ['admin', 'staff_pks'])) {
            $stats['korlapCount'] = FieldCoordinator::where('last_updated_by', $user->id)->count();
            $stats['roadSectionCount'] = RoadSection::where('last_updated_by', $user->id)->count();
            $stats['agreementPdfCount'] = AgreementPdfHistory::where('generated_by_user_id', $user->id)->count();

            // Parking location histories: "tampilkan data lokasi mana saja yang diubah oleh user"
            $recentActivities = ParkingLocationHistory::with('parkingLocation.roadSection')
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();
        } elseif ($user->role === 'staff_keu') {
            $stats['validatedDepositsCount'] = DepositTransaction::where('validated_by_user_id', $user->id)->count();
            $stats['validatedDepositsAmount'] = DepositTransaction::where('validated_by_user_id', $user->id)->sum('amount');
            
            $recentActivities = DepositTransaction::with('agreement.parkingLocations.roadSection')
                ->where('validated_by_user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();
        } elseif ($user->role === 'treasurer') {
            $treasurer = Treasurer::where('user_id', $user->id)->first();
            if ($treasurer) {
                $query = DepositTransaction::where('payment_date', '>=', $treasurer->start_date);
                if ($treasurer->end_date) {
                    $query->where('payment_date', '<=', $treasurer->end_date);
                }
                $query->where('is_validated', true);
                
                $stats['termDepositsCount'] = $query->count();
                $stats['termDepositsAmount'] = $query->sum('amount');
            } else {
                $stats['termDepositsCount'] = 0;
                $stats['termDepositsAmount'] = 0;
            }
        } elseif ($user->role === 'leader') {
            $leader = Leader::where('user_id', $user->id)->first();
            if ($leader) {
                $stats['signedAgreementsCount'] = Agreement::where('leader_id', $leader->id)->count();
            } else {
                $stats['signedAgreementsCount'] = 0;
            }
        }

        // Relasi default untuk tampilan avatar dll
        if ($user->role === 'field_coordinator') {
            $user->load('fieldCoordinator');
        } elseif ($user->role === 'leader') {
            $user->load('leader');
        } elseif (in_array($user->role, ['treasurer', 'staff_keu'])) {
            if (method_exists($user, 'treasurer')) {
                $user->load('treasurer');
            }
        }

        return view('profile.index', compact('user', 'stats', 'recentActivities'));
    }

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
        } elseif ($user->role === 'treasurer' || $user->role === 'staff_keu') {
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
            'employee_number' => ['nullable', 'string', 'max:50'],
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
