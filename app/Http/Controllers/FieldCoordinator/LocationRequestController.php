<?php

namespace App\Http\Controllers\FieldCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\LocationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // ✅ WAJIB ADA UNTUK STR::SLUG & STR::RANDOM

class LocationRequestController extends Controller
{
    private $customMessages = [
        'agreement_id.required' => 'Data PKS tidak ditemukan, silakan muat ulang halaman.',
        'request_type.required' => 'Jenis pengajuan wajib dipilih (Penambahan/Pencabutan).',
        'reason.required' => 'Alasan pengajuan wajib diisi.',
        'road_section_name.required' => 'Nama ruas jalan wajib diisi.',
        'name.required' => 'Nama titik lokasi wajib diisi.',
        'offered_daily_deposit.required' => 'Penawaran setoran harian wajib diisi.',
        'offered_daily_deposit.numeric' => 'Setoran harian harus berupa angka.',
        'offered_daily_deposit.min' => 'Setoran harian tidak boleh kurang dari 0.',
        'image.image' => 'File harus berupa gambar (JPG, PNG, dll).',
        'image.max' => 'Ukuran gambar maksimal 5MB.',
        'proposal_document.mimes' => 'Dokumen proposal harus berformat PDF, DOC, atau DOCX.',
        'proposal_document.max' => 'Ukuran dokumen maksimal 5MB.',
        'parking_location_id.required' => 'Anda wajib memilih titik parkir yang ingin dicabut.',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();

        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $requests = LocationRequest::with(['parkingLocation.roadSection'])
            ->whereHas('agreement.fieldCoordinator', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('road_section_name', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('parkingLocation', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhereHas('roadSection', function ($q3) use ($search) {
                                    $q3->where('name', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->get();

        return view('field_coordinator.location_requests.index', compact('requests', 'search', 'startDate', 'endDate'));
    }

    public function create()
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $user = Auth::user();

        $activeAgreement = Agreement::whereHas('fieldCoordinator', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->where('status', 'active')
            ->first();

        if (! $activeAgreement) {
            return redirect()->back()->with('error', 'Anda tidak memiliki PKS yang aktif saat ini.');
        }

        $activeLocations = $activeAgreement->activeParkingLocations()->with('roadSection')->get();

        return view('field_coordinator.location_requests.create', compact('activeAgreement', 'activeLocations'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $request->validate([
            'agreement_id' => 'required|exists:agreements,id',
            'request_type' => 'required|in:add,remove',
            'reason' => 'required|string',
        ], $this->customMessages);

        $data = $request->except(['image', 'proposal_document', 'koordinat_gabungan']);
        $data['status'] = 'pending';

        if ($request->request_type === 'add') {
            $request->validate([
                'road_section_name' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'offered_daily_deposit' => 'required|numeric|min:0',
                'image' => 'nullable|image|max:5120',
                'proposal_document' => 'nullable|mimes:pdf,doc,docx|max:5120',
            ], $this->customMessages);

            if ($request->filled('koordinat_gabungan')) {
                $coords = explode(',', $request->koordinat_gabungan);
                if (count($coords) >= 2) {
                    $data['latitude'] = trim($coords[0]);
                    $data['longitude'] = trim($coords[1]);
                }
            } else {
                $data['latitude'] = $request->latitude;
                $data['longitude'] = $request->longitude;
            }

            // ✅ LOGIKA PENYIMPANAN MENIRU PARKING LOCATION (AMANTI BADAI LAN)
            $safeName = Str::slug($request->name);
            $randomNum = mt_rand(100, 999);

            if ($request->hasFile('image')) {
                $imageName = time().'_'.Str::random(8).'.'.$request->image->extension();
                // Simpan di folder yang sama dengan master data biar rapi
                $data['image'] = $request->file('image')->storeAs('uploads/location_requests/images', $imageName, 'public');
            }

            if ($request->hasFile('proposal_document')) {
                $proposalName = "pengajuan_{$safeName}_{$randomNum}.".$request->proposal_document->extension();
                $data['proposal_document'] = $request->file('proposal_document')->storeAs('uploads/location_requests/proposals', $proposalName, 'public');
            }
        } else {
            $request->validate([
                'parking_location_id' => 'required|exists:parking_locations,id',
            ], $this->customMessages);
        }

        LocationRequest::create($data);

        return redirect()->route('field_coordinator.location-requests.index')
            ->with('success', 'Pengajuan berhasil dikirim dan sedang menunggu review dari Dinas.');
    }

    public function show(LocationRequest $locationRequest)
    {
        $user = Auth::user();
        $locationRequest->load(['agreement.fieldCoordinator.user', 'parkingLocation.roadSection', 'review.reviewer']);

        if ($locationRequest->agreement->fieldCoordinator->user_id !== $user->id) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk melihat pengajuan ini.');
        }

        return view('field_coordinator.location_requests.show', compact('locationRequest'));
    }

    public function edit(LocationRequest $locationRequest)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        if ($locationRequest->agreement->fieldCoordinator->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin.');
        }

        if ($locationRequest->status !== 'pending') {
            return redirect()->route('field_coordinator.location-requests.index')
                ->with('error', 'Hanya pengajuan berstatus PENDING yang dapat diubah.');
        }

        $activeAgreement = $locationRequest->agreement;
        $activeLocations = $activeAgreement->activeParkingLocations()->with('roadSection')->get();

        return view('field_coordinator.location_requests.edit', compact('locationRequest', 'activeAgreement', 'activeLocations'));
    }

    public function update(Request $request, LocationRequest $locationRequest)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        if ($locationRequest->agreement->fieldCoordinator->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin.');
        }

        if ($locationRequest->status !== 'pending') {
            return redirect()->route('field_coordinator.location-requests.index')
                ->with('error', 'Data sudah diproses, tidak bisa diubah lagi.');
        }

        $request->validate([
            'request_type' => 'required|in:add,remove',
            'reason' => 'required|string',
        ], $this->customMessages);

        $data = $request->only(['request_type', 'reason']);

        if ($request->request_type === 'add') {
            $request->validate([
                'road_section_name' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'offered_daily_deposit' => 'required|numeric|min:0',
                'image' => 'nullable|image|max:5120',
                'proposal_document' => 'nullable|mimes:pdf,doc,docx|max:5120',
            ], $this->customMessages);

            $data['road_section_name'] = $request->road_section_name;
            $data['name'] = $request->name;
            $data['offered_daily_deposit'] = $request->offered_daily_deposit;
            $data['parking_location_id'] = null;

            if ($request->filled('koordinat_gabungan')) {
                $coords = explode(',', $request->koordinat_gabungan);
                if (count($coords) >= 2) {
                    $data['latitude'] = trim($coords[0]);
                    $data['longitude'] = trim($coords[1]);
                }
            } else {
                $data['latitude'] = $request->latitude;
                $data['longitude'] = $request->longitude;
            }

            // ✅ LOGIKA PENYIMPANAN EDIT MENIRU PARKING LOCATION
            $safeName = Str::slug($request->name);
            $randomNum = mt_rand(100, 999);

            if ($request->hasFile('image')) {
                if ($locationRequest->image) {
                    Storage::disk('public')->delete($locationRequest->image);
                }

                $imageName = time().'_'.Str::random(8).'.'.$request->image->extension();
                $data['image'] = $request->file('image')->storeAs('uploads/location_requests/images', $imageName, 'public');
            }

            if ($request->hasFile('proposal_document')) {
                if ($locationRequest->proposal_document) {
                    Storage::disk('public')->delete($locationRequest->proposal_document);
                }

                $proposalName = "pengajuan_{$safeName}_{$randomNum}.".$request->proposal_document->extension();
                $data['proposal_document'] = $request->file('proposal_document')->storeAs('uploads/location_requests/proposals', $proposalName, 'public');
            }
        } else {
            $request->validate([
                'parking_location_id' => 'required|exists:parking_locations,id',
            ], $this->customMessages);

            $data['parking_location_id'] = $request->parking_location_id;
            $data['road_section_name'] = null;
            $data['name'] = null;
            $data['offered_daily_deposit'] = null;
        }

        $locationRequest->update($data);

        return redirect()->route('field_coordinator.location-requests.index')
            ->with('success', 'Perubahan data pengajuan berhasil disimpan.');
    }

    public function destroy(LocationRequest $locationRequest)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        if ($locationRequest->agreement->fieldCoordinator->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin.');
        }

        if ($locationRequest->status !== 'pending') {
            return redirect()->route('field_coordinator.location-requests.index')
                ->with('error', 'Hanya pengajuan berstatus PENDING yang dapat dibatalkan/dihapus.');
        }

        if ($locationRequest->image) {
            Storage::disk('public')->delete($locationRequest->image);
        }
        if ($locationRequest->proposal_document) {
            Storage::disk('public')->delete($locationRequest->proposal_document);
        }

        $locationRequest->delete();

        return redirect()->route('field_coordinator.location-requests.index')
            ->with('success', 'Pengajuan berhasil dibatalkan dan dihapus.');
    }
}
