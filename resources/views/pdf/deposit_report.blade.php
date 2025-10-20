<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>Laporan Setoran - {{ $reportTitle }}</title>
        <style>
            @font-face {
                font-family: 'Bookman Old Style';
                src: url('{{ storage_path(' fonts/bookmanoldstyle.ttf') }}') format('truetype');
                font-weight: normal;
                font-style: normal;
            }

            @font-face {
                font-family: 'Bookman Old Style';
                src: url('{{ storage_path(' fonts/bookmanoldstylebold.ttf') }}') format('truetype');
                font-weight: bold;
                font-style: normal;
            }

            body {
                font-family: 'Bookman Old Style', serif;
                margin: 1cm;
                padding: 0;
                font-size: 9pt;
                line-height: 1.5;
            }

            .container {
                width: 100%;
                box-sizing: border-box;
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .header h1 {
                font-size: 16pt;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .header h2 {
                font-size: 14pt;
                font-weight: normal;
            }

            .header h3 {
                /* Added style for the new search filter line */
                font-size: 12pt;
                font-weight: normal;
                margin-top: 5px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
                font-weight: bold;
                text-transform: uppercase;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .total-row {
                background-color: #e0e0e0;
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="header">
                <h1>Laporan Transaksi Setoran</h1>
                <h2>{{ $reportTitle }}</h2>
                {{-- Display combined or individual search/filter terms --}}
                @if (isset($search) && $search && isset($fieldCoordinatorId) && $fieldCoordinatorId)
                    <h3>Filter: "{{ $search }}" (Untuk Koordinator:
                        {{ \App\Models\FieldCoordinator::find($fieldCoordinatorId)->user->name ?? 'N/A' }})</h3>
                @elseif(isset($search) && $search)
                    <h3>Filter Pencarian: "{{ $search }}"</h3>
                @elseif(isset($fieldCoordinatorId) && $fieldCoordinatorId)
                    <h3>Untuk Koordinator:
                        {{ \App\Models\FieldCoordinator::find($fieldCoordinatorId)->user->name ?? 'N/A' }}</h3>
                @endif
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No. Perjanjian</th>
                        <th>Korlap</th>
                        <th>Tanggal Setoran</th>
                        <th>Kode Referensi</th>
                        <th>Status Deposit</th>
                        <th>Catatan Deposit</th>
                        <th class="text-right">Jumlah Setoran (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ✅ LOGIKA BARU MENGGUNAKAN LOOP BERTINGKAT --}}
                    @forelse ($reportsByAgreement as $agreementId => $transactions)
                        @foreach ($transactions as $report)
                            <tr>
                                {{-- Tampilkan No PKS & Korlap HANYA di baris pertama setiap grup --}}
                                @if ($loop->first)
                                    <td rowspan="{{ count($transactions) }}">
                                        {{ $report->agreement->agreement_number ?? 'N/A' }}
                                    </td>
                                    <td rowspan="{{ count($transactions) }}">
                                        {{ $report->agreement->fieldCoordinator->user->name ?? 'N/A' }}
                                    </td>
                                @endif

                                {{-- Kolom ini akan selalu tampil untuk setiap transaksi --}}
                                <td>{{ $report->deposit_date->format('d M Y') }}</td>
                                <td>{{ $report->referral_code ?? '-' }}</td>
                                <td class="text-center">{{ $report->is_validated ? 'Tervalidasi' : 'Pending' }}</td>
                                <td>{{ $report->notes ?? '-' }}</td>
                                <td class="text-right">Rp {{ number_format($report->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data setoran untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="6" class="text-right">Total Setoran Tervalidasi:</td>
                        <td class="text-right">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>

</html>
