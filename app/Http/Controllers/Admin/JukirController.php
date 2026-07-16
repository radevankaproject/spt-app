<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jukir;
use App\Models\JukirHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class JukirController extends Controller
{
    public function index(Request $request)
    {
        $jukirs = Jukir::with(['parkingLocation.roadSection', 'parkingLocation.agreements' => function($q) {
            $q->wherePivot('status', 'active')->with('fieldCoordinator.user');
        }, 'violations'])->latest()->get();
        $parkingLocations = \App\Models\ParkingLocation::with(['roadSection', 'agreements' => function($q) {
            $q->wherePivot('status', 'active')->with('fieldCoordinator.user');
        }])->orderBy('name')->get();
        
        $roadSections = \App\Models\RoadSection::orderBy('name')->get();

        $maxId = Jukir::max('id_jukir');
        $nextId = $maxId ? ((int) $maxId) + 1 : 1;
        $nextIdJukir = str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('admin.jukirs.index', compact('jukirs', 'parkingLocations', 'roadSections', 'nextIdJukir'));
    }

    public function show(Jukir $jukir)
    {
        $jukir->load([
            'parkingLocation.roadSection', 
            'parkingLocation.agreements.fieldCoordinator.user',
            'histories.user', 
            'violations.user'
        ]);

        $locationNames = \App\Models\ParkingLocation::pluck('name', 'id')->toArray();

        return view('admin.jukirs.show', compact('jukir', 'locationNames'));
    }

    public function store(Request $request)
    {
        $rules = [
            'id_jukir' => 'required|string|max:255|unique:jukirs,id_jukir',
            'nama_jukir' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'parking_location_id' => 'nullable|exists:parking_locations,id',
            'no_ktp' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'image_ktp' => 'nullable|image|max:2048',
            'kta_type' => 'nullable|in:baru,perpanjangan',
            'kta_start_date' => 'nullable|date',
        ];

        $messages = [
            'id_jukir.required' => 'ID Jukir wajib diisi.',
            'id_jukir.unique' => 'ID Jukir sudah digunakan.',
            'nama_jukir.required' => 'Nama jukir wajib diisi.',
            'parking_location_id.required' => 'Titik parkir wajib dipilih.',
            'parking_location_id.exists' => 'Titik parkir tidak valid.',
            'image.image' => 'File foto profil harus berupa gambar.',
            'image.max' => 'Ukuran foto profil maksimal 2MB.',
            'image_ktp.image' => 'File foto KTP harus berupa gambar.',
            'image_ktp.max' => 'Ukuran foto KTP maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time().'_jukir.'.$request->image->extension();
            $imagePath = $request->file('image')->storeAs('uploads/jukir_images', $imageName, 'public');
        }

        $imageKtpPath = null;
        if ($request->hasFile('image_ktp')) {
            $ktpName = time().'_jukir_ktp.'.$request->image_ktp->extension();
            $imageKtpPath = $request->file('image_ktp')->storeAs('uploads/jukir_ktp_images', $ktpName, 'public');
        }

        $ktaEndDate = null;
        if ($request->parking_location_id && $request->kta_start_date) {
            $ktaEndDate = Carbon::parse($request->kta_start_date)->addMonths(3)->format('Y-m-d');
        }

        $ktaType = $request->parking_location_id ? $request->kta_type : null;
        $ktaStartDate = $request->parking_location_id ? $request->kta_start_date : null;

        $jukir = Jukir::create([
            'id_jukir' => $request->id_jukir,
            'nama_jukir' => $request->nama_jukir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'parking_location_id' => $request->parking_location_id,
            'no_ktp' => $request->no_ktp,
            'phone_number' => $request->phone_number,
            'image' => $imagePath,
            'image_ktp' => $imageKtpPath,
            'is_active' => $request->has('is_active'),
            'kta_type' => $ktaType,
            'kta_start_date' => $ktaStartDate,
            'kta_end_date' => $ktaEndDate,
        ]);

        JukirHistory::create([
            'jukir_id' => $jukir->id,
            'user_id' => Auth::id(),
            'parking_location_id' => $jukir->parking_location_id,
            'action' => 'Create',
            'description' => 'Mendaftarkan data jukir baru.',
            'new_values' => $jukir->toArray(),
        ]);

        return redirect()->route('admin.jukirs.index')->with('success', 'Data Jukir berhasil ditambahkan.');
    }

    public function update(Request $request, Jukir $jukir)
    {
        $rules = [
            'nama_jukir' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'parking_location_id' => 'nullable|exists:parking_locations,id',
            'no_ktp' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'image_ktp' => 'nullable|image|max:2048',
            'kta_type' => 'nullable|in:baru,perpanjangan',
            'kta_start_date' => 'nullable|date',
        ];

        $messages = [
            'nama_jukir.required' => 'Nama jukir wajib diisi.',
            'parking_location_id.required' => 'Titik parkir wajib dipilih.',
            'parking_location_id.exists' => 'Titik parkir tidak valid.',
            'image.image' => 'File foto profil harus berupa gambar.',
            'image.max' => 'Ukuran foto profil maksimal 2MB.',
            'image_ktp.image' => 'File foto KTP harus berupa gambar.',
            'image_ktp.max' => 'Ukuran foto KTP maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        $imagePath = $jukir->image;
        if ($request->hasFile('image')) {
            if ($jukir->image) {
                Storage::disk('public')->delete($jukir->image);
            }
            $imageName = time().'_jukir.'.$request->image->extension();
            $imagePath = $request->file('image')->storeAs('uploads/jukir_images', $imageName, 'public');
        }

        $imageKtpPath = $jukir->image_ktp;
        if ($request->hasFile('image_ktp')) {
            if ($jukir->image_ktp) {
                Storage::disk('public')->delete($jukir->image_ktp);
            }
            $ktpName = time().'_jukir_ktp.'.$request->image_ktp->extension();
            $imageKtpPath = $request->file('image_ktp')->storeAs('uploads/jukir_ktp_images', $ktpName, 'public');
        }

        $ktaType = $request->parking_location_id ? $request->kta_type : null;
        $ktaStartDate = $request->parking_location_id ? $request->kta_start_date : null;
        
        $ktaEndDate = $request->parking_location_id ? $jukir->kta_end_date : null;
        if ($request->parking_location_id && $request->has('kta_start_date') && $request->kta_start_date != $jukir->kta_start_date) {
            $ktaEndDate = $request->kta_start_date ? Carbon::parse($request->kta_start_date)->addMonths(3)->format('Y-m-d') : null;
        }

        $oldValues = $jukir->getOriginal();

        $jukir->update([
            'nama_jukir' => $request->nama_jukir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'parking_location_id' => $request->parking_location_id,
            'no_ktp' => $request->no_ktp,
            'phone_number' => $request->phone_number,
            'image' => $imagePath,
            'image_ktp' => $imageKtpPath,
            'is_active' => $request->has('is_active'),
            'kta_type' => $ktaType,
            'kta_start_date' => $ktaStartDate,
            'kta_end_date' => $ktaEndDate,
        ]);

        $changes = $jukir->getChanges();
        if (!empty($changes)) {
            $description = 'Mengupdate data jukir.';
            if (isset($changes['parking_location_id'])) {
                $description .= ' Lokasi parkir diubah.';
            }

            JukirHistory::create([
                'jukir_id' => $jukir->id,
                'user_id' => Auth::id(),
                'parking_location_id' => $jukir->parking_location_id,
                'action' => 'Update',
                'description' => $description,
                'old_values' => array_intersect_key($oldValues, $changes),
                'new_values' => $changes,
            ]);
        }

        return redirect()->back()->with('success', 'Data Jukir berhasil diupdate.');
    }

    public function destroy(Jukir $jukir)
    {
        if ($jukir->image) {
            Storage::disk('public')->delete($jukir->image);
        }
        if ($jukir->image_ktp) {
            Storage::disk('public')->delete($jukir->image_ktp);
        }
        $jukir->delete();

        return redirect()->route('admin.jukirs.index')->with('success', 'Data Jukir berhasil dihapus.');
    }

    public function printKta(Jukir $jukir)
    {
        $jukir->load(['parkingLocation.roadSection', 'parkingLocation.agreements' => function ($q) {
            $q->wherePivot('status', 'active')->with(['leader.user']);
        }]);

        // 1. Try from jukir's parking location active agreement
        $activeLeader = null;
        if ($jukir->parkingLocation && $jukir->parkingLocation->agreements->isNotEmpty()) {
            $activeLeader = $jukir->parkingLocation->agreements->first()->leader;
        }

        // 2. Fallback: ambil leader aktif dari sistem (untuk tanda tangan KTA)
        if (!$activeLeader) {
            $activeLeader = \App\Models\Leader::with('user')->first();
        }

        // Generate QR code (PNG format, base64 encoded) pointing to public complaint page
        $complaintUrl = route('public.jukir.complaint.create', $jukir->id_jukir);
        $qrCode = base64_encode(QrCode::format('png')->size(200)->margin(0)->generate($complaintUrl));

        return view('admin.jukirs.kta_print', compact('jukir', 'activeLeader', 'qrCode'));
    }
}
