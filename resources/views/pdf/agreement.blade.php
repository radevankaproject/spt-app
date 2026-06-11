<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PKS - {{ $agreement->agreement_number }}</title>
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
        @font-face {
            font-family: 'Bookman Old Style';
            src: url('{{ storage_path('fonts/bookmanoldstyle_italic.ttf') }}') format('truetype');
            font-weight: normal; font-style: italic;
        }
        @font-face {
            font-family: 'Bookman Old Style';
            src: url('{{ storage_path('fonts/bookmanoldstyle_bolditalic.ttf') }}') format('truetype');
            font-weight: bold; font-style: italic;
        }

        /* ✅ UKURAN KERTAS F4 */
        @page {
            size: 215mm 330mm;
            margin: 0;
        }

        body {
            font-family: 'Bookman Old Style', serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 1cm 2cm 2.5cm 2.5cm;
            color: #000;
        }
        .cover-page {
            text-align: center;
            height: 95vh;
            padding-top: 50px;
        }
        .cover-logo-container img { height: 95px; margin: 0 15px; }
        .cover-title h1 { font-size: 16pt; margin: 5px 0; font-weight: bold; }
        .page-break { page-break-after: always; }
        .content { text-align: justify; }
        .title-header { text-align: center; font-weight: bold; font-size: 14pt; margin-bottom: 20px; line-height: 1.2; }
        .title-header span { display: block; }

        /* ✅ JUDUL PASAL TETAP MENEMPEL KE PARAGRAF BAWAHNYA */
        .pasal-title {
            text-align: center;
            font-weight: bold;
            margin: 20px 0 10px 0;
            page-break-after: avoid;
        }

        /* ✅ PARAGRAF DAN LIST */
        p { margin-bottom: 10px; text-align: justify; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table td { vertical-align: top; padding: 2px 0; }

        /* LIST ANGKA BERKURUNG: (1) (2) (3) */
        .list-angka {
            list-style-type: none;
            padding-left: 40px;
            margin: 0;
        }
        .list-angka > li {
            position: relative;
            margin-bottom: 8px;
            text-align: justify;
            counter-increment: angka-counter;
        }
        .list-angka {
            counter-reset: angka-counter;
        }
        .list-angka > li::before {
            content: "(" counter(angka-counter) ")";
            position: absolute;
            left: -40px;
            width: 30px;
            text-align: right;
        }

        /* LIST HURUF KECIL: a. b. c. */
        .list-huruf {
            list-style-type: none;
            counter-reset: huruf-counter;
            padding-left: 40px;
            margin: 0;
        }
        .list-huruf > li {
            counter-increment: huruf-counter;
            position: relative;
            margin-bottom: 8px;
            text-align: justify;
        }
        .list-huruf > li::before {
            content: counter(huruf-counter, lower-alpha) ".";
            position: absolute;
            left: -30px;
            width: 20px;
            text-align: left;
        }

        /* LIST HURUF BESAR: A. B. C. (untuk segmen lokasi) */
        .list-huruf-besar {
            list-style-type: none;
            counter-reset: huruf-besar-counter;
            padding-left: 30px;
            margin: 0;
        }
        .list-huruf-besar > li {
            counter-increment: huruf-besar-counter;
            position: relative;
            margin-bottom: 8px;
            text-align: justify;
        }
        .list-huruf-besar > li::before {
            content: counter(huruf-besar-counter, upper-alpha) ".";
            position: absolute;
            left: -30px;
            width: 25px;
            text-align: left;
            font-weight: bold;
        }

        /* LIST ANGKA BIASA: 1. 2. 3. (untuk titik lokasi) */
        .list-titik-lokasi {
            list-style-type: decimal;
            padding-left: 25px;
            margin: 0 0 0 10px;
        }
        .list-titik-lokasi > li {
            margin-bottom: 3px;
            text-align: justify;
        }

        /* SUB-LIST HURUF di dalam list-angka */
        .sub-huruf {
            list-style-type: none;
            counter-reset: sub-huruf-counter;
            padding-left: 30px;
            margin: 0;
        }
        .sub-huruf > li {
            counter-increment: sub-huruf-counter;
            position: relative;
            margin-bottom: 8px;
            text-align: justify;
        }
        .sub-huruf > li::before {
            content: counter(sub-huruf-counter, lower-alpha) ".";
            position: absolute;
            left: -25px;
            width: 20px;
            text-align: left;
        }

        /* LIST ANGKA BIASA DI DALAM ATRIBUT: 1. 2. 3. */
        .list-atribut {
            list-style-type: decimal;
            padding-left: 25px;
            margin: 0 0 0 5px;
        }
        .list-atribut > li {
            margin-bottom: 3px;
            text-align: justify;
        }

        /* ✅ TANDA TANGAN */
        .signature-table { margin-top: 40px; page-break-inside: avoid; }

        /* ✅ CSS AREA VERIFIKASI QR PREMIUM */
        .verification-area {
            margin-top: 25px;
            text-align: right;
            page-break-inside: avoid;
        }

        .qr-container {
            display: inline-block;
            text-align: center;
            vertical-align: top;
            width: 100px;
        }

        .qr-text {
            font-size: 8pt;
            margin: 5px 0 0 0;
            font-style: italic;
            font-weight: normal;
        }
    </style>
</head>
<body>
    @php
        // ✅ LOGIKA STATUS JABATAN PIMPINAN
        $leaderTitle = ($agreement->leader->status_jabatan != 'tetap')
            ? ucwords($agreement->leader->status_jabatan) . '. Kepala'
            : 'Kepala';
        $leaderTitleUpper = strtoupper($leaderTitle);

        // ✅ FIX LOGIKA WAKTU
        $start = \Carbon\Carbon::parse($agreement->start_date);
        $end = \Carbon\Carbon::parse($agreement->end_date);
        $diffMonths = $start->diffInMonths($end->copy()->addDay());

        if($diffMonths < 1) {
            $val = $start->diffInDays($end) + 1;
            $unit = 'hari';
        } else {
            $val = round($diffMonths);
            $unit = 'bulan';
        }
        $words = ucwords(\App\Helpers\NumberToWords::convert($val));
    @endphp

    {{-- HALAMAN SAMPUL --}}
    <div class="cover-page">
        <div class="cover-logo-container">
            @php
                $logoPekanbaru = storage_path('images/pekanbaru.png');
                $logoDishub = storage_path('images/dishub.png');
            @endphp
            @if (file_exists($logoPekanbaru)) <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPekanbaru)) }}"> @endif
            @if (file_exists($logoDishub)) <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoDishub)) }}"> @endif
        </div>
        <div class="cover-title" style="margin-top: 40px;">
            <h1>KONTRAK PERJANJIAN KERJASAMA</h1>
            @if(in_array($agreement->jenis, ['draft', 'sementara']))
                <h1 style="margin-top: 5px;">{{ strtoupper($agreement->jenis) }}</h1>
            @endif
            <h1>PERJANJIAN KERJASAMA</h1>
            <h1>ANTARA</h1>
            <h1>DINAS PERHUBUNGAN KOTA PEKANBARU</h1>
            <h1>DENGAN</h1>
            <h1>MITRA KERJASAMA PERPARKIRAN</h1>
            <h1 style="margin-top: 30px;">TENTANG</h1>
            <h1>KONTRAK KERJASAMA PENGELOLAAN PERPARKIRAN</h1>
            <h1>DI KOTA PEKANBARU</h1>
            <h1 style="margin-top: 40px;">PEKANBARU</h1>
            <h1>TAHUN {{ $agreement->start_date->year }}</h1>
        </div>
    </div>
    <div class="page-break"></div>

    {{-- ISI KONTRAK --}}
    <div class="content">
        <div class="title-header">
            <span>PERJANJIAN KERJASAMA</span>
            <span>ANTARA</span>
            <span>DINAS PERHUBUNGAN KOTA PEKANBARU</span>
            <span>DENGAN</span>
            <span>MITRA KERJASAMA PERPARKIRAN</span>
            <span>TENTANG</span>
            <span>KONTRAK KERJASAMA PENGELOLAAN PERPARKIRAN DI KOTA PEKANBARU</span>
            <span style="font-weight: normal; margin-top: 10px;">Nomor : {{ $agreement->agreement_number }}</span>
            <hr style="border: 0; border-top: 2px solid #000; margin-top: 15px; margin-bottom: 15px;">
        </div>

        <p>Pada hari ini <strong>{{ $agreement->signed_date->translatedFormat('l') }}</strong> tanggal <strong>{{ ucwords(\App\Helpers\NumberToWords::convert($agreement->signed_date->format('d'))) }}</strong> bulan <strong>{{ $agreement->signed_date->translatedFormat('F') }}</strong> tahun <strong>{{ ucwords(\App\Helpers\NumberToWords::convert($agreement->signed_date->format('Y'))) }}</strong>, Kami yang bertanda tangan di bawah ini :</p>

        <table>
            <tr>
                <td width="30">I</td>
                <td width="100">Nama</td>
                <td width="10">:</td>
                <td><strong>{{ strtoupper($agreement->leader->user->name) }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $leaderTitle }} UPT Perparkiran</td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $uptProfile->address }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="padding-top: 5px;">Bertindak dalam jabatannya tersebut, untuk dan atas nama Pemerintah Kota Pekanbaru selanjutnya disebut <strong>PIHAK PERTAMA</strong>.</td>
            </tr>
        </table>

        <table style="margin-top: 10px;">
            <tr>
                <td width="30">II</td>
                <td width="100">Nama</td>
                <td width="10">:</td>
                <td><strong>{{ strtoupper($agreement->fieldCoordinator->user->name) }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan</td>
                <td>:</td>
                <td>Mitra Kerjasama Pengelolaan Perparkiran</td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $agreement->fieldCoordinator->address }}</td>
            </tr>
            <tr>
                <td></td>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $agreement->fieldCoordinator->id_card_number }}</td>
            </tr>
            <tr>
                <td></td>
                <td>No. HP</td>
                <td>:</td>
                <td>{{ $agreement->fieldCoordinator->phone_number ?? '-' }}</td>
            </tr>
        </table>
        
        <table>
            <tr>
                <td width="30"></td>
                <td colspan="3" style="padding-top: 5px;">Bertindak dalam jabatannya tersebut, untuk dan atas nama berkedudukan sebagai mitra kerjasama selanjutnya disebut <strong>PIHAK KEDUA</strong>.</td>
            </tr>
        </table>

        <div class="page-break"></div>

        <p><strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> (secara bersama-sama untuk selanjutnya disebut <strong>PARA PIHAK</strong>) menerangkan terlebih dahulu sebagai berikut:</p>
        <ol class="list-huruf">
            <li>Bahwa untuk mengoptimalkan pelayanan parkir di dalam ruang milik jalan dalam wilayah Kota Pekanbaru, PIHAK PERTAMA bermaksud untuk melakukan kerjasama Pengelolaan Perparkiran di Kota Pekanbaru.</li>
            <li>Bahwa PIHAK KEDUA merupakan Mitra Kerjasama Pengelolaan Perparkiran dan bermaksud untuk melakukan kerjasama Pengelolaan Perparkiran di wilayah Kota Pekanbaru, diterima baik oleh PIHAK PERTAMA.</li>
        </ol>

        <p>Berdasarkan hal-hal yang diuraikan tersebut diatas, maka PARA PIHAK setuju dan mufakat serta berkomitmen untuk membuat Perjanjian Kerjasama tentang Kerjasama Pengelolaan Perparkiran di wilayah Kota Pekanbaru dengan ketentuan-ketentuan sebagai berikut:</p>

        {{-- ==================== PASAL 1 ==================== --}}
        <div class="pasal-title">Pasal 1<br>Ruang Lingkup</div>
        <p>Ruang lingkup Perjanjian Kerjasama ini adalah meliputi :</p>
        <ol class="list-huruf">
            <li>Pengelolaan manajemen dan kegiatan layanan parkir di dalam ruang milik jalan pada titik/segmen yang telah ditentukan.</li>
            <li>Pemungutan tarif jasa layanan perparkiran didalam ruang milik jalan.</li>
        </ol>

        {{-- ==================== PASAL 2 ==================== --}}
        <div class="pasal-title">Pasal 2<br>Objek</div>
        <ol class="list-angka">
            <li>Objek Perjanjian Kerjasama ini adalah dalam rangka pelaksanaan kewenangan PIHAK PERTAMA yaitu Kerjasama Pengelolaan Perparkiran di dalam ruang milik jalan pada Wilayah Kota Pekanbaru yang dikuasai PIHAK PERTAMA yaitu:
                <ol class="list-huruf-besar" style="margin-top: 8px;">
                    @foreach ($agreement->activeParkingLocations->groupBy('roadSection.name') as $roadSectionName => $locations)
                        <li><strong>Segmen lokasi parkir di {{ $roadSectionName }}</strong>, pada titik :
                            <ol class="list-titik-lokasi">
                                @foreach ($locations as $location) <li>{{ $location->name }}.</li> @endforeach
                            </ol>
                        </li>
                    @endforeach
                </ol>
            </li>
        </ol>

        {{-- ==================== PASAL 3 ==================== --}}
        <div class="pasal-title">Pasal 3<br>Jangka Waktu</div>
        <p>Perjanjian Kerjasama ini berlaku untuk jangka waktu <strong>{{ $val }} ({{ $words }}) {{ $unit }}</strong> terhitung sejak ditandatanganinya Perjanjian Kerjasama ini, yaitu tanggal <strong>{{ $agreement->start_date->translatedFormat('d F Y') }}</strong> s/d <strong>{{ $agreement->end_date->translatedFormat('d F Y') }}</strong>. Kerjasama ini dapat diperpanjang sampai dengan selesainya penataan parkir dan diberlakukannya hasil survei potensi.</p>

        {{-- ==================== PASAL 4 ==================== --}}
        <div class="pasal-title">Pasal 4<br>Pembayaran</div>
        <ol class="list-angka">
            @if(in_array($agreement->jenis, ['draft', 'sementara']))
            <li>Pendapatan layanan parkir sebagaimana yang sudah ditetapkan dan tertuang didalam kontrak perjanjian kerjasama ini dengan jumlah setoran sebesar <strong>Rp. {{ number_format($agreement->daily_deposit_amount, 0, ',', '.') }},- ({{ ucwords(\App\Helpers\NumberToWords::convert(round($agreement->daily_deposit_amount))) }} Rupiah) / hari</strong> dan disetorkan langsung ke rekening pendapatan kas BLUD Perparkiran dengan nomor rekening : @if($activeBankAccount) <strong>{{ $activeBankAccount->account_number }} ({{ $activeBankAccount->bank_name }})</strong> atas nama <strong>{{ $activeBankAccount->account_name }}</strong> @endif dan bukti penyetoran disampaikan ke bendahara penerimaan untuk dilakukan validasi. Jumlah setoran tersebut bersifat sementara dan ditetapkan berdasarkan justifikasi pengurangan potensi terbaru, sampai dengan adanya penetapan kembali berdasarkan hasil evaluasi atau survei potensi parkir selanjutnya;</li>
            <li>Apabila terjadi keterlambatan penyetoran/kekurangan penyetoran maka PIHAK PERTAMA berhak melakukan pemotongan uang jaminan secara sepihak sebesar kekurangan setoran yang ditetapkan ke kas penampungan BLUD.</li>
            <li>Apabila PIHAK KEDUA tidak menyetorkan kewajiban 3 hari berturut-turut pertama maka PIHAK PERTAMA memberikan surat teguran tertulis I dan di ikuti penarikan setoran, selanjutnya dalam 3 hari berturut-turut kedua masih juga tidak dilakukan penyetoran maka dapat diberikan surat teguran II, dan dalam 3 hari berturut-turut ketiga tetap tidak melakukan penyetoran maka PIHAK PERTAMA memberikan surat teguran III sekaligus dengan pemutusan kerjasama;</li>
            <li>Dalam hal PIHAK KEDUA telah melakukan penyetoran kewajiban dalam jangka waktu 6 (enam) hari berturut-turut dengan lancar maka surat teguran I dinyatakan tidak berlaku dengan sendirinya;</li>
            <li>Selanjutnya PIHAK KEDUA telah melakukan penyetoran kewajiban dalam jangka waktu 2 (dua) minggu berturut-turut dengan lancar maka surat teguran II dinyatakan gugur dengan sendirinya.</li>
            @else
            <li>Jumlah setoran harian sebesar <strong>Rp. {{ number_format($agreement->daily_deposit_amount, 0, ',', '.') }},- ({{ ucwords(\App\Helpers\NumberToWords::convert(round($agreement->daily_deposit_amount))) }} Rupiah)</strong> disetorkan ke rekening Kas BLUD Perparkiran @if($activeBankAccount) <strong><em>{{ $activeBankAccount->account_number }} ({{ $activeBankAccount->bank_name }})</em></strong> a.n <strong><em>{{ $activeBankAccount->account_name }}</em></strong> @endif.</li>
            <li>PIHAK KEDUA wajib menyerahkan dana jaminan pelaksanaan sebesar jumlah nilai setoran selama jangka waktu kerjasama.</li>
            <li>Setoran harus dilakukan secara rutin dan bukti penyetoran disampaikan ke Bendahara Penerimaan untuk validasi.</li>
            @endif
        </ol>

        {{-- ==================== PASAL 5 ==================== --}}
        <div class="pasal-title">Pasal 5<br>Hak dan Kewajiban PIHAK PERTAMA</div>
        <ol class="list-angka">
            <li>PIHAK PERTAMA berhak :
                <ol class="sub-huruf">
                    <li>Memperoleh setoran tarif layanan parkir sebesar : <strong> Rp. {{ number_format($agreement->daily_deposit_amount, 0, ',', '.') }},- ({{ ucwords(\App\Helpers\NumberToWords::convert(round($agreement->daily_deposit_amount))) }} Rupiah) /hari.</strong></li>
                    <li>Melakukan pengawasan langsung atas pengelolaan dan pelayanan perparkiran yang dilaksanakan oleh PIHAK KEDUA;</li>
                    <li>Memutuskan Kontrak Kerjasama ini apabila PIHAK KEDUA dianggap tidak cakap dan dalam penilaian maka PIHAK PERTAMA kerjasama oprasional perparkiran tidak dapat dilaksanakan.</li>
                </ol>
            </li>
            <li>PIHAK PERTAMA berkewajiban :
                <ol class="sub-huruf">
                    <li>Menentukan dan menetapkan wilayah parkir yang akan dikelola oleh PIHAK KEDUA;</li>
                    <li>Memberikan data wilayah parkir yang akan dikelola oleh PIHAK KEDUA untuk dapat melakukan pengelolaan dan pelayanan parkir sesuai kewenangan PIHAK KEDUA;</li>
                    <li>Melakukan pengawasan terhadap pengelolaan dan pelayanan parkir oleh PIHAK KEDUA.</li>
                </ol>
            </li>
        </ol>

        {{-- ==================== PASAL 6 ==================== --}}
        <div class="pasal-title">Pasal 6<br>Hak dan Kewajiban PIHAK KEDUA</div>
        <ol class="list-angka">
            <li>PIHAK KEDUA berhak :
                <ol class="sub-huruf">
                    <li>Menerima data wilayah parkir yang diserahkan oleh PIHAK PERTAMA</li>
                    <li>Mengelola dan melakukan pelayanan parkir pada wilayah parkir yang telah disepakati bersama oleh PARA PIHAK;</li>
                    <li>Memperoleh keuntungan dari pengelolaan dan pelayanan parkir yang telah disepakati bersama oleh PARA PIHAK;</li>
                </ol>
            </li>
            <li>PIHAK KEDUA berkewajiban :
                <ol class="sub-huruf">
                    <li>Melaksanakan tugas pengelolaan parkir sesuai Peraturan Perundang – undangan yang berlaku;</li>
                    <li>Melakukan Pemungutan Jasa Layanan Perparkiran sesuai tarif yang telah ditentukan pada Peraturan Wali Kota Pekanbaru Nomor 02 Tahun 2025 tentang Peninjauan Tarif Retribusi Jasa Umum atas Pelayanan Parkir di Tepi Jalan Umum;</li>
                    <li>Menyetorkan hasil pungutan jasa layanan perparkiran setiap hari (1x24) jam secara non tunai ke rekening Kas BLUD UPT Perparkiran melalui Bendahara Penerimaan BLUD UPT Perparkiran Dinas Perhubungan Kota Pekanbaru;</li>
                    <li>Melaksanakan jasa layananan perparkiran sesuai dengan Standar Pelayanan Minimal (SPM) berdasarkan Peraturan Walikota Nomor 132 tahun 2020 tentang Standar Pelayanan Minimal Unit Pelaksana Teknis Perparkiran Dinas Perhubungan Kota Pekanbaru;</li>
                    <li>Melengkapi atribut juru parkir berupa :
                        <ol class="list-atribut">
                            <li>Buku Saku;</li>
                            <li>Rompi;</li>
                            <li>Peluit;</li>
                            <li>Karcis;</li>
                            <li>Topi;</li>
                            <li>Kartu Tanda Anggota (KTA);</li>
                            <li>Payung;</li>
                            <li>Jas Hujan, dan</li>
                            <li>Menyediakan Asuransi Keselamatan Kerja.</li>
                        </ol>
                    </li>
                    <li>Memberikan sosialisasi dan pembekalan kepada juru parkir tentang Jasa Layanan Parkir;</li>
                    <li>Menyediakan fasilitas pembayaran non tunai <em>(cashless)</em> seperti <em>e-ticket/e-payment/e-money/QRIS</em> sesuai dengan kebutuhan untuk kelancaran pelaksanaan pengelola parkir secara profesional;</li>
                    <li>Berkoordinasi dan melaporkan hasil pelaksanaan tugas kepada UPT Perparkiran secara berkala;</li>
                    <li>PIHAK KEDUA diwajibkan untuk melaporkan setiap penambahan potensi dan titik lokasi parkir yang berlaku.</li>
                </ol>
            </li>
        </ol>

        {{-- ==================== PASAL 7 ==================== --}}
        <div class="pasal-title">Pasal 7<br>Tarif Parkir</div>
        <p>PIHAK KEDUA memungut besaran tarif layanan parkir berdasarkan tarif layanan parkir yang ditetapkan yaitu Rp. 1000,- (seribu rupiah) untuk kendaraan roda 2 dan Rp. 2000,- (dua ribu rupiah) untuk kendaraan roda 4 dan Rp. 6.000,- (enam ribu rupiah) untuk roda 6.</p>

        {{-- ==================== PASAL 8 ==================== --}}
        <div class="pasal-title">Pasal 8<br>Pelayanan dan Pelaksanaan</div>
        <ol class="list-angka">
            <li>Dalam rangka pelaksanaan pelayanan parkir, PIHAK KEDUA wajib mempedomani Standar Pelayanan Minimal (SPM) pelayanan parkir yang ditetapkan oleh PIHAK PERTAMA;</li>
            <li>PIHAK PERTAMA melakukan monitoring dan evaluasi terhadap kinerja pelaksanaan pelayanan parkir yang dilakukan oleh PIHAK KEDUA sesuai SPM yang ditetapkan PIHAK PERTAMA;</li>
            <li>Dalam rangka monitoring dan evaluasi sebagaimana dimaksud pada ayat (2) pasal ini, PIHAK PERTAMA berhak mengakses data administrasi yang dikelola oleh PIHAK KEDUA;</li>
            <li>Dalam rangka peningkatan pelayanan parkir, juru parkir wajib menerapkan 3S (Sapa,Senyum,Salam) kepada pengguna jasa layanan parkir.</li>
        </ol>

        {{-- ==================== PASAL 9 ==================== --}}
        <div class="pasal-title">Pasal 9<br>Pendapatan</div>
        <ol class="list-angka">
            <li>Pendapatan yang diperoleh PIHAK KEDUA dari tarif layanan parkir wajib disetorkan kepada PIHAK PERTAMA secara non tunai melalui bendaraha penerimaan sebagaimana diatur dalam pasal 4 ayat (1);</li>
            <li>Apabila terjadi perubahan penambahan potensi pendapatan layanan parkir maka PARA PIHAK dapat menyesuaikan jumlah nilai setoran sebagaimana dimaksud pada ayat (1);</li>
        </ol>

        {{-- ==================== PASAL 10 ==================== --}}
        <div class="pasal-title">Pasal 10<br>Koordinasi</div>
        <p>Dalam rangka pelaksanaan Perjanjian Kerjasama ini akan dilakukan koordinasi antara PARA PIHAK paling kurang 1 (satu) minggu sekali dan/atau pada waktu tertentu yang disepakati oleh PARA PIHAK;</p>

        {{-- ==================== PASAL 11 ==================== --}}
        <div class="pasal-title">Pasal 11<br>Adendum</div>
        <p style="margin-bottom: 5px;">Adendum perjanjian kerjasama ini dapat dilakukan apabila :</p>
        <ol class="list-huruf">
            <li>Terjadinya perubahan kebijakan ketenagakerjaan;</li>
            <li>Terjadinya perubahan potensi parkir;</li>
            <li>Terjadinya perubahan Tarif Layanan Parkir;</li>
            <li>Hal-hal lain yang disepakati PARA PIHAK.</li>
        </ol>

        {{-- ==================== PASAL 12 ==================== --}}
        <div class="pasal-title">Pasal 12<br>Keadaan Memaksa (Force Majeure)</div>
        <ol class="list-angka">
            <li>Keadaan memaksa (<em>force majeure</em>) adalah keadaan yang terjadi diluar jangkauan dan kemauan PARA PIHAK seperti kerusuhan sosial, peperangan, kebakaran, peledakan, sabotase, badai, banjir, gempa bumi, tsunami yang mengakibatkan keterlambatan atau kegagalan salah satu Pihak dalam memenuhi kewajibannya sebagaimana tercantum dalam Perjanjian Kerjasama ini;</li>
            <li>Apabila terjadi <em>force majeure</em> sebagaimana dimaksud pada ayat (1) pasal ini maka Pihak yang mengalami <em>force majeure</em> wajib memberitahukan secara tertulis kepada Pihak lainnya selambat-lambatnya 7 (tujuh) hari kalender terhitung sejak tanggal terjadinya <em>force majeure</em>;</li>
            <li>Keterlambatan atau kelalaian atas pemberitahuan tersebut mengakibatkan tidak diakuinya peristiwa tersebut sebagai keadaan <em>force majeure</em>.</li>
        </ol>

        {{-- ==================== PASAL 13 ==================== --}}
        <div class="pasal-title">Pasal 13<br>Larangan</div>
        <p style="margin-bottom: 5px;">Selama Perjanjian Kerjasama ini berlaku, PIHAK KEDUA dilarang :</p>
        <ol class="list-huruf">
            <li>Melakukan pengelolaan perparkiran tidak sesuai dengan Peraturan Perundang-undangan yang berlaku;</li>
            <li>Memungut tarif layanan parkir melebihi besaran tarif layanan parkir yang berlaku yang telah diatur dalam Peraturan Perundang-undangan yang berlaku;</li>
            <li>PIHAK KEDUA tidak dibenarkan mengalihkan pegelolaan dan pemungutan jasa layanan perparkiran kepada pihak lain;</li>
            <li>PIHAK KEDUA tidak dibenarkan mengelola dan memungut jasa layanan perparkiran pada titik lokasi yang tidak tercantum dalam perjanjian kerjasama ini;</li>
            <li>Melaksanakan pelayanan parkir tidak berdasarkan Standar Pelayanan Minimal(SPM) yang ditetapkan PIHAK PERTAMA.</li>
        </ol>

        {{-- ==================== PASAL 14 ==================== --}}
        <div class="pasal-title">Pasal 14<br>Sanksi</div>
        <ol class="list-angka">
            <li>Dalam hal PIHAK KEDUA melanggar ketentuan sebagaimana dimaksud dalam Pasal 14 huruf c, maka PIHAK PERTAMA berhak memutus Perjanjian Kerjasama ini secara sepihak;</li>
            <li>Dalam hal PIHAK KEDUA melanggar ketentuan sebagaimana dimaksud dalam Pasal 14 huruf a, maka PIHAK KEDUA dikenakan sanksi dan ketentuan peraturan perundang-undangan yang berlaku;</li>
            <li>Dalam hal ayat (1) dan (2) terbukti dan terjadi pemutusan perjanjian kerjasama maka PIHAK PERTAMA berhak mengambil alih secara utuh pengelolaan pelayanan parkir didalam ruang milik jalan sesuai peraturan perundang-undangan yang berlaku;</li>
        </ol>

        {{-- ==================== PASAL 15 ==================== --}}
        <div class="pasal-title">Pasal 15<br>Berakhirnya Perjanjian Kerjasama</div>
        <ol class="list-angka">
            <li>Perjanjian Kerjasama ini dapat berakhir disebabkan oleh :
                <ol class="sub-huruf">
                    <li>Berakhirnya jangka waktu;</li>
                    <li>Diputus oleh salah satu Pihak; dan</li>
                    <li>Terjadinya keadaan memaksa.</li>
                </ol>
            </li>
        </ol>
        <ol class="list-huruf">
            <li>PIHAK KEDUA melanggar ketentuan sebagaimana dimaksud dalam Pasal 15.</li>
        </ol>
        <ol class="list-angka" style="counter-reset: angka-counter 1;">
            <li>Pemutusan Perjanjian Kerjasama sebagaimana dimaksud ayat (1) huruf b, dilakukan oleh PIHAK KEDUA dalam hal PIHAK PERTAMA tidak dapat melaksanakan kewajiban sebagaimana diatur dalam Perjanjian Kerjasama ini;</li>
            <li>Jika dikemudian hari terjadi pemutusan kerjasama sebagaimana dimaksud ayat (1) maka PIHAK KEDUA tidak dapat menuntut secara hukum yang berlaku.</li>
        </ol>

        {{-- ==================== PASAL 16 ==================== --}}
        <div class="pasal-title">Pasal 16<br>Perselisihan</div>
        <ol class="list-angka">
            <li>Segala perbedaan pendapat atau perselisihan yang timbul dalam Perjanjian Kerjasama ini akan diselesaikan secara musyawarah dan mufakat oleh PARA PIHAK;</li>
        </ol>

        {{-- ==================== PASAL 17 ==================== --}}
        <div class="pasal-title">Pasal 17<br>Lain-lain</div>
        <ol class="list-angka">
            <li>Segala sesuatu yang belum atau tidak cukup diatur dalam Perjanjian Kerjasama ini akan dituangkan dalam suatu perjanjian tambahan (addendum) tersendiri yang merupakan satu kesatuan yang tidak dapat terpisahkan dengan Perjanjian Kerjasama ini dan mempunyai kekuatan hukum yang sama;</li>
            <li>Perjanjian Kerjasama ini tetap berlaku walaupun terjadi perubahan kepemimpinan/jabatan dan bentuk badan hukum pada salah satu Pihak</li>
            <li>Pengajuan untuk perpanjangan Perjanjian Kerjasama wajib diajukan 10 (sepuluh) hari sebelum kerjasama ini berakhir;</li>
            <li>Dalam pengambilan keputusan terkait pengajuan,penetapan dan pengelolaan titik lokasi parkir kewenangan sepenuhnya berada di Dinas Perhubungan Kota Pekanbaru melalui BLUD UPT Perparkiran.</li>
        </ol>

        {{-- ==================== PASAL 18 ==================== --}}
        <div class="pasal-title">Pasal 18<br>Penutup</div>
        <p>Demikian Perjanjian Kerjasama ini dibuat dan ditandatangani di Pekanbaru pada hari dan tanggal tersebut di atas dalam rangkap 2 (dua) bermaterai cukup,masing-masing mempunyai kekuatan hukum yang sama.</p>

        <div class="signature-table">
            <table width="100%">
                <tr>
                    <td width="50%" align="center">
                        PIHAK PERTAMA,<br>
                        <strong>{{ $leaderTitleUpper }} UPT PERPARKIRAN<br>SELAKU PIMPINAN BLUD</strong>
                        <br><br><br><br><br>
                        <strong><u>{{ strtoupper($agreement->leader->user->name) }}</u></strong><br>
                        NIP. {{ formatNip($agreement->leader->employee_number) }}
                    </td>
                    <td width="50%" align="center">
                        PIHAK KEDUA,<br>
                        <strong>MITRA KERJA SAMA</strong>
                        <br><br><br><br><br><br>
                        <strong><u>{{ strtoupper($agreement->fieldCoordinator->user->name) }}</u></strong>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ✅ TAMPILKAN QR CODE DARI CONTROLLER --}}
        @if (!empty($qrCodeImage))
        <div class="verification-area">
            <div class="qr-container">
                <img src="data:image/png;base64,{{ $qrCodeImage }}" width="90" height="90" style="display: block; margin: 0 auto;">
                <p class="qr-text">Pindai Verifikasi</p>
            </div>
        </div>
        @endif

        {{-- ✅ PARAF PIHAK DI SETIAP HALAMAN KECUALI COVER & LAST PAGE --}}
        <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_script('
                    // $PAGE_NUM adalah halaman saat ini, $PAGE_COUNT adalah total halaman
                    if ($PAGE_NUM > 1 && $PAGE_NUM < $PAGE_COUNT) {
                        $font = $fontMetrics->get_font("Helvetica", "normal");
                        $size = 9;
                        $color = array(0, 0, 0); // Hitam
                        
                        $w = 100; // Lebar total (50px per kolom)
                        $h = 45; // Tinggi total
                        $x = $pdf->get_width() - $w - 50; // Jarak dari kanan
                        $y = $pdf->get_height() - $h - 28; // Jarak dari bawah
                        
                        // Garis luar kotak
                        $pdf->rectangle($x, $y, $w, $h, $color, 0.5);
                        // Garis horizontal pemisah judul dan kotak paraf
                        $pdf->line($x, $y + 15, $x + $w, $y + 15, $color, 0.5);
                        // Garis vertikal tengah
                        $pdf->line($x + ($w / 2), $y, $x + ($w / 2), $y + $h, $color, 0.5);
                        
                        // Teks (Pihak I dan Pihak II)
                        $pdf->text($x + 10, $y + 4, "Pihak I", $font, $size, $color);
                        $pdf->text($x + 58, $y + 4, "Pihak II", $font, $size, $color);
                    }
                ');
            }
        </script>
    </div>

</body>
</html>
