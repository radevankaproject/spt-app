<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FieldCoordinator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FieldCoordinatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'active'); // ✅ Default ke tab 'active'

        $query = FieldCoordinator::with(['user', 'agreements' => function ($query) {
            $query->where('status', 'active');
        }]);

        // ✅ LOGIKA TAB IS_ACTIVE
        if ($tab === 'active') {
            $query->whereHas('user', function ($q) {
                $q->where('is_active', true);
            });
        } elseif ($tab === 'inactive') {
            $query->whereHas('user', function ($q) {
                $q->where('is_active', false);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('position', 'like', '%'.$search.'%')
                    ->orWhere('id_card_number', 'like', '%'.$search.'%');
                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%');
                });
            });
        }

        $fieldCoordinators = $query->orderBy(
            User::select('name')->whereColumn('users.id', 'field_coordinators.user_id'), 'asc'
        )->paginate(10);

        // ✅ HITUNG TOTAL UNTUK BADGE
        $countAll = FieldCoordinator::count();
        $countActive = FieldCoordinator::whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->count();
        $countInactive = FieldCoordinator::whereHas('user', function ($q) {
            $q->where('is_active', false);
        })->count();

        return view('admin.field_coordinators.index', compact('fieldCoordinators', 'search', 'tab', 'countAll', 'countActive', 'countInactive'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        // ✅ PESAN VALIDASI BAHASA INDONESIA
        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'id_card_number.required' => 'Nomor KTP (NIK) wajib diisi.',
            'id_card_number.max' => 'Nomor KTP tidak boleh lebih dari 16 digit.',
            'id_card_number.unique' => 'Nomor KTP ini sudah terdaftar pada koordinator lain.',
            'phone_number.required' => 'Nomor Telepon/HP wajib diisi.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'position.required' => 'Posisi/Jabatan wajib diisi.',
            'img.image' => 'File profil harus berupa gambar.',
            'img.mimes' => 'Format foto profil harus jpeg, png, atau jpg.',
            'img.max' => 'Ukuran foto profil maksimal 5MB.',
            'id_card_img.image' => 'File KTP harus berupa gambar.',
            'id_card_img.mimes' => 'Format foto KTP harus jpeg, png, atau jpg.',
            'id_card_img.max' => 'Ukuran foto KTP maksimal 5MB.',
        ];

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'id_card_number' => 'required|string|max:16|unique:field_coordinators,id_card_number',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string',
            'position' => 'required|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'id_card_img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], $messages);

        DB::beginTransaction();
        try {
            $username = strtolower(str_replace(' ', '_', $validatedData['name'])).'_'.rand(100, 999);

            $user = User::create([
                'name' => $validatedData['name'],
                'username' => $username,
                'email' => $username.'@korlap-parkir.local',
                'password' => Hash::make('password'),
                'role' => 'field_coordinator',
            ]);

            // ✅ FIX: Simpan Foto Profil (Konsisten pakai Storage Public)
            if ($request->hasFile('img')) {
                try {
                    $profileImageName = time().'_usersCoordinator.'.$request->file('img')->extension();
                    $path = $request->file('img')->storeAs('uploads/users', $profileImageName, 'public');
                    $user->img = $path;
                    $user->save();
                } catch (\Exception $e) {
                    Log::error('FieldCoordinatorController@store: Error moving profile image: '.$e->getMessage());
                }
            }

            // ✅ FIX: Simpan Foto KTP (Konsisten pakai Storage Public)
            $idCardImagePath = null;
            if ($request->hasFile('id_card_img')) {
                try {
                    $idCardImageName = time().'_idcard.'.$request->file('id_card_img')->extension();
                    $idCardImagePath = $request->file('id_card_img')->storeAs('uploads/id_cards', $idCardImageName, 'public');
                } catch (\Exception $e) {
                    Log::error('FieldCoordinatorController@store: Error moving ID card image: '.$e->getMessage());
                }
            }

            FieldCoordinator::create([
                'user_id' => $user->id,
                'position' => $validatedData['position'],
                'address' => $validatedData['address'],
                'id_card_number' => $validatedData['id_card_number'],
                'id_card_img' => $idCardImagePath,
                'phone_number' => $validatedData['phone_number'],
            ]);

            DB::commit();

            return redirect()->route('admin.field-coordinators.index')
                ->with('success', 'Koordinator Lapangan berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating field coordinator: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data. Terjadi kesalahan internal.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, FieldCoordinator $fieldCoordinator)
    {
        $availableYears = $fieldCoordinator->agreements()
            ->selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        $selectedYear = $request->input('year', $availableYears->first());

        $agreementsInYear = $fieldCoordinator->agreements()
            ->whereYear('start_date', $selectedYear)
            ->with(['activeParkingLocations.roadSection'])
            ->withSum(['depositTransactions as total_deposit' => function ($q) {
                $q->where('is_validated', true);
            }], 'amount')
            ->orderBy('start_date', 'desc')
            ->get();

        $totalAgreementsCount = $agreementsInYear->count();
        $activeAgreements = $agreementsInYear->whereIn('status', ['active', 'pending_renewal']);
        $historyAgreements = $agreementsInYear->whereNotIn('status', ['active', 'pending_renewal']);

        $activeParkingLocationsCount = $activeAgreements->flatMap->activeParkingLocations->unique('id')->count();
        $totalValidatedDeposit = $agreementsInYear->sum('total_deposit');

        $fieldCoordinator->load('user');

        return view('admin.field_coordinators.show', compact(
            'fieldCoordinator',
            'availableYears',
            'selectedYear',
            'agreementsInYear',
            'activeAgreements',
            'historyAgreements',
            'activeParkingLocationsCount',
            'totalValidatedDeposit',
            'totalAgreementsCount'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FieldCoordinator $fieldCoordinator)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'id_card_number.required' => 'Nomor KTP (NIK) wajib diisi.',
            'id_card_number.max' => 'Nomor KTP tidak boleh lebih dari 16 digit.',
            'id_card_number.unique' => 'Nomor KTP ini sudah digunakan oleh koordinator lain.',
            'phone_number.required' => 'Nomor Telepon/HP wajib diisi.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'position.required' => 'Posisi/Jabatan wajib diisi.',
            'img.image' => 'File profil harus berupa gambar.',
            'img.mimes' => 'Format foto profil harus jpeg, png, atau jpg.',
            'img.max' => 'Ukuran foto profil maksimal 5MB.',
            'id_card_img.image' => 'File KTP harus berupa gambar.',
            'id_card_img.mimes' => 'Format foto KTP harus jpeg, png, atau jpg.',
            'id_card_img.max' => 'Ukuran foto KTP maksimal 5MB.',
        ];

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'id_card_number' => ['required', 'string', 'max:16', Rule::unique('field_coordinators')->ignore($fieldCoordinator->id)],
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string',
            'position' => 'required|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'id_card_img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], $messages);

        DB::beginTransaction();
        try {
            $user = $fieldCoordinator->user;
            if ($user) {
                $user->name = $validatedData['name'];

                // ✅ FIX UPDATE FOTO PROFIL: Hapus pakai Storage, simpan pakai storeAs
                if ($request->hasFile('img')) {
                    if ($user->img && Storage::disk('public')->exists($user->img)) {
                        Storage::disk('public')->delete($user->img);
                    }
                    $imagePath = $request->file('img')->storeAs('uploads/users', time().'_profile.'.$request->file('img')->extension(), 'public');
                    $user->img = $imagePath;
                }
                $user->save();
            }

            $coordinatorData = Arr::except($validatedData, ['name', 'img']);

            // ✅ FIX UPDATE FOTO KTP: Hapus pakai Storage, simpan pakai storeAs
            if ($request->hasFile('id_card_img')) {
                if ($fieldCoordinator->id_card_img && Storage::disk('public')->exists($fieldCoordinator->id_card_img)) {
                    Storage::disk('public')->delete($fieldCoordinator->id_card_img);
                }
                $idCardPath = $request->file('id_card_img')->storeAs('uploads/id_cards', time().'_idcard.'.$request->file('id_card_img')->extension(), 'public');
                $coordinatorData['id_card_img'] = $idCardPath;
            }

            $fieldCoordinator->update($coordinatorData);

            DB::commit();

            return redirect()->route('admin.field-coordinators.index')
                ->with('success', 'Data Koordinator Lapangan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating field coordinator: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data. Terjadi kesalahan internal.');
        }
    }

    public function toggleStatus(FieldCoordinator $fieldCoordinator)
    {
        $user = $fieldCoordinator->user;

        if ($user) {
            $user->is_active = ! $user->is_active; // Balikkan nilainya (true jadi false, dst)
            $user->save();

            $statusName = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()->back()->with('success', "Akses akun koordinator berhasil {$statusName}.");
        }

        return redirect()->back()->with('error', 'Data User tidak ditemukan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FieldCoordinator $fieldCoordinator)
    {
        // ✅ 1. GERBANG AUDIT (SMART DELETE LOGIC)
        // Cek apakah Korlap punya sejarah PKS (Aktif maupun Arsip/Expired)
        if ($fieldCoordinator->agreements()->count() > 0) {
            return redirect()->back()->with('error', 'TIDAK BISA DIHAPUS! Koordinator ini memiliki riwayat Kontrak (PKS) dan jejak Setoran. Demi integritas data dan Audit, profil ini harus dipertahankan secara permanen.');
        }

        // ✅ 2. EKSEKUSI HAPUS (Hanya jika belum pernah punya PKS)
        try {
            // Bersihkan foto profil dengan aman
            if ($fieldCoordinator->user && $fieldCoordinator->user->img) {
                if (Storage::disk('public')->exists($fieldCoordinator->user->img)) {
                    Storage::disk('public')->delete($fieldCoordinator->user->img);
                }
            }

            // Bersihkan KTP dengan aman
            if ($fieldCoordinator->id_card_img) {
                if (Storage::disk('public')->exists($fieldCoordinator->id_card_img)) {
                    Storage::disk('public')->delete($fieldCoordinator->id_card_img);
                }
            }

            // Hapus Korlap dulu, baru hapus User (agar relasi tidak error)
            $user = $fieldCoordinator->user;
            $fieldCoordinator->delete();
            if ($user) {
                $user->delete();
            }

        } catch (\Exception $e) {
            Log::error('FieldCoordinatorController@destroy: Error deleting Field Coordinator or user: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal menghapus Koordinator. Terjadi kesalahan internal.');
        }

        return redirect()->route('admin.field-coordinators.index')->with('success', 'Data Koordinator (yang belum memiliki riwayat) berhasil dihapus permanen.');
    }
}
