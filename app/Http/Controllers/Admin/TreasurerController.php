<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositTransaction;
use App\Models\Treasurer;
use App\Models\TreasurerHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TreasurerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'active');

        $query = Treasurer::with('user');

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

        $treasurers = $query->latest()->paginate(10);
        $countAll = Treasurer::count();
        $countActive = Treasurer::whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->count();
        $countInactive = Treasurer::whereHas('user', function ($q) {
            $q->where('is_active', false);
        })->count();

        return view('admin.treasurers.index', compact('treasurers', 'search', 'tab', 'countAll', 'countActive', 'countInactive'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username|regex:/^[a-z0-9_-]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'nullable|numeric|digits_between:10,14',
            'password' => 'required|string|min:8|confirmed|not_regex:/\s/',
            'img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'employee_number' => 'required|string|max:18|unique:treasurers,employee_number',
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
                'role' => 'treasurer', // ✅ ROLE BENDAHARA
                'is_active' => true,
            ]);

            // Otomatis lengserkan Bendahara lain
            User::where('role', 'treasurer')->where('id', '!=', $user->id)->update(['is_active' => false]);

            if ($request->hasFile('img')) {
                $imageName = time().'_userBendahara.'.$request->img->extension();
                $path = $request->file('img')->storeAs('uploads/users', $imageName, 'public');
                $user->img = $path;
                $user->save();
            }

            Treasurer::create([
                'user_id' => $user->id,
                'employee_number' => $validatedData['employee_number'],
                'phone_number' => $validatedData['phone_number'] ?? null,
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
            ]);

            DB::commit();

            return redirect()->route('admin.treasurers.index')->with('success', 'Bendahara baru berhasil ditambahkan dan otomatis menjadi Bendahara Aktif!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TreasurerController@store Error: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan Bendahara.');
        }
    }

    public function show(Treasurer $treasurer)
    {
        $treasurer->load(['user', 'histories']);

        // ✅ AMBIL DATA SETORAN YANG BERHUBUNGAN DENGAN BENDAHARA INI
        $deposits = DepositTransaction::with(['agreement.fieldCoordinator.user'])
            ->where('treasurer_id', $treasurer->id)
            ->latest('deposit_date') // Urutkan dari setoran terbaru
            ->paginate(10);

        // ✅ HITUNG TOTAL NOMINAL YANG SUDAH DIVALIDASI BENDAHARA INI (Opsional untuk UI Premium)
        $totalValidatedAmount = DepositTransaction::where('treasurer_id', $treasurer->id)
            ->where('is_validated', true)
            ->sum('amount');

        return view('admin.treasurers.show', compact('treasurer', 'deposits', 'totalValidatedAmount'));
    }

    public function update(Request $request, Treasurer $treasurer)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($treasurer->user_id), 'regex:/^[a-z0-9_-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($treasurer->user_id)],
            'phone_number' => 'nullable|numeric|digits_between:10,14',
            'password' => 'nullable|string|min:8|confirmed|not_regex:/\s/',
            'img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'employee_number' => ['required', 'string', 'max:18', Rule::unique('treasurers', 'employee_number')->ignore($treasurer->id)],
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
            'start_date.required' => 'Tanggal mulai menjabat wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
        ]);

        DB::beginTransaction();
        try {
            $treasurer->user->name = $validatedData['name'];
            $treasurer->user->username = $validatedData['username'];
            $treasurer->user->email = $validatedData['email'];
            $treasurer->user->phone_number = $validatedData['phone_number'] ?? null;

            if ($request->filled('password')) {
                $treasurer->user->password = Hash::make($validatedData['password']);
            }

            if ($request->hasFile('img')) {
                if ($treasurer->user->img && Storage::disk('public')->exists($treasurer->user->img)) {
                    Storage::disk('public')->delete($treasurer->user->img);
                }
                $imageName = time().'_userBendahara.'.$request->img->extension();
                $treasurer->user->img = $request->file('img')->storeAs('uploads/users', $imageName, 'public');
            }

            $treasurer->user->save();

            $treasurer->update([
                'employee_number' => $validatedData['employee_number'],
                'phone_number' => $validatedData['phone_number'] ?? null,
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
            ]);

            DB::commit();

            return redirect()->route('admin.treasurers.index')->with('success', 'Data Bendahara berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TreasurerController@update Error: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy(Treasurer $treasurer)
    {
        // ✅ GERBANG AUDIT: Cek apakah sudah ada sejarah jabatan. Nanti bisa ditambah cek relasi validasi transaksi setoran.
        if ($treasurer->histories()->count() > 0) {
            return redirect()->back()->with('error', 'TIDAK BISA DIHAPUS! Bendahara ini memiliki riwayat jabatan. Demi audit, data harus dipertahankan.');
        }

        try {
            if ($treasurer->user && $treasurer->user->img && Storage::disk('public')->exists($treasurer->user->img)) {
                Storage::disk('public')->delete($treasurer->user->img);
            }
            $user = $treasurer->user;
            $treasurer->delete();
            if ($user) {
                $user->delete();
            }
        } catch (\Exception $e) {
            Log::error('TreasurerController@destroy Error: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal menghapus Bendahara.');
        }

        return redirect()->route('admin.treasurers.index')->with('success', 'Data Bendahara berhasil dihapus permanen.');
    }

    public function toggleStatus(Request $request, Treasurer $treasurer)
    {
        $user = $treasurer->user;
        $isDeactivating = $user->is_active;

        DB::beginTransaction();
        try {
            if ($isDeactivating) {
                $request->validate(['end_date' => 'required|date']);
                $treasurer->update(['end_date' => $request->end_date]);
                $user->update(['is_active' => false]);
                $msg = 'Bendahara berhasil dipurna-tugaskan (Nonaktif).';
            } else {
                $request->validate([
                    'start_date' => 'required|date',
                    'status_jabatan' => 'required|in:tetap,plt,plh',
                ]);

                TreasurerHistory::create([
                    'treasurer_id' => $treasurer->id,
                    'status_jabatan' => $treasurer->status_jabatan ?? 'tetap',
                    'start_date' => $treasurer->start_date,
                    'end_date' => $treasurer->end_date ?? now()->toDateString(),
                ]);

                $treasurer->update([
                    'status_jabatan' => $request->status_jabatan,
                    'start_date' => $request->start_date,
                    'end_date' => null,
                ]);

                // Lengserkan bendahara aktif lainnya
                User::where('role', 'treasurer')->where('id', '!=', $user->id)->update(['is_active' => false]);
                $user->update(['is_active' => true]);

                $msg = 'Bendahara berhasil menjabat kembali sebagai '.strtoupper($request->status_jabatan).' !';
            }
            DB::commit();

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Toggle Status Error: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal memproses status jabatan.');
        }
    }
}
