<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Setoran</title>
    <style>
        @font-face {
            font-family: 'Bookman Old Style';
            src: url('{{ storage_path('fonts/bookmanoldstyle.ttf') }}') format('truetype');
            font-weight: normal; font-style: normal;
        }
        @font-face {
            font-family: 'Bookman Old Style';
            src: url('{{ storage_path('fonts/bookmanoldstylebold.ttf') }}') format('truetype');
            font-weight: bold; font-style: normal;
        }
        body { font-family: 'Bookman Old Style', serif; margin: 1cm; padding: 0; font-size: 9pt; line-height: 1.5; color: #333; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 16pt; font-weight: bold; margin: 0 0 5px 0; text-transform: uppercase; }
        .header h2 { font-size: 12pt; font-weight: normal; margin: 0; color: #555; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 8pt; }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .total-row td { font-weight: bold; font-size: 9pt; }
        .bg-light { background-color: #f9f9f9; }

        .signature-table { width: 100%; border: none; margin-top: 40px; }
        .signature-table td { border: none; padding: 0; }
    </style>
</head>

<body>
    @php
        $activeTreasurer = \App\Models\Treasurer::with('user')->whereHas('user', function ($q) { $q->where('is_active', true); })->first();
    @endphp

    <div class="header">
        <h1>REKAPITULASI TRANSAKSI SETORAN PKS</h1>
        <h2>{{ strtoupper($reportTitle) }}</h2>
        <p style="margin: 5px 0 0 0; font-size: 8pt;">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">No. Dokumen PKS</th>
                <th style="width: 18%;">Koordinator (Mitra)</th>
                <th style="width: 12%;">Tanggal Setor</th>
                <th style="width: 20%;">Kode Referensi</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 13%;">Catatan</th>
                <th style="width: 12%;" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reportsByAgreement as $agreementId => $transactions)
                @foreach ($transactions as $report)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ count($transactions) }}" style="vertical-align: middle;"><strong>{{ $report->agreement->agreement_number ?? 'N/A' }}</strong></td>
                            <td rowspan="{{ count($transactions) }}" style="vertical-align: middle;">{{ strtoupper($report->agreement->fieldCoordinator->user->name ?? 'N/A') }}</td>
                        @endif
                        <td class="text-center">{{ $report->deposit_date->format('d/m/Y') }}</td>
                        <td class="text-center" style="font-family: monospace; font-size: 8pt;">{{ $report->referral_code ?? '-' }}</td>
                        <td class="text-center">{{ $report->is_validated ? 'SAH' : 'PENDING' }}</td>
                        <td style="font-size: 8pt;">{{ $report->notes ?? '-' }}</td>
                        <td class="text-right">{{ number_format($report->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="7" class="text-center" style="padding: 20px;"><em>Tidak ada transaksi setoran yang ditemukan.</em></td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row bg-light">
                <td colspan="6" class="text-right">TARGET PROYEKSI PENDAPATAN:</td>
                <td class="text-right">Rp {{ number_format($totalTargetAmount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL PENDAPATAN (SAH):</td>
                <td class="text-right">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row bg-light">
                <td colspan="6" class="text-right">PERSENTASE PENCAPAIAN:</td>
                <td class="text-right">{{ $percentage }}%</td>
            </tr>
        </tfoot>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                Pekanbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Mengetahui,<br><strong>Bendahara Penerimaan</strong>
                <br><br><br><br><br><br>
                <strong><u>{{ strtoupper($activeTreasurer->user->name ?? '.......................................') }}</u></strong><br>
                NIP. {{ $activeTreasurer ? formatNip($activeTreasurer->employee_number) : '.......................................' }}
            </td>
        </tr>
    </table>
</body>
</html>
