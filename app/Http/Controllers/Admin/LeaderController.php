<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use App\Models\LeaderHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LeaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'active'); // Default tab aktif

        $query = Leader::with('user')->withCount('agreements');

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
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('username', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            })->orWhere('employee_number', 'like', '%'.$search.'%');
        }

        $leaders = $query->latest()->paginate(10);

        // ✅ HITUNG TOTAL UNTUK BADGE
        $countAll = Leader::count();
        $countActive = Leader::whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->count();
        $countInactive = Leader::whereHas('user', function ($q) {
            $q->where('is_active', false);
        })->count();

        return view('admin.leaders.index', compact('leaders', 'search', 'tab', 'countAll', 'countActive', 'countInactive'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username|regex:/^[a-z0-9_-]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'nullable|numeric|digits_between:10,14',
            'password' => 'required|string|min:8|confirmed|not_regex:/\s/',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'employee_number' => 'required|string|max:18|unique:leaders,employee_number',
            'status_jabatan' => 'required|in:tetap,plt,plh',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'username.regex' => 'Username hanya boleh berisi huruf kecil, angka, strip, atau underscore.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'phone_number.numeric' => 'Nomor HP harus berupa angka.',
            'phone_number.digits_between' => 'Nomor HP harus antara 10 hingga 14 digit.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.not_regex' => 'Kata sandi tidak boleh mengandung spasi.',
            'img.image' => 'File harus berupa gambar.',
            'img.max' => 'Ukuran gambar maksimal 5MB.',
            'employee_number.required' => 'NIP wajib diisi.',
            'employee_number.unique' => 'NIP ini sudah terdaftar.',
            'status_jabatan.required' => 'Status jabatan wajib dipilih.',
            'start_date.required' => 'Tanggal mulai menjabat wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'phone_number' => $validatedData['phone_number'] ?? null,
                'password' => Hash::make($validatedData['password']),
                'role' => 'leader',
                'is_active' => true, // Default aktif
            ]);

            // ✅ LOGIKA EKSKLUSIF: Otomatis nonaktifkan semua Pimpinan lama karena ada Pimpinan baru
            User::where('role', 'leader')->where('id', '!=', $user->id)->update(['is_active' => false]);

            if ($request->hasFile('img')) {
                $imgaName = time().'_userLeader.'.$request->img->extension();
                $path = $request->file('img')->storeAs('uploads/users', $imgaName, 'public');
                $user->img = $path;
                $user->save();
            }

            Leader::create([
                'user_id' => $user->id,
                'employee_number' => $validatedData['employee_number'],
                'status_jabatan' => $validatedData['status_jabatan'],
                'phone_number' => $validatedData['phone_number'] ?? null,
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
            ]);

            DB::commit();

            return redirect()->route('admin.leaders.index')->with('success', 'Pimpinan baru berhasil ditambahkan dan otomatis menjadi Pimpinan Aktif!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LeaderController@store Error: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pimpinan. Terjadi kesalahan sistem.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Leader $leader)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($leader->user_id), 'regex:/^[a-z0-9_-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($leader->user_id)],
            'phone_number' => 'nullable|numeric|digits_between:10,14',
            'password' => 'nullable|string|min:8|confirmed|not_regex:/\s/',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'employee_number' => ['required', 'string', 'max:18', Rule::unique('leaders', 'employee_number')->ignore($leader->id)],
            'status_jabatan' => 'required|in:tetap,plt,plh',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'username.regex' => 'Username hanya boleh berisi huruf kecil, angka, strip, atau underscore.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'phone_number.numeric' => 'Nomor HP harus berupa angka.',
            'phone_number.digits_between' => 'Nomor HP harus antara 10 hingga 14 digit.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.not_regex' => 'Kata sandi tidak boleh mengandung spasi.',
            'img.image' => 'File harus berupa gambar.',
            'img.max' => 'Ukuran gambar maksimal 5MB.',
            'employee_number.required' => 'NIP wajib diisi.',
            'employee_number.unique' => 'NIP ini sudah terdaftar.',
            'status_jabatan.required' => 'Status jabatan wajib dipilih.',
            'start_date.required' => 'Tanggal mulai menjabat wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
        ]);

        DB::beginTransaction();
        try {
            $leader->user->name = $validatedData['name'];
            $leader->user->username = $validatedData['username'];
            $leader->user->email = $validatedData['email'];
            $leader->user->phone_number = $validatedData['phone_number'] ?? null;

            if ($request->filled('password')) {
                $leader->user->password = Hash::make($validatedData['password']);
            }

            if ($request->hasFile('img')) {
                if ($leader->user->img && Storage::disk('public')->exists($leader->user->img)) {
                    Storage::disk('public')->delete($leader->user->img);
                }
                $imageName = time().'_userLeader.'.$request->img->extension();
                $path = $request->file('img')->storeAs('uploads/users', $imageName, 'public');
                $leader->user->img = $path;
            }

            $leader->user->save();

            $leader->employee_number = $validatedData['employee_number'];
            $leader->status_jabatan = $validatedData['status_jabatan'];
            $leader->phone_number = $validatedData['phone_number'] ?? null;
            $leader->start_date = $validatedData['start_date'];
            $leader->end_date = $validatedData['end_date'];
            $leader->save();

            DB::commit();

            return redirect()->route('admin.leaders.index')->with('success', 'Data pimpinan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LeaderController@update Error: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pimpinan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Leader $leader)
    {
        // 1. Dapatkan daftar tahun langsung dari tabel Agreement tanpa memanggil relasi
        $availableYears = \App\Models\Agreement::where('leader_id', $leader->id)
            ->selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        // 2. Tangkap tahun yang dipilih...
        $selectedYear = $request->input('year', $availableYears->first());
        $search = $request->input('search');

        // 3. Ambil data PKS HANYA untuk tahun yang dipilih...
        $agreementsQuery = \App\Models\Agreement::where('leader_id', $leader->id)
            ->whereYear('start_date', $selectedYear)
            ->with(['fieldCoordinator.user']);

        if ($search) {
            $agreementsQuery->where(function ($q) use ($search) {
                $q->where('agreement_number', 'like', "%{$search}%")
                  ->orWhereHas('fieldCoordinator.user', function ($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 4. Kalkulasi Statistik
        $totalAgreementsCount = \App\Models\Agreement::where('leader_id', $leader->id)
            ->whereYear('start_date', $selectedYear)
            ->count();
            
        $activeAgreementsCount = \App\Models\Agreement::where('leader_id', $leader->id)
            ->whereYear('start_date', $selectedYear)
            ->whereIn('status', ['active', 'pending_renewal'])
            ->count();

        // 5. Paginate Data (Aktif duluan, lalu tanggal mulai)
        $agreementsInYear = $agreementsQuery
            ->orderByRaw("CASE WHEN status IN ('active', 'pending_renewal') THEN 0 ELSE 1 END")
            ->orderBy('start_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        // ✅ TAMBAHKAN RELASI 'histories' DI SINI
        $leader->load(['user', 'histories' => function ($q) {
            $q->orderBy('start_date', 'desc'); // Urutkan riwayat dari yang paling baru
        }]);

        return view('admin.leaders.show', compact(
            'leader',
            'availableYears',
            'selectedYear',
            'agreementsInYear',
            'totalAgreementsCount',
            'activeAgreementsCount',
            'search'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leader $leader)
    {
        // ✅ GERBANG AUDIT (SMART DELETE)
        if ($leader->agreements()->count() > 0) {
            return redirect()->back()->with('error', 'TIDAK BISA DIHAPUS! Pimpinan ini memiliki riwayat menandatangani PKS. Demi audit, data harus dipertahankan.');
        }

        try {
            if ($leader->user && $leader->user->img) {
                if (Storage::disk('public')->exists($leader->user->img)) {
                    Storage::disk('public')->delete($leader->user->img);
                }
            }
            $user = $leader->user;
            $leader->delete();
            if ($user) {
                $user->delete();
            }

        } catch (\Exception $e) {
            Log::error('LeaderController@destroy Error: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal menghapus pimpinan.');
        }

        return redirect()->route('admin.leaders.index')->with('success', 'Data pimpinan berhasil dihapus permanen.');
    }

    /**
     * Mengubah status is_active Pimpinan.
     * Jika diaktifkan, SEMUA pimpinan lain akan otomatis dinonaktifkan.
     */
    public function toggleStatus(Request $request, Leader $leader)
    {
        $user = $leader->user;
        $isDeactivating = $user->is_active;

        DB::beginTransaction();
        try {
            if ($isDeactivating) {
                // ✅ 1. PROSES PURNA TUGAS (NONAKTIF)
                $request->validate(['end_date' => 'required|date']);

                // Tutup masa jabatannya saat ini
                $leader->update(['end_date' => $request->end_date]);
                $user->update(['is_active' => false]);

                $msg = 'Pimpinan berhasil dipurna-tugaskan (Nonaktif).';
            } else {
                // ✅ 2. PROSES MENJABAT KEMBALI (AKTIF)
                $request->validate([
                    'start_date' => 'required|date',
                    'status_jabatan' => 'required|in:tetap,plt,plh',
                ]);

                // A. Arsipkan masa jabatan lama ke tabel history (Mesin Waktu)
                LeaderHistory::create([
                    'leader_id' => $leader->id,
                    'status_jabatan' => $leader->status_jabatan ?? 'tetap',
                    'start_date' => $leader->start_date,
                    'end_date' => $leader->end_date ?? now()->toDateString(),
                ]);

                // B. Timpa data di tabel leader dengan masa jabatan BARU
                $leader->update([
                    'status_jabatan' => $request->status_jabatan,
                    'start_date' => $request->start_date,
                    'end_date' => null, // Buka kembali masa jabatan
                ]);

                // C. Lengserkan pimpinan lain yang sedang aktif
                User::where('role', 'leader')->where('id', '!=', $user->id)->update(['is_active' => false]);

                $user->update(['is_active' => true]);
                $msg = 'Pimpinan berhasil menjabat kembali sebagai '.strtoupper($request->status_jabatan).' !';
            }

            DB::commit();

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Toggle Status Error: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal memproses status jabatan.');
        }
    }
    public function extend(Request $request, Leader $leader)
    {
        $request->validate([
            'status_jabatan' => 'required|in:tetap,plt,plh',
            'start_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Arsipkan jabatan lama ke LeaderHistory
            $oldEndDate = $request->end_date_old 
                ? \Carbon\Carbon::parse($request->end_date_old)->format('Y-m-d')
                : \Carbon\Carbon::parse($request->start_date)->subDay()->format('Y-m-d');

            LeaderHistory::create([
                'leader_id' => $leader->id,
                'status_jabatan' => $leader->status_jabatan,
                'start_date' => $leader->start_date,
                'end_date' => $oldEndDate,
            ]);

            // Update data pimpinan dengan masa jabatan baru
            $leader->update([
                'status_jabatan' => $request->status_jabatan,
                'start_date' => $request->start_date,
                'end_date' => null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Jabatan Pimpinan berhasil diperpanjang. Riwayat jabatan sebelumnya telah diarsipkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Leader Extend Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperpanjang jabatan.');
        }
    }
}
