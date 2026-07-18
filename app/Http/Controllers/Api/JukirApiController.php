<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jukir;
use Carbon\Carbon;

class JukirApiController extends Controller
{
    public function show($id)
    {
        $jukir = Jukir::with('parkingLocation.roadSection')->withCount('complaints')->where('id_jukir', $id)->first();

        if (!$jukir) {
            return response()->json([
                'success' => false,
                'message' => 'Juru Parkir tidak ditemukan'
            ], 404);
        }

        $isExpired = Carbon::parse($jukir->kta_end_date)->isPast();

        return response()->json([
            'success' => true,
            'data' => [
                'id_jukir' => $jukir->id_jukir,
                'nama_jukir' => $jukir->nama_jukir,
                'jenis_kelamin' => $jukir->jenis_kelamin,
                'image_url' => $jukir->image_url,
                'parking_location' => $jukir->parkingLocation->name ?? 'Belum Ditentukan',
                'road_section' => $jukir->parkingLocation->roadSection->name ?? '-',
                'is_active' => $jukir->is_active,
                'is_expired' => $isExpired,
                'is_blacklisted' => $jukir->is_blacklisted,
                'kta_end_date' => $jukir->kta_end_date ? \Carbon\Carbon::parse($jukir->kta_end_date)->locale('id')->translatedFormat('d F Y') : null,
                'complaints_count' => $jukir->complaints_count ?? 0,
            ]
        ]);
    }
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $jukirs = Jukir::with('parkingLocation.roadSection')
            ->withCount('complaints')
            ->when($search, function ($query, $search) {
                return $query->where('nama_jukir', 'like', "%{$search}%")
                             ->orWhere('id_jukir', 'like', "%{$search}%")
                             ->orWhereHas('parkingLocation', function($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%")
                                   ->orWhereHas('roadSection', function($q2) use ($search) {
                                       $q2->where('name', 'like', "%{$search}%");
                                   });
                             });
            })
            ->paginate(12);

        $result = $jukirs->map(function($jukir) {
            $isExpired = \Carbon\Carbon::parse($jukir->kta_end_date)->isPast();
            return [
                'id_jukir' => $jukir->id_jukir,
                'nama_jukir' => $jukir->nama_jukir,
                'parking_location' => $jukir->parkingLocation->name ?? 'Belum Ditentukan',
                'road_section' => $jukir->parkingLocation->roadSection->name ?? '-',
                'image_url' => $jukir->image_url,
                'kta_end_date' => $jukir->kta_end_date ? \Carbon\Carbon::parse($jukir->kta_end_date)->locale('id')->translatedFormat('d F Y') : null,
                'is_expired' => $isExpired,
                'is_blacklisted' => $jukir->is_blacklisted,
                'complaints_count' => $jukir->complaints_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result,
            'meta' => [
                'current_page' => $jukirs->currentPage(),
                'last_page' => $jukirs->lastPage(),
                'has_more_pages' => $jukirs->hasMorePages(),
                'total' => $jukirs->total(),
            ]
        ]);
    }

    public function syncComplaint(Request $request)
    {
        $validated = $request->validate([
            'report_code' => 'required|string',
            'jukir_id_spt' => 'required|string',
            'reporter_name' => 'required|string',
            'reporter_phone' => 'nullable|string',
            'category' => 'nullable|string',
            'description' => 'required|string',
            'evidence_urls' => 'nullable|array',
        ]);

        $jukir = Jukir::where('id_jukir', $validated['jukir_id_spt'])->first();
        if (!$jukir) {
            return response()->json(['success' => false, 'message' => 'Jukir not found'], 404);
        }

        $complaint = \App\Models\JukirComplaint::updateOrCreate(
            ['report_code' => $validated['report_code']],
            [
                'jukir_id' => $jukir->id,
                'reporter_name' => $validated['reporter_name'],
                'reporter_phone' => $validated['reporter_phone'],
                'description' => $validated['description'],
                'category' => $validated['category'] ?? 'lainnya',
                'evidence_urls' => $validated['evidence_urls'] ?? null,
                'status' => 'pending'
            ]
        );

        return response()->json(['success' => true, 'data' => $complaint]);
    }

    public function updateComplaintSync(Request $request, $report_code)
    {
        $validated = $request->validate([
            'status' => 'nullable|string',
            'admin_officer' => 'nullable|string',
            'field_officer_name' => 'nullable|string',
            'follow_up_description' => 'nullable|string',
            'follow_up_evidence_urls' => 'nullable|array',
            'is_violation_proven' => 'nullable|boolean',
        ]);

        $complaint = \App\Models\JukirComplaint::where('report_code', $report_code)->first();
        if (!$complaint) {
            return response()->json(['success' => false, 'message' => 'Complaint not found'], 404);
        }

        if (!empty($validated['status'])) {
            $mappedStatus = 'pending';
            if (in_array($validated['status'], ['rejected', 'invalid'])) {
                $mappedStatus = 'invalid';
            } elseif (in_array($validated['status'], ['verified', 'completed', 'in_progress'])) {
                $mappedStatus = 'valid';
            }
            $complaint->status = $mappedStatus;
        }

        if (array_key_exists('admin_officer', $validated)) {
            $complaint->admin_note = "Diteruskan ke tim lapangan oleh: " . $validated['admin_officer'];
        }

        if (array_key_exists('field_officer_name', $validated)) {
            $complaint->field_officer_name = $validated['field_officer_name'];
        }

        if (array_key_exists('follow_up_description', $validated)) {
            $complaint->follow_up_description = $validated['follow_up_description'];
        }

        if (array_key_exists('follow_up_evidence_urls', $validated)) {
            $complaint->follow_up_evidence_urls = $validated['follow_up_evidence_urls'];
        }

        if (array_key_exists('is_violation_proven', $validated)) {
            $complaint->is_violation_proven = $validated['is_violation_proven'];

            if ($validated['is_violation_proven'] == true) {
                \App\Models\JukirViolation::firstOrCreate([
                    'jukir_id' => $complaint->jukir_id,
                    'complaint_id' => $complaint->id
                ], [
                    'violation_date' => now(),
                    'description' => 'Pelanggaran terbukti dari laporan warga: ' . $complaint->category,
                    'is_resolved' => false
                ]);
            }
        }

        $complaint->save();

        return response()->json(['success' => true]);
    }

    public function publicStats()
    {
        // 1. Get Blacklisted Jukirs
        $blacklisted = Jukir::with('parkingLocation.roadSection')
            ->withCount('complaints')
            ->where('is_blacklisted', true)
            ->get()
            ->map(function ($jukir) {
                return [
                    'id_jukir' => $jukir->id_jukir,
                    'nama_jukir' => $jukir->nama_jukir,
                    'parking_location' => $jukir->parkingLocation->name ?? 'Belum Ditentukan',
                    'road_section' => $jukir->parkingLocation->roadSection->name ?? '-',
                    'image_url' => $jukir->image_url,
                    'kta_end_date' => $jukir->kta_end_date ? \Carbon\Carbon::parse($jukir->kta_end_date)->locale('id')->translatedFormat('d F Y') : null,
                    'is_expired' => $jukir->kta_end_date ? \Carbon\Carbon::parse($jukir->kta_end_date)->isPast() : false,
                    'is_blacklisted' => $jukir->is_blacklisted,
                    'complaints_count' => $jukir->complaints_count,
                ];
            });

        // 2. Get Problematic Jukirs (Top 5 with most complaints)
        $problematic = Jukir::with('parkingLocation.roadSection')
            ->withCount('complaints')
            ->having('complaints_count', '>', 0)
            ->orderByDesc('complaints_count')
            ->take(5)
            ->get()
            ->map(function ($jukir) {
                return [
                    'id_jukir' => $jukir->id_jukir,
                    'nama_jukir' => $jukir->nama_jukir,
                    'parking_location' => $jukir->parkingLocation->name ?? 'Belum Ditentukan',
                    'road_section' => $jukir->parkingLocation->roadSection->name ?? '-',
                    'image_url' => $jukir->image_url,
                    'kta_end_date' => $jukir->kta_end_date ? \Carbon\Carbon::parse($jukir->kta_end_date)->locale('id')->translatedFormat('d F Y') : null,
                    'is_expired' => $jukir->kta_end_date ? \Carbon\Carbon::parse($jukir->kta_end_date)->isPast() : false,
                    'is_blacklisted' => $jukir->is_blacklisted,
                    'complaints_count' => $jukir->complaints_count,
                ];
            });

        // 3. Get Total Counts
        $total_jukir = Jukir::count();
        $total_blacklisted = Jukir::where('is_blacklisted', true)->count();
        $total_problematic = Jukir::has('complaints')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'blacklisted' => $blacklisted,
                'problematic' => $problematic,
                'totals' => [
                    'all' => $total_jukir,
                    'blacklisted' => $total_blacklisted,
                    'problematic' => $total_problematic,
                ]
            ]
        ]);
    }
}
