<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                {{-- ✅ LOGO DIAMBIL DARI PROFIL UPT --}}
                <img src="{{ asset('logo.png') }}" alt="Logo" height="35">
            </span>
            <span class="app-brand-text demo menu-text fw-bold">
                {{-- ✅ NAMA APLIKASI DIAMBIL DARI PROFIL UPT --}}
                {{ '        ' . (isset($uptProfile) ? $uptProfile->app_name : config('app.name')) }}
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z"
                    fill-opacity="0.9" />
                <path
                    d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z"
                    fill-opacity="0.4" />
            </svg>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-header small"><span class="menu-header-text">Beranda Utama</span></li>

        {{-- ✅ MENU DASHBOARD BERDASARKAN ROLE --}}
        @if (Auth::user()->role === 'admin')
            <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-dashboard-line"></i>
                    <div data-i18n="Dashboard">Dashboard Admin</div>
                </a>
            </li>
        @elseif (Auth::user()->role === 'staff_pks')
            <li class="menu-item {{ request()->routeIs('staff-pks.dashboard') ? 'active' : '' }}">
                <a href="{{ route('staff-pks.dashboard') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-dashboard-line"></i>
                    <div data-i18n="Dashboard PKS">Dashboard PKS</div>
                </a>
            </li>
        @elseif (Auth::user()->role === 'staff_keu')
            <li class="menu-item {{ request()->routeIs('staff-keuangan.dashboard') ? 'active' : '' }}">
                <a href="{{ route('staff-keuangan.dashboard') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-dashboard-line"></i>
                    <div data-i18n="Dashboard Keuangan">Dashboard Keuangan</div>
                </a>
            </li>
        @elseif (Auth::user()->role === 'leader')
            <li class="menu-item {{ request()->routeIs('leader.dashboard') ? 'active' : '' }}">
                <a href="{{ route('leader.dashboard') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-dashboard-line"></i>
                    <div data-i18n="Dashboard Pimpinan">Dashboard Pimpinan</div>
                </a>
            </li>
        @elseif (Auth::user()->role === 'treasurer')
            <li class="menu-item {{ request()->routeIs('treasurer.dashboard') ? 'active' : '' }}">
                <a href="{{ route('treasurer.dashboard') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-dashboard-line"></i>
                    <div data-i18n="Dashboard Bendahara">Dashboard Bendahara</div>
                </a>
            </li>
        @elseif (Auth::user()->role === 'field_coordinator')
            <li class="menu-item {{ request()->routeIs('field_coordinator.dashboard') ? 'active' : '' }}">
                <a href="{{ route('field_coordinator.dashboard') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-home-smile-line"></i>
                    <div data-i18n="Dashboard Mitra">Dashboard Mitra</div>
                </a>
            </li>
        @endif

        {{-- ✅ MENU KHUSUS KOORDINATOR LAPANGAN (MITRA PKS) --}}
        @if (Auth::user()->role === 'field_coordinator')
            <li class="menu-header small"><span class="menu-header-text">Operasional</span></li>
            <li class="menu-item {{ request()->routeIs('field_coordinator.location-requests.*') ? 'active' : '' }}">
                <a href="{{ route('field_coordinator.location-requests.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-map-pin-add-line"></i>
                    <div data-i18n="Pengajuan Titik">Pengajuan Titik</div>
                </a>
            </li>
        @endif

        {{-- MENU ADMINISTRASI --}}
        @if (Auth::user()->role === 'admin')
            <li class="menu-header small"><span class="menu-header-text">Administrasi</span></li>
            <li class="menu-item {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.leaders.*') || request()->routeIs('admin.treasurers.*') || request()->routeIs('admin.field-coordinators.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="icon-base ri menu-icon tf-icons ri-user-settings-line"></i>
                    <div data-i18n="Manage Users">Manage Users</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="menu-link"><div>All Users</div></a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.leaders.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.leaders.index') }}" class="menu-link"><div>Pimpinan UPT</div></a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.treasurers.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.treasurers.index') }}" class="menu-link"><div>Bendahara</div></a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.field-coordinators.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.field-coordinators.index') }}" class="menu-link"><div>Koordinator Lapangan</div></a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.upt-profile.index') ? 'active' : '' }}">
                <a href="{{ route('admin.upt-profile.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-building-4-line"></i>
                    <div data-i18n="Profil UPT">Profil UPT</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.blud-bank-accounts.*') ? 'active' : '' }}">
                <a href="{{ route('admin.blud-bank-accounts.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-bank-line"></i>
                    <div data-i18n="Rekening BLUD">Rekening BLUD</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
                <a href="{{ route('admin.backup.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-database-2-line"></i>
                    <div data-i18n="Backup Database">Backup Database</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.app-versions.*') ? 'active' : '' }}">
                <a href="{{ route('admin.app-versions.manage') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-git-branch-line"></i>
                    <div data-i18n="Manajemen Versi">Manajemen Versi</div>
                </a>
            </li>
        @endif

        {{-- ✅ LOKASI PARKIR & PKS (DIBUKA UNTUK LEADER JUGA) --}}
        @if (in_array(Auth::user()->role, ['admin', 'staff_pks', 'leader']))
            <li class="menu-header small"><span class="menu-header-text">Data Wilayah & PKS</span></li>
            <li class="menu-item {{ request()->routeIs('masterdata.road-sections.*') || request()->routeIs('masterdata.parking-locations.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="icon-base ri menu-icon tf-icons ri-map-pin-line"></i>
                    <div data-i18n="Master Lokasi">Master Lokasi</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('masterdata.road-sections.*') ? 'active' : '' }}">
                        <a href="{{ route('masterdata.road-sections.index') }}" class="menu-link"><div data-i18n="Ruas Jalan">Ruas Jalan</div></a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('masterdata.parking-locations.*') ? 'active' : '' }}">
                        <a href="{{ route('masterdata.parking-locations.index') }}" class="menu-link"><div data-i18n="Titik Parkir">Titik Parkir</div></a>
                    </li>
                </ul>
            </li>

            {{-- Leader biasanya cukup pantau, staff/admin yang eksekusi persetujuan titik --}}
            <!-- @if (in_array(Auth::user()->role, ['admin', 'staff_pks']))
            <li class="menu-item {{ request()->routeIs('masterdata.location-requests.*') ? 'active' : '' }}">
                <a href="{{ route('masterdata.location-requests.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-survey-line"></i>
                    <div data-i18n="Persetujuan Titik">Persetujuan Titik</div>
                </a>
            </li>
            @endif -->

            <li class="menu-item {{ request()->routeIs('masterdata.agreements.*') ? 'active' : '' }}">
                <a href="{{ route('masterdata.agreements.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-file-text-line"></i>
                    <div data-i18n="Perjanjian Kerjasama">Perjanjian PKS</div>
                </a>
            </li>
            
            <li class="menu-item {{ request()->routeIs('masterdata.agreement-histories.*') ? 'active' : '' }}">
                <a href="{{ route('masterdata.agreement-histories.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-history-line"></i>
                    <div data-i18n="Histori Perjanjian">Riwayat PKS</div>
                </a>
            </li>
        @endif

        {{-- ✅ BLOK KEUANGAN (DIBUKA UNTUK LEADER JUGA) --}}
        @if (in_array(Auth::user()->role, ['admin', 'staff_keu', 'treasurer', 'leader']))
            <li class="menu-header small"><span class="menu-header-text">Keuangan & Setoran</span></li>
            
            @if (in_array(Auth::user()->role, ['admin', 'staff_keu', 'leader']))
            <li class="menu-item {{ request()->routeIs('masterdata.deposit-targets.*') ? 'active' : '' }}">
                <a href="{{ route('masterdata.deposit-targets.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-line-chart-line"></i>
                    <div data-i18n="Target Setoran">Target Pendapatan</div>
                </a>
            </li>
            @endif

            {{-- Validasi Setoran: Admin, Keuangan, Bendahara (Leader mungkin cuma mantau dari laporan) --}}
            @if (in_array(Auth::user()->role, ['admin', 'staff_keu', 'treasurer']))
            <li class="menu-item {{ request()->routeIs('masterdata.deposit-transactions.*') ? 'active' : '' }}">
                <a href="{{ route('masterdata.deposit-transactions.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-safe-2-line"></i>
                    <div data-i18n="Validasi Setoran">Validasi Setoran</div>
                </a>
            </li>
            @endif

            {{-- Laporan Setoran: Dibuka untuk Pimpinan (Leader) agar bisa cek data masuk --}}
            <li class="menu-item {{ request()->routeIs('masterdata.deposit-reports.*') ? 'active' : '' }}">
                <a href="{{ route('masterdata.deposit-reports.index') }}" class="menu-link">
                    <i class="icon-base ri menu-icon tf-icons ri-exchange-dollar-fill"></i>
                    <div data-i18n="Laporan Setoran">Laporan Setoran</div>
                </a>
            </li>
        @endif

        <li class="menu-header small"><span class="menu-header-text">Sistem</span></li>
        <li class="menu-item {{ request()->routeIs('profile.settings') ? 'active' : '' }}">
            <a href="{{ route('profile.settings') }}" class="menu-link">
                <i class="icon-base ri menu-icon tf-icons ri-settings-2-line"></i>
                <div data-i18n="Setting Profil">Pengaturan Akun</div>
            </a>
        </li>
    </ul>
</aside>

<div class="menu-mobile-toggler d-xl-none rounded-1">
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
        <i class="icon-base ri ri ri-menu-line icon-base"></i>
        <i class="icon-base ri ri ri-arrow-right-s-line icon-base"></i>
    </a>
</div>