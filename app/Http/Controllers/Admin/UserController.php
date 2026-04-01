<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil query pencarian dari request
        $search = $request->input('search');

        // Mulai query User
        $query = User::query();

        $query->whereIn('role', ['admin', 'staff_keu', 'staff_pks']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Terapkan filter pencarian jika ada
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%'); // Bisa juga mencari berdasarkan role
            });
        }

        // Ambil data user dengan paginasi (misal 10 per halaman)
        // Pastikan untuk mengurutkan data agar konsisten
        $users = $query->latest()->paginate(10);

        // Kirimkan query pencarian ke view agar input search tetap terisi
        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');
        return view('admin.users.create');
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
            'password' => 'required|string|min:8|confirmed|not_regex:/\s/',
            'role' => 'required|string|in:admin,leader,field_coordinator,staff_keu,staff_pks', // Tambahkan validasi role
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:300',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        if ($request->hasFile('img')) {
            // 1. Tentukan nama file kustom Anda.
            $imageName = time() . '_users.' . $request->img->extension();

            // 2. Simpan file dengan nama kustom ke disk 'public'.
            // Ini akan menyimpan file di: storage/app/public/uploads/users/
            // Metode ini akan mengembalikan path lengkapnya, contoh: "uploads/users/1662...jpg"
            $path = $request->file('img')->storeAs('uploads/users', $imageName, 'public');

            // 3. Simpan path yang dikembalikan ke database.
            $user->img = $path;
            $user->save();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna ' . $user->name . ' berhasil ditambahkan!')
            ->with('user_name', $user->name); // Kirim nama user untuk popup
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
                'regex:/^[a-z0-9_-]+$/',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed|not_regex:/\s/',
            'role' => 'required|string|in:admin,leader,field_coordinator,staff_pks,staff_keu',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:300',
        ]);

        $user->name = $validatedData['name'];
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        if ($request->hasFile('img')) {
            try {
                //hapus gambar lama jika ada
                if ($user->img) {
                    Storage::disk('public')->delete($user->img);
                }

                $imageName = time() . '_users.' . $request->img->extension();
                $path = $request->file('img')->storeAs('uploads/users', $imageName, 'public');
                $user->img = $path;
            } catch (\Exception $e) {
                Log::error('UserController@update: Error handling user image upload: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengunggah gambar.');
            }
        }
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna ' . $user->name . ' berhasil diperbarui!')
            ->with('user_name', $user->name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Hapus gambar profil jika ada
            if ($user->img) {
                Storage::disk('public')->delete($user->img);
            }
            $user->delete(); // Soft delete user
        } catch (\Exception $e) {
            Log::error('UserController@destroy: Error deleting user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}
