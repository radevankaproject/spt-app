<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Titik Lokasi Parkir</title>
    <style>
        @font-face {
            font-family: 'Work Sans';
            font-style: normal;
            font-weight: 400;
            src: url("{{ public_path('fonts/work_sans/WorkSans-Regular.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Work Sans';
            font-style: normal;
            font-weight: 600;
            src: url("{{ public_path('fonts/work_sans/WorkSans-SemiBold.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Work Sans';
            font-style: normal;
            font-weight: 700;
            src: url("{{ public_path('fonts/work_sans/WorkSans-Bold.ttf') }}") format('truetype');
        }
        body {
            font-family: 'Work Sans', sans-serif;
            font-size: 11px;
            color: #2b2b2b;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 3px 0;
            font-size: 12px;
            color: #7f8c8d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #bdc3c7;
            padding: 10px 8px;
            vertical-align: middle;
        }
        th {
            background-color: #2c3e50;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .summary-box {
            margin: 15px auto;
            border-top: 1px dashed #bdc3c7;
            border-bottom: 1px dashed #bdc3c7;
            padding: 10px 0;
            width: 80%;
        }
        .summary-box table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .summary-box th, .summary-box td {
            border: none;
            padding: 4px 8px;
            background: transparent;
        }
        .summary-box td.label {
            text-align: right;
            font-size: 11px;
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            width: 50%;
        }
        .summary-box td.value {
            text-align: left;
            font-size: 12px;
            color: #2c3e50;
            font-weight: 700;
            width: 50%;
        }
        .road-section-header {
            background-color: #ecf0f1;
            font-weight: 700;
            font-size: 12px;
            color: #2c3e50;
        }
        .subtotal-row {
            background-color: #f2f4f4;
            font-weight: 700;
            color: #34495e;
        }
        .grand-total-row {
            background-color: #34495e;
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
        }
        .grand-total-row td {
            border-color: #34495e;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 style="margin: 0; font-size: 26px; color: #1a252f; letter-spacing: 2px;">LAPORAN TITIK PARKIR</h1>
        <p style="font-size: 13px; letter-spacing: 0.5px;">SISTEM PENGELOLAAN PARKIR (SPT-APP)</p>
        
        <div class="summary-box">
            <table>
                <tr>
                    <td class="label">Total Titik Lokasi :</td>
                    <td class="value">{{ $parkingLocations->count() }} Titik Parkir</td>
                </tr>
                <tr>
                    <td class="label">Waktu Cetak :</td>
                    <td class="value">{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
                </tr>
            </table>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="20%">Nama Titik Lokasi</th>
                <th width="10%">Zona</th>
                <th width="20%">Koordinator Lapangan</th>
                <th class="text-right" width="15%">Setoran (Rp)</th>
                <th class="text-right" width="15%">Survey Tajuk (Rp)</th>
                <th class="text-right" width="15%">Survey Tanam (Rp)</th>
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
                    <td colspan="7" class="road-section-header">
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
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $location->name }}</td>
                        <td class="text-center">{{ $location->roadSection->zone ?? '-' }}</td>
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
                        <td class="text-right">{{ number_format($setoran, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $tajuk > 0 ? number_format((float)$tajuk, 0, ',', '.') : '-' }}</td>
                        <td class="text-right">{{ $tanam > 0 ? number_format((float)$tanam, 0, ',', '.') : '-' }}</td>
                    </tr>
                @endforeach
                
                <tr class="subtotal-row">
                    <td colspan="4" class="text-right">Subtotal {{ $roadSectionName }}:</td>
                    <td class="text-right">{{ number_format($subTotalSetoran, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($subTotalTajuk, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($subTotalTanam, 0, ',', '.') }}</td>
                </tr>

                @php
                    $grandTotalSetoran += $subTotalSetoran;
                    $grandTotalTajuk += $subTotalTajuk;
                    $grandTotalTanam += $subTotalTanam;
                @endphp
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse

            @if($groupedLocations->isNotEmpty())
            <tr class="grand-total-row">
                <td colspan="4" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-right">{{ number_format($grandTotalSetoran, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotalTajuk, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotalTanam, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
