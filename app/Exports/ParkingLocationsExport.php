<?php

namespace App\Exports;

use App\Models\ParkingLocation;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParkingLocationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = ParkingLocation::with(['roadSection', 'agreements' => function ($q) {
            $q->where('agreements.status', 'active')
              ->where('agreement_parking_locations.status', 'active')
              ->with('fieldCoordinator.user');
        }]);

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

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Titik Lokasi',
            'Ruas Jalan',
            'Zona',
            'Status Lokasi',
            'Koordinator Lapangan',
            'Setoran Harian',
            'Estimasi Luas',
            'Estimasi SRP R2',
            'Estimasi SRP R4',
            'Titik Koordinat',
        ];
    }

    public function map($location): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        // Ambil data Korlap aktif dari relasi agreements yang telah di-filter 'active'
        $activeAgreement = $location->agreements->first();
        $korlapName = '-';
        
        if ($activeAgreement && $activeAgreement->fieldCoordinator && $activeAgreement->fieldCoordinator->user) {
            $korlapName = $activeAgreement->fieldCoordinator->user->name;
        }

        return [
            $rowNumber,
            $location->name,
            $location->roadSection->name ?? '-',
            $location->roadSection->zone ?? '-',
            ucfirst($location->status),
            $korlapName,
            $location->daily_deposit,
            $location->estimated_area ?? '-',
            $location->estimated_srp_r2 ?? '-',
            $location->estimated_srp_r4 ?? '-',
            ($location->latitude && $location->longitude) ? $location->latitude . ', ' . $location->longitude : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
