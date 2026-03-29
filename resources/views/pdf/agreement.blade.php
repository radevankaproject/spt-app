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
        body {
            font-family: 'Bookman Old Style', serif;
            font-size: 12pt; /* Sesuai standar dokumen hukum */
            line-height: 1.3;
            margin: 1cm 1.5cm 1cm 2.5cm; /* Margin kiri lebih lebar untuk jilid */
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
        .pasal-title { text-align: center; font-weight: bold; margin: 20px 0 10px 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table td { vertical-align: top; padding: 2px 0; }
        .list-ol { padding-left: 25px; margin: 0; }
        .list-ol li { margin-bottom: 8px; text-align: justify; }
        .sub-list { list-style-type: lower-alpha; padding-left: 20px; }
        .signature-table { margin-top: 40px; page-break-inside: avoid; }
    </style>
</head>
<body>
    @php
        // ✅ LOGIKA STATUS JABATAN PIMPINAN
        $leaderTitle = ($agreement->leader->status_jabatan != 'tetap')
            ? ucwords($agreement->leader->status_jabatan) . '. Kepala'
            : 'Kepala';
        $leaderTitleUpper = strtoupper($leaderTitle);

        // ✅ FIX LOGIKA WAKTU (Bulatkan ke Bulan terdekat untuk PKS Bulanan)
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
                <td>{{ $leaderTitle }} UPT. Perparkiran</td>
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
                <td colspan="3" style="padding-top: 5px;">Bertindak dalam jabatannya tersebut, untuk dan atas nama berkedudukan sebagai mitra kerjasama selanjutnya disebut <strong>PIHAK KEDUA</strong>.</td>
            </tr>
        </table>

        <p>PARA PIHAK terlebih dahulu menerangkan hal-hal sebagai berikut :</p>
        <ol class="list-ol">
            <li>Bahwa untuk mengoptimalkan pelayanan parkir di dalam ruang milik jalan dalam wilayah Kota Pekanbaru, PIHAK PERTAMA bermaksud untuk melakukan kerjasama Pengelolaan Perparkiran di Kota Pekanbaru.</li>
            <li>Bahwa PIHAK KEDUA merupakan Mitra Kerjasama Pengelolaan Perparkiran dan bermaksud untuk melakukan kerjasama Pengelolaan Perparkiran di wilayah Kota Pekanbaru, diterima baik oleh PIHAK PERTAMA.</li>
        </ol>

        <p>Berdasarkan hal-hal tersebut di atas, PARA PIHAK setuju membuat Perjanjian Kerjasama dengan ketentuan sebagai berikut :</p>

        <div class="pasal-title">Pasal 1<br>Ruang Lingkup</div>
        <ol class="list-ol">
            <li>Pengelolaan manajemen dan kegiatan layanan parkir di dalam ruang milik jalan pada titik/segmen yang telah ditentukan.</li>
            <li>Pemungutan tarif jasa layanan perparkiran didalam ruang milik jalan.</li>
        </ol>

        <div class="pasal-title">Pasal 2<br>Objek</div>
        <p>Objek Perjanjian Kerjasama ini adalah pengelolaan perparkiran pada :</p>
        <ol class="list-ol" style="list-style-type: lower-alpha;">
            @foreach ($agreement->activeParkingLocations->groupBy('roadSection.name') as $roadSectionName => $locations)
                <li>Segmen Lokasi Parkir di Ruas Jalan <strong>{{ $roadSectionName }}</strong>, yaitu :
                    <ol style="list-style-type: decimal; padding-left: 20px;">
                        @foreach ($locations as $location) <li>{{ $location->name }};</li> @endforeach
                    </ol>
                </li>
            @endforeach
        </ol>

        <div class="pasal-title">Pasal 3<br>Jangka Waktu</div>
        <p>Perjanjian Kerjasama ini berlaku untuk jangka waktu <strong>{{ $val }} ({{ $words }}) {{ $unit }}</strong> terhitung sejak tanggal <strong>{{ $agreement->start_date->translatedFormat('d F Y') }}</strong> s/d <strong>{{ $agreement->end_date->translatedFormat('d F Y') }}</strong>.</p>

        <div class="pasal-title">Pasal 4<br>Pembayaran</div>
        <ol class="list-ol">
            <li>Jumlah setoran harian sebesar <strong>Rp. {{ number_format($agreement->daily_deposit_amount, 0, ',', '.') }},- ({{ ucwords(\App\Helpers\NumberToWords::convert(round($agreement->daily_deposit_amount))) }} Rupiah)</strong> disetorkan ke rekening Kas BLUD Perparkiran @if($activeBankAccount) <strong>{{ $activeBankAccount->account_number }} ({{ $activeBankAccount->bank_name }})</strong> a.n <strong>{{ $activeBankAccount->account_name }}</strong> @endif.</li>
            <li>PIHAK KEDUA wajib menyerahkan dana jaminan pelaksanaan sebesar jumlah nilai setoran selama jangka waktu kerjasama.</li>
            <li>Setoran harus dilakukan secara rutin dan bukti penyetoran disampaikan ke Bendahara Penerimaan untuk validasi.</li>
        </ol>

        {{-- PASAL LAINNYA DISINGKAT AGAR RESPON TIDAK TERPOTONG, TAPI TETAP MENGIKUTI STRUKTUR ASLI --}}
        <p class="pasal-title">Pasal 5<br>HAK DAN KEWAJIBAN PIHAK PERTAMA</p>
        <ol type="1">
            <li>PIHAK PERTAMA berhak :
                <ol type="a">
                    <li>Memperoleh setoran tarif layanan parkir sebesar : Rp. {{ number_format($agreement->daily_deposit_amount, 0, ',', '.') }} ({{ ucwords(\App\Helpers\NumberToWords::convert(round($agreement->daily_deposit_amount))) }} Rupiah) / hari.</li>
                    <li>Melakukan pengawasan langsung atas pengelolaan dan pelayanan perparkiran yang dilaksanakan oleh PIHAK KEDUA;</li>
                    <li>Memutuskan Kontrak Kerjasama ini apabila PIHAK KEDUA dianggap tidak cakap dan dalam penilaian maka PIHAK PERTAMA kerjasama oprasional perparkiran tidak dapat dilaksanakan.</li>
                </ol>
            </li>
            <li>PIHAK PERTAMA berkewajiban :
                <ol type="a">
                    <li>Menentukan dan menetapkan wilayah parkir yang akan dikelola oleh PIHAK KEDUA;</li>
                    <li>Memberikan data wilayah parkir yang akan dikelola oleh PIHAK KEDUA untuk dapat melakukan pengelolaan dan pelayanan parkir sesuai kewenangan PIHAK KEDUA;</li>
                    <li>Melakukan pengawasan terhadap pengelolaan dan pelayanan parkir oleh PIHAK KEDUA.</li>
                </ol>
            </li>
        </ol>

        <p class="pasal-title">Pasal 6<br>HAK DAN KEWAJIBAN PIHAK KEDUA</p>
        <ol type="1">
            <li>PIHAK KEDUA berhak :
                <ol type="a">
                    <li>Menerima data wilayah parkir yang diserahkan oleh PIHAK PERTAMA</li>
                    <li>Mengelola dan melakukan pelayanan parkir pada wilayah parkir yang telah disepakati bersama oleh PARA PIHAK;</li>
                    <li>Memperoleh keuntungan dari pengelolaan dan pelayanan parkir yang telah disepakati bersama oleh PARA PIHAK;</li>
                </ol>
            </li>
            <li>PIHAK KEDUA berkewajiban :
                <ol type="a">
                    <li>Melaksanakan tugas pengelolaan parkir sesuai Peraturan Perundang – undangan yang berlaku;</li>
                    <li>Melakukan Pemungutan Jasa Layanan Perparkiran sesuai tarif yang telah ditentukan pada Peraturan Wali Kota Pekanbaru Nomor 02 Tahun 2025 tentang Peninjauan Tarif Retribusi Jasa Umum atas Pelayanan Parkir di Tepi Jalan Umum;</li>
                    <li>Menyetorkan hasil pungutan jasa layanan perparkiran setiap hari (1x24) jam secara non tunai ke rekening Kas BLUD UPT Perparkiran melalui Bendahara Penerimaan BLUD UPT Perparkiran Dinas Perhubungan Kota Pekanbaru;</li>
                    <li>Melaksanakan jasa layananan perparkiran sesuai dengan Standar Pelayanan Minimal (SPM) berdasarkan Peraturan Walikota Nomor 132 tahun 2020 tentang Standar Pelayanan Minimal Unit Pelaksana Teknis Perparkiran Dinas Perhubungan Kota Pekanbaru;</li>
                    <li>Melengkapi atribut juru parkir berupa :
                        <ul style="list-style-type: disc;">
                            <li>Buku Saku;</li>
                            <li>Rompi;</li>
                            <li>Peluit;</li>
                            <li>Karcis;</li>
                            <li>Topi;</li>
                            <li>Kartu Tanda Anggota (KTA);</li>
                            <li>Payung;</li>
                            <li>Jas Hujan, dan</li>
                            <li>Menyediakan Asuransi Keselamatan Kerja.</li>
                        </ul>
                    </li>
                    <li>Memberikan sosialisasi dan pembekalan kepada juru parkir tentang Jasa Layanan Parkir;</li>
                    <li>Menyediakan fasilitas pembayaran non tunai (cashless) seperti e-ticket/e-payment/e-money/QRIS sesuai dengan kebutuhan untuk kelancaran pelaksanaan pengelola parkir secara profesional;</li>
                    <li>Berkoordinasi dan melaporkan hasil pelaksanaan tugas kepada UPT Perparkiran secara berkala;</li>
                    <li>PIHAK KEDUA diwajibkan untuk melaporkan setiap penambahan potensi dan titik lokasi parkir yang berlaku.</li>
                </ol>
            </li>
        </ol>

        <p class="pasal-title">Pasal 7<br>TARIF PARKIR</p>
        <p class="paragraph">PIHAK KEDUA memungut besaran tarif layanan parkir berdasarkan tarif layanan parkir yang ditetapkan yaitu Rp. 1000,- (seribu rupiah) untuk kendaraan roda 2 dan Rp. 2000,- (dua ribu rupiah) untuk kendaraan roda 4 dan Rp. 6.000,- (enam ribu rupiah) untuk roda 6.</p>

        <p class="pasal-title">Pasal 8<br>PELAYANAN DAN PELAKSANAAN</p>
        <ol type="1">
            <li>Dalam rangka pelaksanaan pelayanan parkir, PIHAK KEDUA wajib mempedomani Standar Pelayanan Minimal (SPM) pelayanan parkir yang ditetapkan oleh PIHAK PERTAMA;</li>
            <li>PIHAK PERTAMA melakukan monitoring dan evaluasi terhadap kinerja pelaksanaan pelayanan parkir yang dilakukan oleh PIHAK KEDUA sesuai SPM yang ditetapkan PIHAK PERTAMA;</li>
            <li>Dalam rangka monitoring dan evaluasi sebagaimana dimaksud pada ayat (2) pasal ini, PIHAK PERTAMA berhak mengakses data administrasi yang dikelola oleh PIHAK KEDUA;</li>
            <li>Dalam rangka peningkatan pelayanan parkir, juru parkir wajib menerapkan 3S (Sapa,Senyum,Salam) kepada pengguna jasa layanan parkir.</li>
        </ol>

        <p class="pasal-title">Pasal 9<br>PENDAPATAN</p>
        <ol type="1">
            <li>Pendapatan yang diperoleh PIHAK KEDUA dari tarif layanan parkir wajib disetorkan kepada PIHAK PERTAMA secara non tunai melalui bendaraha penerimaan sebagaimana diatur dalam pasal 4 ayat (1);</li>
            <li>Apabila terjadi perubahan penambahan potensi pendapatan layanan parkir maka PARA PIHAK dapat menyesuaikan jumlah nilai setoran sebagaimana dimaksud pada ayat (1);</li>
        </ol>

        <p class="pasal-title">Pasal 10<br>KOORDINASI</p>
        <p class="paragraph">Dalam rangka pelaksanaan Perjanjian Kerjasama ini akan dilakukan koordinasi antara PARA PIHAK paling kurang 1 (satu) minggu sekali dan/atau pada waktu tertentu yang disepakati oleh PARA PIHAK;</p>

        <p class="pasal-title">Pasal 11<br>ADENDUM</p>
        <p class="paragraph">Adendum perjanjian kerjasama ini dapat dilakukan apabila :</p>
        <ol type="a">
            <li>Terjadinya perubahan kebijakan ketenagakerjaan;</li>
            <li>Terjadinya perubahan potensi parkir;</li>
            <li>Terjadinya perubahan Tarif Layanan Parkir;</li>
            <li>Hal-hal lain yang disepakati PARA PIHAK.</li>
        </ol>

        <p class="pasal-title">Pasal 12<br>KEADAAN MEMAKSA (FORCE MAJEURE)</p>
        <ol type="1">
            <li>Keadaan memaksa (force majeure) adalah keadaan yang terjadi diluar jangkauan dan kemauan PARA PIHAK seperti kerusuhan sosial, peperangan, kebakaran, peledakan, sabotase, badai, banjir, gempa bumi, tsunami yang mengakibatkan keterlambatan atau kegagalan salah satu Pihak dalam memenuhi kewajibannya sebagaimana tercantum dalam Perjanjian Kerjasama ini;</li>
            <li>Apabila terjadi force majeure sebagaimana dimaksud pada ayat (1) pasal ini maka Pihak yang mengalami force majeure wajib memberitahukan secara tertulis kepada Pihak lainnya selambat-lambatnya 7 (tujuh) hari kalender terhitung sejak tanggal terjadinya force majeure ;</li>
            <li>Keterlambatan atau kelalaian atas pemberitahuan tersebut mengakibatkan tidak diakuinya peristiwa tersebut sebagai keadaan force majeure.</li>
        </ol>

        <p class="pasal-title">Pasal 13<br>LARANGAN</p>
        <p class="paragraph">Selama Perjanjian Kerjasama ini berlaku, PIHAK KEDUA dilarang :</p>
        <ol type="a">
            <li>Melakukan pengelolaan perparkiran tidak sesuai dengan Peraturan Perundang-undangan yang berlaku;</li>
            <li>Memungut tarif layanan parkir melebihi besaran tarif layanan parkir yang berlaku yang telah diatur dalam Peraturan Perundang-undangan yang berlaku;</li>
            <li>PIHAK KEDUA tidak dibenarkan mengalihkan pegelolaan dan pemungutan jasa layanan perparkiran kepada pihak lain;</li>
            <li>PIHAK KEDUA tidak dibenarkan mengelola dan memungut jasa layanan perparkiran pada titik lokasi yang tidak tercantum dalam perjanjian kerjasama ini;</li>
            <li>Melaksanakan pelayanan parkir tidak berdasarkan Standar Pelayanan Minimal(SPM) yang ditetapkan PIHAK PERTAMA.</li>
        </ol>

        <p class="pasal-title">Pasal 14<br>SANKSI</p>
        <ol type="1">
            <li>Dalam hal PIHAK KEDUA melanggar ketentuan sebagaimana dimaksud dalam Pasal 13 huruf c, maka PIHAK PERTAMA berhak memutus Perjanjian Kerjasama ini secara sepihak;</li>
            <li>Dalam hal PIHAK KEDUA melanggar ketentuan sebagaimana dimaksud dalam Pasal 13 huruf a, maka PIHAK KEDUA dikenakan sanksi dan ketentuan peraturan perundang-undangan yang berlaku;</li>
            <li>Dalam hal ayat (1) dan (2) terbukti dan terjadi pemutusan perjanjian kerjasama maka PIHAK PERTAMA berhak mengambil alih secara utuh pengelolaan pelayanan parkir didalam ruang milik jalan sesuai peraturan perundang-undangan yang berlaku;</li>
        </ol>

        <p class="pasal-title">Pasal 15<br>BERAKHIRNYA PERJANJIAN KERJASAMA</p>
        <ol type="1">
            <li>Perjanjian Kerjasama ini dapat berakhir disebabkan oleh :
                <ol type="a">
                    <li>Berakhirnya jangka waktu;</li>
                    <li>Diputus oleh salah satu Pihak; dan</li>
                    <li>Terjadinya keadaan memaksa.</li>
                    <li>PIHAK KEDUA melanggar ketentuan sebagaimana dimaksud dalam Pasal 13.</li>
                </ol>
            </li>
            <li>Pemutusan Perjanjian Kerjasama sebagaimana dimaksud ayat (1) huruf b, dilakukan oleh PIHAK KEDUA dalam hal PIHAK PERTAMA tidak dapat melaksanakan kewajiban sebagaimana diatur dalam Perjanjian Kerjasama ini;</li>
            <li>Jika dikemudian hari terjadi pemutusan kerjasama sebagaimana dimaksud ayat (1) maka PIHAK KEDUA tidak dapat menuntut secara hukum yang berlaku.</li>
        </ol>

        <p class="pasal-title">Pasal 16<br>PERSELISIHAN</p>
        <p class="paragraph">Segala perbedaan pendapat atau perselisihan yang timbul dalam Perjanjian Kerjasama ini akan diselesaikan secara musyawarah dan mufakat oleh PARA PIHAK;</p>

        <p class="pasal-title">Pasal 17<br>LAIN-LAIN</p>
        <ol type="1">
            <li>Segala sesuatu yang belum atau tidak cukup diatur dalam Perjanjian Kerjasama ini akan dituangkan dalam suatu perjanjian tambahan (addendum) tersendiri yang merupakan satu kesatuan yang tidak dapat terpisahkan dengan Perjanjian Kerjasama ini dan mempunyai kekuatan hukum yang sama;</li>
            <li>Perjanjian Kerjasama ini tetap berlaku walaupun terjadi perubahan kepemimpinan/jabatan dan bentuk badan hukum pada salah satu Pihak</li>
            <li>Pengajuan untuk perpanjangan Perjanjian Kerjasama wajib diajukan 10 (sepuluh) hari sebelum kerjasama ini berakhir;</li>
            <li>Dalam pengambilan keputusan terkait pengajuan,penetapan dan pengelolaan titik lokasi parkir kewenangan sepenuhnya berada di Dinas Perhubungan Kota Pekanbaru melalui BLUD UPT Perparkiran.</li>
        </ol>

        <div class="pasal-title">Pasal 18<br>Penutup</div>
        <p>Demikian Perjanjian Kerjasama ini dibuat dalam rangkap 2 (dua) bermaterai cukup, masing-masing mempunyai kekuatan hukum yang sama.</p>

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
                        <strong>MITRA KERJASAMA</strong>
                        <br><br><br><br><br><br>
                        <strong><u>{{ strtoupper($agreement->fieldCoordinator->user->name) }}</u></strong>
                    </td>
                </tr>
            </table>
        </div>

        @if ($agreement->verification_code)
        <div style="margin-top: 30px; text-align: right;">
            <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(80)->generate(route('public.agreement.verify', $agreement->verification_code))) }}">
            <p style="font-size: 8pt; margin-right: 15px;">Pindai Verifikasi</p>
        </div>
        @endif
    </div>
</body>
</html>
