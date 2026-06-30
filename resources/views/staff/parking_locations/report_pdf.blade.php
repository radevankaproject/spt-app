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
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
        }
        .header table {
            width: 100%;
            border: none;
            margin-bottom: 0;
        }
        .header th, .header td {
            border: none;
            background-color: transparent;
            padding: 0;
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
        <table>
            <tr>
                <td style="width: 15%; text-align: left;">
                    @if(file_exists(public_path('assets/img/logo-spt.png')))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/logo-spt.png'))) }}" alt="Logo SPT" style="height: 70px; object-fit: contain;">
                    @endif
                </td>
                <td style="width: 70%; text-align: center;">
                    <h1 style="margin: 0; font-size: 26px; color: #1a252f; letter-spacing: 2px;">LAPORAN TITIK PARKIR</h1>
                    <p style="margin: 5px 0 0 0; font-size: 13px; font-weight: 600; letter-spacing: 0.5px; color: #34495e;">SISTEM PERJANJIAN KERJASAMA PERPARKIRAN (SPKP)</p>
                    <p style="margin: 5px 0 0 0; font-size: 11px; color: #7f8c8d;">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y - H:i') }} WIB</p>
                </td>
                <td style="width: 15%; text-align: right;">
                </td>
            </tr>
        </table>
    </div>

    @php
        $showKoordinator = request()->has('export_submitted') ? request()->has('show_koordinator') : true;
        $showJukir = request()->has('export_submitted') ? request()->has('show_jukir') : true;
        $showSurveyor = request()->has('export_submitted') ? request()->has('show_surveyor') : true;
        $showKeterangan = request()->has('export_submitted') ? request()->has('show_keterangan') : true;
        
        $baseColspan = 3; // No, Titik Lokasi, Zona
        if ($showKoordinator) $baseColspan++;
        if ($showJukir) $baseColspan++;
        if ($showSurveyor) $baseColspan++;
        if ($showKeterangan) $baseColspan++;
        
        $totalColspan = $baseColspan + 3; // +3 for Setoran, Tajuk, Tanam
    @endphp
    <table>
        <thead>
            <tr>
                <th class="text-center" width="3%">No</th>
                <th width="15%">Nama Titik Lokasi</th>
                <th width="8%">Zona</th>
                @if($showKoordinator) <th width="12%">Koordinator</th> @endif
                @if($showJukir) <th width="12%">Jukir</th> @endif
                @if($showSurveyor) <th width="12%">Surveyor</th> @endif
                @if($showKeterangan) <th width="12%">Keterangan</th> @endif
                <th class="text-right" width="10%">Setoran (Rp)</th>
                <th class="text-right" width="10%">Survey Tajuk (Rp) *</th>
                <th class="text-right" width="10%">Survey Tanam (Rp) **</th>
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
                    <td colspan="{{ $totalColspan }}" class="road-section-header">
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
                        
                        @if($showKoordinator)
                            <td>
                                @php $activeAgreement = $location->agreements->first(); @endphp
                                {{ $activeAgreement && $activeAgreement->fieldCoordinator && $activeAgreement->fieldCoordinator->user ? $activeAgreement->fieldCoordinator->user->name : '-' }}
                            </td>
                        @endif

                        @if($showJukir)
                            <td>{{ $location->latestSurvey->jukir->name ?? '-' }}</td>
                        @endif

                        @if($showSurveyor)
                            <td>{{ $location->latestSurvey->surveyor ?? '-' }}</td>
                        @endif

                        @if($showKeterangan)
                            <td>{{ $location->latestSurvey->notes ?? '-' }}</td>
                        @endif
                        <td class="text-right">{{ number_format($setoran, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $tajuk > 0 ? number_format((float)$tajuk, 0, ',', '.') : '-' }}</td>
                        <td class="text-right">{{ $tanam > 0 ? number_format((float)$tanam, 0, ',', '.') : '-' }}</td>
                    </tr>
                @endforeach
                
                <tr class="subtotal-row">
                    <td colspan="{{ $baseColspan }}" class="text-right">Subtotal {{ $roadSectionName }}:</td>
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
                    <td colspan="{{ $totalColspan }}" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse

            @if($groupedLocations->isNotEmpty())
            <tr class="grand-total-row">
                <td colspan="{{ $baseColspan }}" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-right">{{ number_format($grandTotalSetoran, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotalTajuk, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotalTanam, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    @if($groupedLocations->isNotEmpty())
    <div style="margin-top: 30px; page-break-inside: avoid;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 55%; vertical-align: top; border: none; padding-right: 20px;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="width: 20%; vertical-align: top; border: none; font-weight: 700; font-size: 10px; padding: 0;">KETERANGAN</td>
                            <td style="width: 80%; vertical-align: top; border: none; color: red; font-weight: 700; font-size: 10px; padding: 0; line-height: 1.6;">
                                * HASIL TANYA JUKIR BERSIFAT KOTOR ( GAJI JUKIR SUDAH DI KELUARKAN AKAN TETAPI GAJI JURU KUTIP DAN KEUNTUNGAN PENGELOLA BELUM DIKELUARKAN)<br><br>
                                ** HASIL SURVEYOR PENANAMAN ANGGOTA BERSIFAT KOTOR ( BELUM DIKELUARKAN GAJI JUKIR, JURU KUTIP, DAN KEUNTUNGAN PENGELOLA)
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 45%; vertical-align: top; border: none; padding: 0;">
                    <table style="width: 100%; border: 2px solid #2c3e50; background-color: #f8f9fa;">
                        <tr>
                            <th colspan="2" style="background-color: #2c3e50; color: white; text-align: center; font-size: 13px; padding: 12px; letter-spacing: 1px;">
                                RINGKASAN TOTAL LAPORAN
                            </th>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Ruas Jalan</td>
                            <td class="text-right" style="font-weight: 700; color: #2c3e50; padding: 10px; border: 1px solid #bdc3c7;">{{ $groupedLocations->count() }} Ruas</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Titik Parkir</td>
                            <td class="text-right" style="font-weight: 700; color: #2c3e50; padding: 10px; border: 1px solid #bdc3c7;">{{ $parkingLocations->count() }} Titik</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Pendapatan (Setoran)</td>
                            <td class="text-right" style="font-weight: 700; color: #27ae60; padding: 10px; border: 1px solid #bdc3c7;">Rp {{ number_format($grandTotalSetoran, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Potensi Survey Tajuk</td>
                            <td class="text-right" style="font-weight: 700; color: #e67e22; padding: 10px; border: 1px solid #bdc3c7;">Rp {{ number_format($grandTotalTajuk, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Potensi Survey Tanam</td>
                            <td class="text-right" style="font-weight: 700; color: #d35400; padding: 10px; border: 1px solid #bdc3c7;">Rp {{ number_format($grandTotalTanam, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $bulananSetoran = $grandTotalSetoran * 30;
                            $bulananTajuk = $grandTotalTajuk * 30;
                            $bulananTanam = $grandTotalTanam * 30;
                            
                            $tahunanSetoran = $bulananSetoran * 12;
                            $tahunanTajuk = $bulananTajuk * 12;
                            $tahunanTanam = $bulananTanam * 12;
                        @endphp
                        <tr>
                            <td colspan="2" style="background-color: #ecf0f1; font-weight: bold; text-align: center; padding: 8px;">POTENSI BULANAN</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Bulanan (Setoran)</td>
                            <td class="text-right" style="font-weight: 700; color: #27ae60; padding: 10px; border: 1px solid #bdc3c7;">Rp {{ number_format($bulananSetoran, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Bulanan (Tajuk)</td>
                            <td class="text-right" style="font-weight: 700; color: #e67e22; padding: 10px; border: 1px solid #bdc3c7;">Rp {{ number_format($bulananTajuk, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Bulanan (Tanam)</td>
                            <td class="text-right" style="font-weight: 700; color: #d35400; padding: 10px; border: 1px solid #bdc3c7;">Rp {{ number_format($bulananTanam, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="background-color: #ecf0f1; font-weight: bold; text-align: center; padding: 8px;">POTENSI TAHUNAN</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Tahunan (Setoran)</td>
                            <td class="text-right" style="font-weight: 700; color: #27ae60; padding: 10px; border: 1px solid #bdc3c7;">Rp {{ number_format($tahunanSetoran, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Tahunan (Tajuk)</td>
                            <td class="text-right" style="font-weight: 700; color: #e67e22; padding: 10px; border: 1px solid #bdc3c7;">Rp {{ number_format($tahunanTajuk, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 10px; border: 1px solid #bdc3c7;">Total Tahunan (Tanam)</td>
                            <td class="text-right" style="font-weight: 700; color: #d35400; padding: 10px; border: 1px solid #bdc3c7;">Rp {{ number_format($tahunanTanam, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>
    @endif

</body>
</html>
