<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LeaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // Tambahkan Request $request
    {
        // Ambil query pencarian dari request
        $search = $request->input('search');

        // Mulai query Leader dengan eager loading user
        $query = Leader::with('user');

        // Terapkan filter pencarian jika ada
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })->orWhere('employee_number', 'like', '%' . $search . '%')
                ->orWhere('position', 'like', '%' . $search . '%'); // Jika ada kolom position di Leader
        }

        // Ambil data leader dengan paginasi (misal 10 per halaman)
        // Pastikan untuk mengurutkan data agar konsisten
        $leaders = $query->latest()->paginate(10);

        // Kirimkan query pencarian ke view agar input search tetap terisi
        return view('admin.leaders.index', compact('leaders', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.leaders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('LeaderController@store: Request received.', $request->all());

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username|regex:/^[a-z0-9_-]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed|not_regex:/\s/',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:300',
            'employee_number' => 'required|string|max:18|unique:leaders,employee_number',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Log::info('LeaderController@store: Validation successful.', $validatedData);

        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'leader',
        ]);

        Log::info('LeaderController@store: User created with ID ' . $user->id);

        if ($request->hasFile('img')) {
            $imgaName = time() . '_userLeader.' . $request->img->extension();
            $path = $request->file('img')->storeAs('uploads/users', $imgaName, 'public');
            $user->img = $path;
            $user->save();
        }
        $leader = Leader::create([
            'user_id' => $user->id,
            'employee_number' => $validatedData['employee_number'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);

        Log::info('LeaderController@store: Leader created with ID ' . $leader->id);

        return redirect()->route('admin.leaders.index')
            ->with('success', 'Pimpinan baru berhasil ditambahkan!')
            ->with('leader_name', $user->name);
    }

    /**
     * Display the specified resource.
     */
    public function show(Leader $leader)
    {
        return view('admin.leaders.show', compact('leader'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Leader $leader)
    {
        return view('admin.leaders.edit', compact('leader'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Leader $leader)
    {
        Log::info('LeaderController@update: Request received for leader ID ' . $leader->id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($leader->user_id),
                'regex:/^[a-z0-9_-]+$/',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($leader->user_id),
            ],
            'password' => 'nullable|string|min:8|confirmed|not_regex:/\s/',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:300',
            'employee_number' => [
                'required',
                'string',
                'max:18',
                Rule::unique('leaders', 'employee_number')->ignore($leader->id),
            ],
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Log::info('LeaderController@update: Validation successful for leader ID ' . $leader->id);

        $leader->user->name = $validatedData['name'];
        $leader->user->username = $validatedData['username'];
        $leader->user->email = $validatedData['email'];
        if ($request->filled('password')) {
            $leader->user->password = Hash::make($validatedData['password']);
        }

        if ($request->hasFile('img')) {
            try {
                if ($leader->user->img) {
                    Storage::disk('public')->delete($leader->user->img);
                }
                $imageName = time() . '_userLeader.' . $request->img->extension();
                $path = $request->file('img')->storeAs('uploads/users', $imageName, 'public');
                $leader->user->img = $path;
            } catch (\Exception $e) {
                Log::error('LeaderController@update: Error moving new image: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah gambar: ' . $e->getMessage());
            }
        }

        $leader->user->save();

        $leader->employee_number = $validatedData['employee_number'];
        $leader->start_date = $validatedData['start_date'];
        $leader->end_date = $validatedData['end_date'];
        $leader->save();

        Log::info('LeaderController@update: Leader data updated for ID ' . $leader->id);

        return redirect()->route('admin.leaders.index')
            ->with('success', 'Data pimpinan berhasil diperbarui!')
            ->with('leader_name', $leader->user->name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leader $leader)
    {
        Log::info('LeaderController@destroy: Attempting to delete leader ID ' . $leader->id);

        try {
            if ($leader->user->img) {
                Storage::disk('public')->delete($leader->user->image);
                Log::info('LeaderController@destroy: User image deleted: ' . $leader->user->img);
            }
            $leader->user->delete();
            $leader->delete();
            Log::info('LeaderController@destroy: Leader and associated user soft deleted for ID ' . $leader->id);
        } catch (\Exception $e) {
            Log::error('LeaderController@destroy: Error deleting leader or user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus pimpinan: ' . $e->getMessage());
        }

        return redirect()->route('admin.leaders.index')->with('success', 'Pimpinan berhasil dihapus!');
    }
}
