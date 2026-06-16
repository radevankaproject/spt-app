<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Titik Lokasi Parkir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Titik Lokasi Parkir</h2>
        <p>Aplikasi Pengelolaan Parkir (SPT-APP)</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="20%">Nama Titik Lokasi</th>
                <th width="15%">Ruas Jalan</th>
                <th width="10%">Zona</th>
                <th width="20%">Koordinator Lapangan</th>
                <th width="15%">Status</th>
                <th class="text-right" width="15%">Setoran (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parkingLocations as $index => $location)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $location->name }}</td>
                    <td>{{ $location->roadSection->name ?? '-' }}</td>
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
                    <td>{{ ucfirst(str_replace('_', ' ', $location->status)) }}</td>
                    <td class="text-right">{{ number_format($location->daily_deposit, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            @if($parkingLocations->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data.</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
