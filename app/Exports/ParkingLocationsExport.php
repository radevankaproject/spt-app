<?php

namespace App\Exports;

use App\Models\ParkingLocation;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParkingLocationsExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = ParkingLocation::with(['roadSection', 'latestSurvey.jukir', 'agreements' => function ($q) {
            $q->where('agreements.status', 'active')
              ->where('agreement_parking_locations.status', 'active')
              ->with('fieldCoordinator.user');
        }]);

        if ($this->request->filled('selected_locations') && is_array($this->request->selected_locations)) {
            $query->whereIn('parking_locations.id', $this->request->selected_locations);
            
            return view('staff.parking_locations.report_excel', [
                'parkingLocations' => $query->latest()->get()
            ]);
        }

        if ($this->request->filled('korlap_id')) {
            $query->whereHas('agreements', function ($q) {
                $q->where('agreements.status', 'active')
                  ->where('agreements.field_coordinator_id', $this->request->korlap_id);
            });
        }

        if ($this->request->filled('road_section_id')) {
            $roadSectionIds = is_array($this->request->road_section_id) ? $this->request->road_section_id : [$this->request->road_section_id];
            $query->whereIn('road_section_id', $roadSectionIds);
        }

        if ($this->request->filled('zone')) {
            $query->whereHas('roadSection', function ($q) {
                $q->where('zone', $this->request->zone);
            });
        }

        if ($this->request->filled('no_agreement') && $this->request->no_agreement == '1') {
            $query->whereDoesntHave('agreements', function ($q) {
                $q->where('agreement_parking_locations.status', 'active');
            });
        }

        if ($this->request->filled('surveyor')) {
            $query->whereHas('latestSurvey', function ($q) {
                $q->where('surveyor', 'like', '%' . $this->request->surveyor . '%');
            });
        }

        if ($this->request->filled('survey_status')) {
            if ($this->request->survey_status == 'sudah') {
                $query->has('latestSurvey');
            } elseif ($this->request->survey_status == 'belum') {
                $query->doesntHave('latestSurvey');
            }
        }

        return view('staff.parking_locations.report_excel', [
            'parkingLocations' => $query->latest()->get()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style header column or apply global formatting if needed
        ];
    }
}
