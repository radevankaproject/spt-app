<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Bukti Setoran - #{{ $depositTransaction->referral_code }}</title>
    <style>
        @font-face {
            font-family: 'Bookman Old Style';
            src: url('{{ storage_path('fonts/bookmanoldstyle.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Bookman Old Style';
            src: url('{{ storage_path('fonts/bookmanoldstylebold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        body {
            font-family: 'Bookman Old Style', serif;
            font-size: 11px; /* Dikurangi sedikit agar muat 1 halaman */
            margin: 0.5cm; /* Margin dikurangi dari 1cm ke 0.5cm */
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            page-break-after: avoid;
            page-break-before: avoid;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            height: 65px;
            margin-bottom: 10px;
        }

        .header h3 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 2px;
        }

        .header p {
            margin: 5px 0;
            font-size: 11px;
            color: #555;
        }

        .divider {
            border-top: 2px solid #333;
            border-bottom: 1px solid #333;
            height: 2px;
            margin-bottom: 20px;
        }

        /* ✅ BUNGKUSAN CARD UNTUK WATERMARK */
        .content-card {
            position: relative;
            border: 1px solid #ccc;
            padding: 15px; /* Kurangi padding agar hemat ruang vertikal */
            border-radius: 5px;
            margin-bottom: 10px;
            overflow: hidden; /* Mengunci watermark agar tidak keluar kotak */
            background-color: #fff;
            z-index: 1;
        }

        /* ✅ WATERMARK YANG LEBIH SAMAR DAN RAPI */
        .watermark-container {
            position: absolute;
            top: -50%;
            left: -20%;
            width: 150%;
            height: 200%;
            z-index: -1;
            transform: rotate(-25deg);
            opacity: 0.05; /* Sangat samar */
            line-height: 4;
            text-align: center;
            color: #999;
        }

        .watermark-text {
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
            margin-right: 20px;
        }

        .watermark-logo {
            height: 12px;
            vertical-align: middle;
            margin-right: 5px;
            filter: grayscale(100%);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        table td {
            padding: 4px 0; /* Kurangi padding tabel */
            vertical-align: top;
        }

        .details-table td:first-child {
            width: 35%;
            font-weight: bold;
        }

        .notes-section {
            margin-top: 15px;
            padding: 10px;
            border: 1px dashed #aaa;
            background-color: #fcfcfc;
            text-align: left;
        }

        .notes-section p {
            margin: 0;
        }

        .total-section {
            padding-top: 15px;
            text-align: right;
        }

        .total-section h3 {
            margin: 5px 0 0 0;
            font-size: 18px;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            border-radius: 4px;
        }

        .badge-success { color: #1a5632; background-color: #d4edda; border: 1px solid #c3e6cb; }
        .badge-warning { color: #664d03; background-color: #fff3cd; border: 1px solid #ffeeba; }

        .signature-area {
            margin-top: 15px; /* Kurangi margin atas */
            text-align: center;
            page-break-inside: avoid; /* PASTIKAN TTD TIDAK PINDAH HALAMAN */
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if ($uptProfile->logo && file_exists(public_path('storage/' . $uptProfile->logo)))
                <img src="{{ public_path('storage/' . $uptProfile->logo) }}" alt="Logo">
            @else
                <img src="{{ public_path('logo.png') }}" alt="Logo">
            @endif

            <h3>BUKTI SETORAN DANA</h3>
            <p>{{ strtoupper($uptProfile->name) }}<br>{{ $uptProfile->address }}</p>
        </div>

        <div class="divider"></div>

        {{-- ✅ KOTAK KONTEN (CARD) BERISI DATA & WATERMARK --}}
        <div class="content-card">

            @if ($depositTransaction->is_validated)
                <div class="watermark-container">
                    @php
                        $logoPath = ($uptProfile->logo && file_exists(public_path('storage/' . $uptProfile->logo)))
                                    ? public_path('storage/' . $uptProfile->logo)
                                    : public_path('logo.png');
                    @endphp
                    {{-- Looping terbatas hanya untuk di dalam kotak --}}
                    @for ($i = 0; $i < 60; $i++)
                        <span class="watermark-text">
                            <img src="{{ $logoPath }}" class="watermark-logo">
                            TERVALIDASI - {{ $depositTransaction->referral_code }}
                        </span>
                    @endfor
                </div>
            @endif

            <table class="details-table">
                <tr>
                    <td>Kode Referensi</td>
                    <td>: <strong>{{ $depositTransaction->referral_code }}</strong></td>
                </tr>
                <tr>
                    <td>Tanggal Setor (Struk)</td>
                    <td>: {{ $depositTransaction->deposit_date->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Nomor Dokumen PKS</td>
                    <td>: {{ $depositTransaction->agreement->agreement_number }}</td>
                </tr>
                <tr>
                    <td>Koordinator Lapangan</td>
                    <td>: {{ $depositTransaction->agreement->fieldCoordinator->user->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Rincian Tarif Harian</td>
                    <td>: Rp {{ number_format($depositTransaction->agreement->daily_deposit_amount, 0, ',', '.') }} &times; {{ $daysInMonth }} Hari</td>
                </tr>
                <tr>
                    <td>Tagihan Asli</td>
                    <td>: Rp {{ number_format($depositTransaction->amount + $depositTransaction->discount_amount, 0, ',', '.') }}</td>
                </tr>
                @if ($depositTransaction->discount_amount > 0)
                    <tr>
                        <td>Potongan/Keringanan</td>
                        <td>: Rp {{ number_format($depositTransaction->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Disetujui Oleh</td>
                        <td>: {{ $depositTransaction->discountApprover->name ?? 'N/A' }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Pembayaran Untuk Bulan</td>
                    <td>: <strong>{{ $monthName . ' ' . $year }}</strong></td>
                </tr>
                <tr>
                    <td>Status Transaksi</td>
                    <td>:
                        @if ($depositTransaction->is_validated)
                            <span class="badge badge-success">TERVALIDASI</span>
                        @else
                            <span class="badge badge-warning">PENDING</span>
                        @endif
                    </td>
                </tr>

                @if ($depositTransaction->is_validated)
                    <tr>
                        <td>Divalidasi Oleh</td>
                        <td>: {{ $depositTransaction->validator->name ?? 'Administrator' }}</td>
                    </tr>
                    <tr>
                        <td>Waktu Validasi</td>
                        <td>: {{ $depositTransaction->validation_date ? \Carbon\Carbon::parse($depositTransaction->validation_date)->translatedFormat('d F Y, H:i') : '-' }} WIB</td>
                    </tr>
                @endif
            </table>

            @if (!empty($depositTransaction->notes))
                <div class="notes-section">
                    <p><strong>Catatan Tambahan:</strong><br><em>{{ $depositTransaction->notes }}</em></p>
                </div>
            @endif

            @if (!empty($depositTransaction->discount_notes))
                <div class="notes-section" style="margin-top: 5px;">
                    <p><strong>Alasan Potongan:</strong><br><em>{{ $depositTransaction->discount_notes }}</em></p>
                </div>
            @endif

        </div> {{-- End Content Card --}}

        <div class="total-section">
            <p>Total Nilai Setoran:</p>
            <h3>Rp {{ number_format($depositTransaction->amount, 0, ',', '.') }},-</h3>
        </div>

        <div class="signature-area">
            <table class="signature-table">
                <tr>
                    <td>
                        <br>
                        Penyetor (Koordinator Lapangan),
                        <br><br><br><br><br>
                        <strong><u>{{ strtoupper($depositTransaction->agreement->fieldCoordinator->user->name ?? '...........................') }}</u></strong>
                    </td>
                    <td>
                        {{-- ✅ TANGGAL TANDA TANGAN WAJIB MENGIKUTI TANGGAL VALIDASI --}}
                        Pekanbaru, {{ $depositTransaction->validation_date ? \Carbon\Carbon::parse($depositTransaction->validation_date)->translatedFormat('d F Y') : '...........................' }}<br>
                        Bendahara Penerimaan,
                        <br><br><br><br><br>
                        <strong><u>{{ strtoupper($depositTransaction->treasurer->user->name ?? '...........................') }}</u></strong><br>
                        NIP. {{ formatNip($depositTransaction->treasurer->employee_number ?? '') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
