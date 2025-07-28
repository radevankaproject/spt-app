<!DOCTYPE html>
<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>PKS - {{ $agreement->agreement_number }}</title>
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
                font-size: 11pt;
                line-height: 1.6;
                /* Sedikit lebih renggang untuk kenyamanan membaca */
                margin: 1.5cm;
                color: #333;
                /* Warna teks utama sedikit lebih lembut dari hitam pekat */
            }

            .cover-page {
                text-align: center;
                height: 90vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .cover-logo-container img {
                height: 100px;
                margin: 0 30px;
            }

            .cover-page h1,
            .cover-page h2,
            .cover-page h3,
            .cover-page p {
                font-weight: bold;
                margin: 5px 0;
            }

            .cover-page h1 {
                font-size: 18pt;
                margin-top: 50px;
                text-transform: uppercase;
            }

            .cover-page h2 {
                font-size: 16pt;
                text-transform: uppercase;
            }

            .cover-page h3 {
                font-size: 14pt;
                text-transform: uppercase;
            }

            .cover-page .cover-year {
                margin-top: 50px;
            }

            .page-break {
                page-break-after: always;
            }

            .content {
                text-align: justify;
            }

            .title {
                text-align: center;
                font-weight: bold;
                text-decoration: underline;
                text-transform: uppercase;
                font-size: 14pt;
                margin-bottom: 5px;
            }

            .number {
                text-align: center;
                margin-bottom: 25px;
            }

            .paragraph {
                margin-bottom: 12px;
            }

            .pasal-title {
                text-align: center;
                font-weight: bold;
                margin-top: 25px;
                margin-bottom: 15px;
            }

            .font-bold {
                font-weight: bold;
            }

            .underline {
                text-decoration: underline;
            }

            table.no-border {
                width: 100%;
                border: none;
            }

            table.no-border td {
                border: none;
                padding: 1.5px 0;
                vertical-align: top;
            }

            .list {
                padding-left: 25px;
                margin-left: 25px;
            }

            .list li,
            .sub-list li {
                margin-bottom: 6px;
            }

            .sub-list {
                padding-left: 25px;
            }

            .signature-block {
                margin-top: 60px;
                width: 100%;
            }

            .signature-column {
                display: inline-block;
                width: 49%;
                text-align: center;
                vertical-align: top;
            }
        </style>
    </head>

    <body>
        {{-- Halaman Sampul --}}
        <div class="cover-page">
            <div class="cover-logo-container">
                <img src="{{ public_path('assets/images/pekanbaru.png') }}" alt="Logo Pekanbaru">
                <img src="{{ public_path('assets/images/dishub.png') }}" alt="Logo Dishub">
            </div>
            <h1>PERJANJIAN KERJASAMA</h1>
            <h2>ANTARA</h2>
            <h2>DINAS PERHUBUNGAN KOTA PEKANBARU</h2>
            <h3>DENGAN</h3>
            <h3>{{ $agreement->fieldCoordinator->user->name ?? 'MITRA KERJASAMA' }}</h3>
            <h3>TENTANG</h3>
            <h3>PENGELOLAAN JASA LAYANAN PARKIR TEPI JALAN UMUM</h3>
            <p>NOMOR : {{ $agreement->agreement_number }}</p>
            <p class="cover-year">TAHUN {{ $agreement->signed_date->format('Y') }}</p>
        </div>
        <div class="page-break"></div>

        {{-- Konten Perjanjian --}}
        <div class="content">
            <p class="title">PERJANJIAN KERJASAMA</p>
            <p class="number">Nomor : {{ $agreement->agreement_number }}</p>

            <p>Pada hari ini {{ $agreement->signed_date->translatedFormat('l') }} tanggal
                {{ \App\Helpers\NumberToWords::convert($agreement->signed_date->format('d')) }} bulan
                {{ $agreement->signed_date->translatedFormat('F') }} tahun
                {{ \App\Helpers\NumberToWords::convert($agreement->signed_date->format('Y')) }}, bertempat di
                Pekanbaru, yang bertanda tangan di bawah ini:
            </p>

            <table class="no-border" style="margin-top: 15px;">
                <tr>
                    <td style="width: 5%;">I.</td>
                    <td style="width: 25%;">Nama</td>
                    <td style="width: 2%;">:</td>
                    <td class="font-bold">{{ $agreement->leader->user->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>NIP</td>
                    <td>:</td>
                    <td>{{ formatNip($agreement->leader->employee_number) ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>Plt. Kepala {{ $uptProfile->name }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $uptProfile->address }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" style="padding-top: 10px;">Dalam hal ini bertindak dalam jabatannya tersebut
                        untuk dan atas nama UPT Perparkiran Dinas Perhubungan Kota Pekanbaru, yang selanjutnya disebut
                        sebagai <span class="font-bold">PIHAK PERTAMA</span>.</td>
                </tr>
            </table>

            <table class="no-border" style="margin-top: 15px;">
                <tr>
                    <td style="width: 5%;">II.</td>
                    <td style="width: 25%;">Nama</td>
                    <td style="width: 2%;">:</td>
                    <td class="font-bold">{{ $agreement->fieldCoordinator->user->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>NIK</td>
                    <td>:</td>
                    <td>{{ $agreement->fieldCoordinator->id_card_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $agreement->fieldCoordinator->address ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>No. Telepon/HP</td>
                    <td>:</td>
                    <td>{{ $agreement->fieldCoordinator->phone_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" style="padding-top: 10px;">Dalam hal ini bertindak untuk dan atas nama diri
                        sendiri, yang selanjutnya disebut sebagai <span class="font-bold">PIHAK KEDUA</span>.</td>
                </tr>
            </table>

            <p style="margin-top: 15px;">PIHAK PERTAMA dan PIHAK KEDUA secara bersama-sama disebut Para Pihak, sepakat
                untuk mengadakan Perjanjian Kerjasama Pengelolaan Jasa Layanan Parkir di Tepi Jalan Umum (selanjutnya
                disebut Perjanjian) dengan syarat-syarat dan ketentuan sebagai berikut:</p>

            <p class="pasal-title">Pasal 1<br>MAKSUD DAN TUJUAN</p>
            <p class="paragraph">PIHAK PERTAMA menunjuk PIHAK KEDUA dan PIHAK KEDUA menerima penunjukan tersebut untuk
                melaksanakan Pengelolaan Jasa Layanan Parkir di Tepi Jalan Umum pada lokasi yang telah ditetapkan.</p>

            <p class="pasal-title">Pasal 2<br>OBJEK PERJANJIAN</p>
            <p class="paragraph">Objek perjanjian ini adalah pengelolaan jasa layanan parkir pada ruas jalan sebagai
                berikut:</p>
            <ol type="A" class="list">
                @foreach ($agreement->activeParkingLocations->groupBy('roadSection.name') as $roadSectionName => $locations)
                    <li>Segmen Lokasi Parkir di <span class="font-bold">Jalan {{ $roadSectionName }}</span>, pada titik
                        lokasi:
                        <ol class="sub-list" style="list-style-type: decimal;">
                            @foreach ($locations as $location)
                                <li>{{ $location->name }};</li>
                            @endforeach
                        </ol>
                    </li>
                @endforeach
            </ol>

            <p class="pasal-title">Pasal 3<br>JANGKA WAKTU</p>
            <p class="paragraph">Perjanjian ini berlaku untuk jangka waktu
                {{ $agreement->start_date->diffInMonths($agreement->end_date) }}
                ({{ \App\Helpers\NumberToWords::convert($agreement->start_date->diffInMonths($agreement->end_date)) }})
                bulan, terhitung sejak tanggal {{ $agreement->start_date->translatedFormat('d F Y') }} sampai dengan
                tanggal {{ $agreement->end_date->translatedFormat('d F Y') }}.
            </p>

            <p class="pasal-title">Pasal 4<br>PEMBAYARAN</p>
            <p class="paragraph">
                Pendapatan layanan parkir sebagaimana yang sudah ditetapkan dan tertuang didalam kontrak perjanjian
                kerjasama ini dengan jumlah setoran sebesar
                <span class="font-bold">Rp. {{ number_format($agreement->daily_deposit_amount, 0, ',', '.') }},-
                    ({{ \App\Helpers\NumberToWords::convert(round($agreement->daily_deposit_amount)) }} Rupiah)</span>
                / hari
                dan disetorkan langsung ke rekening pendapatan kas BLUD Perparkiran
                @if ($activeBankAccount)
                    dengan nomor rekening : <span class="font-bold">{{ $activeBankAccount->account_number }}
                        ({{ $activeBankAccount->bank_name }})</span> atas nama <span
                        class="font-bold">{{ $activeBankAccount->account_name }}</span> dan bukti penyetoran
                    disampaikan ke bendahara penerimaan untuk dilakukan validasi.
                @else
                    (nomor rekening akan diinformasikan kemudian)
                    dan bukti penyetoran disampaikan ke bendahara penerimaan untuk dilakukan validasi.
                @endif
            </p>
            <p class="paragraph">PIHAK KEDUA wajib menyerahkan dana jaminan pelaksanaan sebesar jumlah nilai setoran
                selama jangka waktu kerjasama ini dilaksanakan;</p>
            <p class="paragraph">Jumlah Setoran dana sebagaimana dimaksud pada ayat (2) harus disetorkan sebelum
                pelaksanaan kerjasama dimulai;</p>
            <p class="paragraph">Apabila terjadi keterlambatan penyetoran/kekurangan penyetoran maka PIHAK PERTAMA
                berhak melakukan pemotongan uang jaminan secara sepihak sebesar kekurangan setoran yang ditetapkan ke
                kas penampungan BLUD.</p>
            <p class="paragraph">Apabila PIHAK KEDUA tidak menyetorkan kewajiban 3 hari berturut-turut pertama maka
                PIHAK PERTAMA memberikan surat teguran tertulis I dan di ikuti penarikan setoran, selanjutnya dalam 3
                hari berturut-turut kedua masih juga tidak dilakukan penyetoran maka dapat diberikan surat teguran II,
                dan dalam 3 hari berturut-turut ketiga tetap tidak melakukan penyetoran maka PIHAK PERTAMA memberikan
                surat teguran III sekaligus dengan pemutusan kerjasama;</p>
            <p class="paragraph">Dalam hal PIHAK KEDUA telah melakukan penyetoran kewajiban dalam jangka waktu 6 (enam)
                hari berturut-turut dengan lancar maka surat teguran I dinyatakan tidak berlaku dengan sendirinya;</p>
            <p class="paragraph">Selanjutnya PIHAK KEDUA telah melakukan penyetoran kewajiban dalam jangka waktu 2 (dua)
                minggu berturut-turut dengan lancar maka surat teguran II dinyatakan gugur dengan sendirinya.</p>

            <p class="pasal-title">Pasal 5<br>HAK DAN KEWAJIBAN PIHAK PERTAMA</p>
            <p class="paragraph">PIHAK PERTAMA berhak :</p>
            <ol type="a" class="list">
                <li>Memperoleh setoran tarif layanan parkir sebesar : Rp.
                    {{ number_format($agreement->daily_deposit_amount, 0, ',', '.') }}.-
                    ({{ \App\Helpers\NumberToWords::convert(round($agreement->daily_deposit_amount)) }} Rupiah) /hari.
                </li>
                <li>Melakukan pengawasan langsung atas pengelolaan dan pelayanan perparkiran yang dilaksanakan oleh
                    PIHAK KEDUA;</li>
                <li>Memutuskan Kontrak Kerjasama ini apabila PIHAK KEDUA dianggap tidak cakap dan dalam penilaian maka
                    PIHAK PERTAMA kerjasama oprasional perparkiran tidak dapat dilaksanakan.</li>
            </ol>
            <p class="paragraph">PIHAK PERTAMA berkewajiban :</p>
            <ol type="a" class="list">
                <li>Menentukan dan menetapkan wilayah parkir yang akan dikelola oleh PIHAK KEDUA;</li>
                <li>Memberikan data wilayah parkir yang akan dikelola oleh PIHAK KEDUA untuk dapat melakukan pengelolaan
                    dan pelayanan parkir sesuai kewenangan PIHAK KEDUA;</li>
                <li>Melakukan pengawasan terhadap pengelolaan dan pelayanan parkir oleh PIHAK KEDUA.</li>
            </ol>

            <p class="pasal-title">Pasal 6<br>HAK DAN KEWAJIBAN PIHAK KEDUA</p>
            <p class="paragraph">PIHAK KEDUA berhak :</p>
            <ol type="a" class="list">
                <li>Menerima data wilayah parkir yang diserahkan oleh PIHAK PERTAMA;</li>
                <li>Mengelola dan melakukan pelayanan parkir pada wilayah parkir yang telah disepakati bersama oleh PARA
                    PIHAK;</li>
                <li>Memperoleh keuntungan dari pengelolaan dan pelayanan parkir yang telah disepakati bersama oleh PARA
                    PIHAK;</li>
            </ol>
            <p class="paragraph">PIHAK KEDUA berkewajiban :</p>
            <ol type="a" class="list">
                <li>Melaksanakan tugas pengelolaan parkir sesuai Peraturan Perundang–undangan yang berlaku;</li>
                <li>Melakukan Pemungutan Jasa Layanan Perparkiran sesuai tarif yang telah ditentukan pada Peraturan Wali
                    Kota Pekanbaru Nomor 02 Tahun 2025 tentang Peninjauan Tarif Retribusi Jasa Umum atas Pelayanan
                    Parkir di Tepi Jalan Umum;</li>
                <li>Menyetorkan hasil pungutan jasa layanan perparkiran setiap hari (1x24) jam secara non tunai ke
                    rekening Kas BLUD UPT Perparkiran melalui Bendahara Penerimaan BLUD UPT Perparkiran Dinas
                    Perhubungan Kota Pekanbaru;</li>
                <li>Melaksanakan jasa layananan perparkiran sesuai dengan Standar Pelayanan Minimal (SPM) berdasarkan
                    Peraturan Walikota Nomor 132 tahun 2020 tentang Standar Pelayanan Minimal Unit Pelaksana Teknis
                    Perparkiran Dinas Perhubungan Kota Pekanbaru;</li>
                <li>Melengkapi atribut juru parkir berupa :</li>
                <ul class="sub-list" style="list-style-type: '- ';">
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
                <li>Memberikan sosialisasi dan pembekalan kepada juru parkir tentang Jasa Layanan Parkir;</li>
                <li>Menyediakan fasilitas pembayaran non tunai (cashless) seperti e-ticket/e-payment/e-money/QRIS sesuai
                    dengan kebutuhan untuk kelancaran pelaksanaan pengelola parkir secara profesional;</li>
                <li>Berkoordinasi dan melaporkan hasil pelaksanaan tugas kepada UPT Perparkiran secara berkala;</li>
                <li>PIHAK KEDUA diwajibkan untuk melaporkan setiap penambahan potensi dan titik lokasi parkir yang
                    berlaku.</li>
            </ol>

            <p class="pasal-title">Pasal 7<br>TARIF PARKIR</p>
            <p class="paragraph">PIHAK KEDUA memungut besaran tarif layanan parkir berdasarkan tarif layanan parkir yang
                ditetapkan yaitu Rp. 1000,- (seribu rupiah) untuk kendaraan roda 2 dan Rp. 2000,- (dua ribu rupiah)
                untuk kendaraan roda 4 dan Rp. 6.000,- (enam ribu rupiah) untuk roda 6.</p>

            <p class="pasal-title">Pasal 8<br>PELAYANAN DAN PELAKSANAAN</p>
            <p class="paragraph">Dalam rangka pelaksanaan pelayanan parkir, PIHAK KEDUA wajib mempedomani Standar
                Pelayanan Minimal (SPM) pelayanan parkir yang ditetapkan oleh PIHAK PERTAMA;</p>
            <p class="paragraph">PIHAK PERTAMA melakukan monitoring dan evaluasi terhadap kinerja pelaksanaan pelayanan
                parkir yang dilakukan oleh PIHAK KEDUA sesuai SPM yang ditetapkan PIHAK PERTAMA;</p>
            <p class="paragraph">Dalam rangka monitoring dan evaluasi sebagaimana dimaksud pada ayat (2) pasal ini,
                PIHAK PERTAMA berhak mengakses data administrasi yang dikelola oleh PIHAK KEDUA;</p>
            <p class="paragraph">Dalam rangka peningkatan pelayanan parkir, juru parkir wajib menerapkan 3S
                (Sapa,Senyum,Salam) kepada pengguna jasa layanan parkir.</p>

            <p class="pasal-title">Pasal 9<br>PENDAPATAN</p>
            <p class="paragraph">Pendapatan yang diperoleh PIHAK KEDUA dari tarif layananparkirwajib disetorkan kepada
                PIHAK PERTAMA secara non tunai melalui bendaraha penerimaan sebagaimana diatur dalam pasal 4 ayat (1);
            </p>
            <p class="paragraph">Apabila terjadi perubahan penambahan potensi pendapatan layanan parkir maka PARA PIHAK
                dapat menyesuaikan jumlah nilai setoran sebagaimana dimaksud pada ayat (1);</p>

            <p class="pasal-title">Pasal 10<br>KOORDINASI</p>
            <p class="paragraph">Dalam rangka pelaksanaan Perjanjian Kerjasama ini akan dilakukan koordinasi antara PARA
                PIHAK paling kurang 1 (satu) minggu sekali dan/atau pada waktu tertentu yang disepakati oleh PARA PIHAK;
            </p>

            <p class="pasal-title">Pasal 11<br>ADDENDUM</p>
            <p class="paragraph">Addendum perjanjian kerjasama ini dapat dilakukan apabila :</p>
            <ol type="a" class="list">
                <li>Terjadinya perubahan kebijakan ketenagakerjaan;</li>
                <li>Terjadinya perubahan potensi parkir;</li>
                <li>Terjadinya perubahan Tarif Layanan Parkir;</li>
                <li>Hal-hal lain yang disepakati PARA PIHAK.</li>
            </ol>

            <p class="pasal-title">Pasal 12<br>KEADAAN MEMAKSA (FORCE MAJEURE)</p>
            <p class="paragraph">Keadaan memaksa (force majeure) adalah keadaan yang terjadi diluar jangkauan dan
                kemauan PARA PIHAK seperti kerusuhan sosial, peperangan, kebakaran, peledakan, sabotase, badai, banjir,
                gempa bumi, tsunami yang mengakibatkan keterlambatan atau kegagalan salah satu Pihak dalam memenuhi
                kewajibannya sebagaimana tercantum dalam Perjanjian Kerjasama ini;</p>
            <p class="paragraph">Apabila terjadi force majeure sebagaimana dimaksud pada ayat (1) pasal ini maka Pihak
                yang mengalami force majeure wajib memberitahukan secara tertulis kepada Pihak lainnya
                selambat-lambatnya 7 (tujuh) hari kalender terhitung sejak tanggal terjadinya force majeure ;</p>
            <p class="paragraph">Keterlambatan atau kelalaian atas pemberitahuan tersebut mengakibatkan tidak diakuinya
                peristiwa tersebut sebagai keadaan force majeure.</p>

            <p class="pasal-title">Pasal 13<br>LARANGAN</p>
            <p class="paragraph">Selama Perjanjian Kerjasama ini berlaku, PIHAK KEDUA dilarang :</p>
            <ol type="a" class="list">
                <li>Melakukan pengelolaan perparkiran tidak sesuai dengan Peraturan Perundang-undangan yang berlaku;
                </li>
                <li>Memungut tarif layanan parkir melebihi besaran tarif layanan parkir yang berlaku yang telah diatur
                    dalam Peraturan Perundang-undangan yang berlaku;</li>
                <li>PIHAK KEDUA tidak dibenarkan mengalihkan pegelolaan dan pemungutan jasa layanan perparkiran kepada
                    pihak lain;</li>
                <li>PIHAK KEDUA tidak dibenarkan mengelola dan memungut jasa layanan perparkiran pada titik lokasi yang
                    tidak tercantum dalam perjanjian kerjasama ini;</li>
                <li>Melaksanakan pelayanan parkir tidak berdasarkan Standar Pelayanan Minimal (SPM) yang ditetapkan
                    PIHAK PERTAMA.</li>
            </ol>

            <p class="pasal-title">Pasal 14<br>SANKSI</p>
            <p class="paragraph">Dalam hal PIHAK KEDUA melanggar ketentuan sebagaimana dimaksud dalam Pasal 14 huruf c,
                maka PIHAK PERTAMA berhak memutus Perjanjian Kerjasama ini secara sepihak;</p>
            <p class="paragraph">Dalam hal PIHAK KEDUA melanggar ketentuan sebagaimana dimaksud dalam Pasal 14 huruf a,
                maka PIHAK KEDUA dikenakan sanksi dan ketentuan peraturan perundang-undangan yang berlaku;</p>
            <p class="paragraph">Dalam hal ayat (1) dan (2) terbukti dan terjadi pemutusan perjanjian kerjasama maka
                PIHAK PERTAMA berhak mengambil alih secara utuh pengelolaan pelayanan parkir didalam ruang milik jalan
                sesuai peraturan perundang-undangan yang berlaku;</p>

            <p class="pasal-title">Pasal 15<br>BERAKHIRNYA PERJANJIAN KERJASAMA</p>
            <p class="paragraph">Perjanjian Kerjasama ini dapat berakhir disebabkan oleh :</p>
            <ol type="a" class="list">
                <li>Berakhirnya jangka waktu;</li>
                <li>Diputus oleh salah satu Pihak; dan</li>
                <li>Terjadinya keadaan memaksa.</li>
            </ol>
            <p class="paragraph">PIHAK KEDUA melanggar ketentuan sebagaimana dimaksud dalam Pasal 15.</p>
            <p class="paragraph">Pemutusan Perjanjian Kerjasama sebagaimana dimaksud ayat (1) huruf b, dilakukan oleh
                PIHAK KEDUA dalam hal PIHAK PERTAMA tidak dapat melaksanakan kewajiban sebagaimana diatur dalam
                Perjanjian Kerjasama ini;</p>
            <p class="paragraph">Jika dikemudian hari terjadi pemutusan kerjasama sebagaimana dimaksud ayat (1) maka
                PIHAK KEDUA tidak dapat menuntut secara hukum yang berlaku.</p>

            <p class="pasal-title">Pasal 16<br>PERSELISIHAN</p>
            <p class="paragraph">Segala perbedaan pendapat atau perselisihan yang timbul dalam Perjanjian Kerjasama ini
                akan diselesaikan secara musyawarah dan mufakat oleh PARA PIHAK;</p>

            <p class="pasal-title">Pasal 17<br>LAIN-LAIN</p>
            <p class="paragraph">Segala sesuatu yang belum atau tidak cukup diatur dalam Perjanjian Kerjasama ini akan
                dituangkan dalam suatu perjanjian tambahan (addendum) tersendiri yang merupakan satu kesatuan yang tidak
                dapat terpisahkan dengan Perjanjian Kerjasama ini dan mempunyai kekuatan hukum yang sama;</p>
            <p class="paragraph">Perjanjian Kerjasama ini tetap berlaku walaupun terjadi perubahan kepemimpinan/jabatan
                dan bentuk badan hukum pada salah satu Pihak</p>
            <p class="paragraph">Pengajuan untuk perpanjangan Perjanjian Kerjasama wajib diajukan 10 (sepuluh) hari
                sebelum kerjasama ini berakhir;</p>
            <p class="paragraph">Dalam pengambilan keputusan terkait pengajuan,penetapan dan pengelolaan titik lokasi
                parkir kewenangan sepenuhnya berada di Dinas Perhubungan Kota Pekanbaru melalui BLUD UPT Perparkiran.
            </p>

            <p class="pasal-title">Pasal 18<br>PENUTUP</p>
            <p class="paragraph">Demikian Perjanjian Kerjasama ini dibuat dan ditandatangani di Pekanbaru pada hari dan
                tanggal tersebut di atas dalam rangkap 2 (dua) bermaterai cukup,masing-masing mempunyai kekuatan hukum
                yang sama.</p>

            <div style="margin-top: 40px; page-break-inside: avoid;">
                <table width="100%">
                    <tr>
                        <td width="70%">
                            {{-- Biarkan kosong atau isi dengan catatan tambahan --}}
                        </td>
                        <div style="position: absolute; bottom: 50px; right: 50px; text-align: center;">
                            @if ($agreement->verification_code)
                                <div style="display: inline-block; text-align: center;">
                                    {{-- ✅ PERBAIKAN UTAMA DI SINI: Gunakan SVG yang di-encode --}}
                                    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(90)->generate(route('public.agreement.verify', $agreement->verification_code))) }}"
                                        alt="QR Code">
                                    <p style="font-size: 8pt; margin-top: 5px;">Pindai untuk Verifikasi</p>
                                </div>
                            @endif
                        </div>
                    </tr>
                </table>
            </div>

            <div class="signature-block">

                <table width="100%" style="page-break-inside: avoid;">
                    <tr>
                        <td width="45%" style="text-align: center;"><br><br>PIHAK PERTAMA, <br><b>Plt. KEPALA UPT
                                PERPARKIRAN SELAKU PIMPINAN BLUD<br><br><br>
                                <u>{{ strtoupper($agreement->leader->user->name) }}</u></b><br>NIP.
                            {{ formatNip($agreement->leader->employee_number) ?? 'N/A' }}
                        </td>
                        <td width="5%" style="text-align: center;">
                        </td>
                        <td width="10%" style="text-align: center;">
                        </td>
                        <td width="40%" style="text-align: center;"><br>PIHAK KEDUA,<br><b>MITRA
                                KERJASAMA<br><br><br><br>
                                <b><u>{{ strtoupper($agreement->fieldCoordinator->user->name) }}</u></b></td>
                    </tr>
                </table>
            </div>
        </div>
    </body>

</html>
