<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SurveyParkingLocation;
use App\Models\ParkingLocation;
use App\Models\Jukir;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SurveyParkingLocationController extends Controller
{
    public function index(Request $request)
    {
        $zones = \App\Models\RoadSection::select('zone')->distinct()->pluck('zone');
        
        $selected_zone = $request->get('zone');
        $roadSections = \App\Models\RoadSection::when($selected_zone, function($query) use ($selected_zone) {
            return $query->where('zone', $selected_zone);
        })->orderBy('name')->get();

        $query = SurveyParkingLocation::with(['parkingLocation.roadSection', 'jukir']);

        if ($request->filled('zone')) {
            $query->whereHas('parkingLocation.roadSection', function($q) use ($request) {
                $q->where('zone', $request->zone);
            });
        }

        if ($request->filled('road_section_id')) {
            $query->whereHas('parkingLocation', function($q) use ($request) {
                $q->where('road_section_id', $request->road_section_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('parkingLocation', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $surveys = $query->latest()->get();

        return view('admin.survey_parking_locations.index', compact('surveys', 'zones', 'roadSections'));
    }

    public function create(Request $request)
    {
        // For filtering Zone -> RoadSection
        $zones = \App\Models\RoadSection::select('zone')->distinct()->pluck('zone');

        $selected_zone = $request->get('zone');
        $roadSections = \App\Models\RoadSection::when($selected_zone, function($query) use ($selected_zone) {
            return $query->where('zone', $selected_zone);
        })->orderBy('name')->get();
        
        $selected_road_section_id = $request->get('road_section_id');
        $selected_survey_date = $request->get('survey_date', \Carbon\Carbon::now()->format('Y-m'));

        $parkingLocations = collect();

        if ($selected_road_section_id) {
            $parkingLocations = ParkingLocation::where('road_section_id', $selected_road_section_id)
                ->where('is_active', true)
                ->with(['surveys' => function ($query) use ($selected_survey_date) {
                    $query->where('survey_date', $selected_survey_date . '-01');
                }, 'surveys.jukir'])
                ->get();
        }

        return view('admin.survey_parking_locations.create', compact('zones', 'selected_zone', 'roadSections', 'selected_road_section_id', 'parkingLocations', 'selected_survey_date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'survey_date' => 'required|date_format:Y-m',
            'surveys' => 'required|array',
            'road_section_id' => 'required|exists:road_sections,id'
        ]);

        // Append -01 to make it a valid full date for the database
        $surveyDate = $request->survey_date . '-01';
        $surveys = $request->surveys;
        $countSaved = 0;

        foreach ($surveys as $locationId => $data) {
            // Check if at least one field is filled (allow '0' as valid input)
            $hasTajuk = isset($data['survey_tajuk']) && $data['survey_tajuk'] !== '';
            $hasTanam = isset($data['survey_tanam']) && $data['survey_tanam'] !== '';
            $hasJukir = !empty($data['nama_jukir']);
            $hasSurveyor = !empty($data['surveyor']);
            $hasNotes = !empty($data['notes']);

            if (!$hasTajuk && !$hasTanam && !$hasJukir && !$hasSurveyor && !$hasNotes) {
                continue; // Skip if all fields are empty
            }

            $jukir_id = null;

            // Handle Jukir
            if (!empty($data['nama_jukir'])) {
                // Find existing jukir with same name in that location, or create new
                $jukir = Jukir::firstOrCreate(
                    [
                        'nama_jukir' => $data['nama_jukir'],
                        'parking_location_id' => $locationId
                    ],
                    [
                        'is_active' => true
                    ]
                );
                $jukir_id = $jukir->id;
            }

            // Clean currency format
            $tajukValue = (isset($data['survey_tajuk']) && $data['survey_tajuk'] !== '') ? preg_replace('/[^0-9]/', '', $data['survey_tajuk']) : null;
            $tanamValue = (isset($data['survey_tanam']) && $data['survey_tanam'] !== '') ? preg_replace('/[^0-9]/', '', $data['survey_tanam']) : null;

            // Update or Create survey
            SurveyParkingLocation::updateOrCreate(
                [
                    'parking_location_id' => $locationId,
                    'survey_date' => $surveyDate,
                ],
                [
                    'survey_tajuk' => $tajukValue,
                    'survey_tanam' => $tanamValue,
                    'surveyor' => $data['surveyor'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'jukir_id' => $jukir_id,
                ]
            );

            $countSaved++;
        }

        if ($countSaved > 0) {
            return redirect()->route('admin.survey-parking-locations.index')
                             ->with('success', $countSaved . ' Data Survey Titik Lokasi berhasil disimpan.');
        }

        return redirect()->back()->with('error', 'Tidak ada data survey yang diisi untuk disimpan.');
    }

    public function edit(SurveyParkingLocation $survey_parking_location)
    {
        $parkingLocations = ParkingLocation::where('is_active', true)->get();
        $jukirs = Jukir::where('is_active', true)->get();
        return view('admin.survey_parking_locations.edit', compact('survey_parking_location', 'parkingLocations', 'jukirs'));
    }

    public function update(Request $request, SurveyParkingLocation $survey_parking_location)
    {
        $request->validate([
            'parking_location_id' => 'required|exists:parking_locations,id',
            'survey_date' => 'required|date_format:Y-m',
            'survey_tajuk' => 'nullable|string',
            'survey_tanam' => 'nullable|string',
            'surveyor' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'jukir_id' => 'nullable|exists:jukirs,id',
            'nama_jukir' => 'nullable|string|max:255',
            'no_ktp' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        $jukir_id = $request->jukir_id;

        // If new jukir data is provided
        if (!$jukir_id && $request->filled('nama_jukir')) {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('jukir_images', 'public');
            }

            $jukir = Jukir::create([
                'nama_jukir' => $request->nama_jukir,
                'parking_location_id' => $request->parking_location_id,
                'no_ktp' => $request->no_ktp,
                'phone_number' => $request->phone_number,
                'image' => $imagePath,
                'is_active' => true,
            ]);
            $jukir_id = $jukir->id;
        }

        $tajukValue = (isset($request->survey_tajuk) && $request->survey_tajuk !== '') ? preg_replace('/[^0-9]/', '', $request->survey_tajuk) : null;
        $tanamValue = (isset($request->survey_tanam) && $request->survey_tanam !== '') ? preg_replace('/[^0-9]/', '', $request->survey_tanam) : null;
        $surveyDate = $request->survey_date . '-01';

        $survey_parking_location->update([
            'parking_location_id' => $request->parking_location_id,
            'survey_tajuk' => $tajukValue,
            'survey_tanam' => $tanamValue,
            'surveyor' => $request->surveyor,
            'notes' => $request->notes,
            'survey_date' => $surveyDate,
            'jukir_id' => $jukir_id,
        ]);

        return redirect()->route('admin.survey-parking-locations.index')
                         ->with('success', 'Data Survey Titik Lokasi berhasil diupdate.');
    }

    public function destroy(SurveyParkingLocation $survey_parking_location)
    {
        $survey_parking_location->delete();
        return redirect()->route('admin.survey-parking-locations.index')
                         ->with('success', 'Data Survey Titik Lokasi berhasil dihapus.');
    }
}
