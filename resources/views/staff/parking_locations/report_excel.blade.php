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
            <th colspan="{{ $totalColspan }}" style="font-weight: bold; font-size: 16px; text-align: center;">LAPORAN TITIK PARKIR - SISTEM PERJANJIAN KERJASAMA PERPARKIRAN (SPKP)</th>
        </tr>
        <tr>
            <th colspan="{{ $totalColspan }}" style="text-align: center;">Total Titik Lokasi: {{ $parkingLocations->count() }} Titik Parkir | Waktu Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</th>
        </tr>
        <tr>
            <th colspan="{{ $totalColspan }}"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="5">No</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="30">Nama Titik Lokasi</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="15">Zona</th>
            @if($showKoordinator) <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="30">Koordinator Lapangan</th> @endif
            @if($showJukir) <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="30">Jukir</th> @endif
            @if($showSurveyor) <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="30">Surveyor</th> @endif
            @if($showKeterangan) <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="40">Keterangan</th> @endif
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="20">Setoran (Rp)</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="20">Survey Tajuk (Rp) *</th>
            <th style="font-weight: bold; background-color: #2c3e50; color: #ffffff;" width="20">Survey Tanam (Rp) **</th>
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
                <td colspan="{{ $totalColspan }}" style="font-weight: bold; background-color: #ecf0f1;">
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
                    <td>{{ $setoran }}</td>
                    <td>{{ $tajuk > 0 ? $tajuk : '-' }}</td>
                    <td>{{ $tanam > 0 ? $tanam : '-' }}</td>
                </tr>
            @endforeach
            
            <tr>
                <td colspan="{{ $baseColspan }}" style="font-weight: bold; text-align: right; background-color: #f2f4f4;">Subtotal {{ $roadSectionName }}:</td>
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
                <td colspan="{{ $totalColspan }}" style="text-align: center;">Tidak ada data.</td>
            </tr>
        @endforelse

        @if($groupedLocations->isNotEmpty())
        <tr>
            <td colspan="{{ $baseColspan }}" style="font-weight: bold; text-align: right; background-color: #34495e; color: #ffffff;">TOTAL KESELURUHAN:</td>
            <td style="font-weight: bold; background-color: #34495e; color: #ffffff;">{{ $grandTotalSetoran }}</td>
            <td style="font-weight: bold; background-color: #34495e; color: #ffffff;">{{ $grandTotalTajuk }}</td>
            <td style="font-weight: bold; background-color: #34495e; color: #ffffff;">{{ $grandTotalTanam }}</td>
        </tr>
        <tr>
            <td colspan="{{ $totalColspan }}"></td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}" style="font-weight: bold; font-size: 10px;">KETERANGAN</td>
            <td colspan="3" style="background-color: #2c3e50; color: white; text-align: center; font-weight: bold;">RINGKASAN TOTAL LAPORAN</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}" style="color: red; font-weight: bold; font-size: 10px;">* HASIL TANYA JUKIR BERSIFAT KOTOR ( GAJI JUKIR SUDAH DI KELUARKAN AKAN TETAPI GAJI JURU KUTIP DAN KEUNTUNGAN PENGELOLA BELUM DIKELUARKAN)</td>
            <td colspan="2" style="font-weight: bold;">Total Ruas Jalan</td>
            <td style="font-weight: bold; text-align: right;">{{ $groupedLocations->count() }} Ruas</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}" style="color: red; font-weight: bold; font-size: 10px;">** HASIL SURVEYOR PENANAMAN ANGGOTA BERSIFAT KOTOR ( BELUM DIKELUARKAN GAJI JUKIR, JURU KUTIP, DAN KEUNTUNGAN PENGELOLA)</td>
            <td colspan="2" style="font-weight: bold;">Total Titik Parkir</td>
            <td style="font-weight: bold; text-align: right;">{{ $parkingLocations->count() }} Titik</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="2" style="font-weight: bold;">Total Pendapatan (Setoran)</td>
            <td style="font-weight: bold; text-align: right; color: #27ae60;">Rp {{ number_format($grandTotalSetoran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="2" style="font-weight: bold;">Total Potensi Survey Tajuk</td>
            <td style="font-weight: bold; text-align: right; color: #e67e22;">Rp {{ number_format($grandTotalTajuk, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="2" style="font-weight: bold;">Total Potensi Survey Tanam</td>
            <td style="font-weight: bold; text-align: right; color: #d35400;">Rp {{ number_format($grandTotalTanam, 0, ',', '.') }}</td>
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
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="3" style="background-color: #ecf0f1; font-weight: bold; text-align: center;">POTENSI BULANAN</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="2" style="font-weight: bold;">Total Bulanan (Setoran)</td>
            <td style="font-weight: bold; text-align: right; color: #27ae60;">Rp {{ number_format($bulananSetoran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="2" style="font-weight: bold;">Total Bulanan (Tajuk)</td>
            <td style="font-weight: bold; text-align: right; color: #e67e22;">Rp {{ number_format($bulananTajuk, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="2" style="font-weight: bold;">Total Bulanan (Tanam)</td>
            <td style="font-weight: bold; text-align: right; color: #d35400;">Rp {{ number_format($bulananTanam, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="3" style="background-color: #ecf0f1; font-weight: bold; text-align: center;">POTENSI TAHUNAN</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="2" style="font-weight: bold;">Total Tahunan (Setoran)</td>
            <td style="font-weight: bold; text-align: right; color: #27ae60;">Rp {{ number_format($tahunanSetoran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="2" style="font-weight: bold;">Total Tahunan (Tajuk)</td>
            <td style="font-weight: bold; text-align: right; color: #e67e22;">Rp {{ number_format($tahunanTajuk, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="{{ $baseColspan }}"></td>
            <td colspan="2" style="font-weight: bold;">Total Tahunan (Tanam)</td>
            <td style="font-weight: bold; text-align: right; color: #d35400;">Rp {{ number_format($tahunanTanam, 0, ',', '.') }}</td>
        </tr>
        @endif
    </tbody>
</table>
