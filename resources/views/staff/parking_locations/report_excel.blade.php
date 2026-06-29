<table>
    <thead>
        <tr>
            <th colspan="7" style="font-weight: bold; font-size: 16px; text-align: center;">LAPORAN TITIK PARKIR - SPT-APP</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">Total Titik Lokasi: {{ $parkingLocations->count() }} Titik Parkir | Waktu Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="5">No</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="30">Nama Titik Lokasi</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="15">Zona</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="30">Koordinator Lapangan</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="20">Setoran (Rp)</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="20">Survey Tajuk (Rp)</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="20">Survey Tanam (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $groupedLocations = $parkingLocations->groupBy(function($item) {
                return $item->roadSection->name ?? 'Tanpa Ruas Jalan';
            });
            
            $grandTotalSetoran = 0;
            $grandTotalTajuk = 0;
            $grandTotalTanam = 0;
        @endphp

        @forelse($groupedLocations as $roadSectionName => $locations)
            <tr>
                <td colspan="7" style="font-weight: bold; background-color: #ecf0f1;">
                    Ruas Jalan: {{ $roadSectionName }} ({{ $locations->count() }} Titik)
                </td>
            </tr>
            @php
                $subTotalSetoran = 0;
                $subTotalTajuk = 0;
                $subTotalTanam = 0;
            @endphp
            @foreach($locations as $index => $location)
                @php
                    $setoran = $location->daily_deposit ?? 0;
                    $tajuk = $location->latestSurvey->survey_tajuk ?? 0;
                    $tanam = $location->latestSurvey->survey_tanam ?? 0;

                    $subTotalSetoran += $setoran;
                    $subTotalTajuk += $tajuk;
                    $subTotalTanam += $tanam;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $location->name }}</td>
                    <td>{{ $location->roadSection->zone ?? '-' }}</td>
                    <td>
                        @php
                            $activeAgreement = $location->agreements->first();
                        @endphp
                        @if($activeAgreement && $activeAgreement->fieldCoordinator && $activeAgreement->fieldCoordinator->user)
                            {{ $activeAgreement->fieldCoordinator->user->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $setoran }}</td>
                    <td>{{ $tajuk > 0 ? $tajuk : '-' }}</td>
                    <td>{{ $tanam > 0 ? $tanam : '-' }}</td>
                </tr>
            @endforeach
            
            <tr>
                <td colspan="4" style="font-weight: bold; text-align: right; background-color: #f2f4f4;">Subtotal {{ $roadSectionName }}:</td>
                <td style="font-weight: bold; background-color: #f2f4f4;">{{ $subTotalSetoran }}</td>
                <td style="font-weight: bold; background-color: #f2f4f4;">{{ $subTotalTajuk }}</td>
                <td style="font-weight: bold; background-color: #f2f4f4;">{{ $subTotalTanam }}</td>
            </tr>

            @php
                $grandTotalSetoran += $subTotalSetoran;
                $grandTotalTajuk += $subTotalTajuk;
                $grandTotalTanam += $subTotalTanam;
            @endphp
        @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data.</td>
            </tr>
        @endforelse

        @if($groupedLocations->isNotEmpty())
        <tr>
            <td colspan="4" style="font-weight: bold; text-align: right; background-color: #34495e; color: #ffffff;">TOTAL KESELURUHAN:</td>
            <td style="font-weight: bold; background-color: #34495e; color: #ffffff;">{{ $grandTotalSetoran }}</td>
            <td style="font-weight: bold; background-color: #34495e; color: #ffffff;">{{ $grandTotalTajuk }}</td>
            <td style="font-weight: bold; background-color: #34495e; color: #ffffff;">{{ $grandTotalTanam }}</td>
        </tr>
        @endif
    </tbody>
</table>
