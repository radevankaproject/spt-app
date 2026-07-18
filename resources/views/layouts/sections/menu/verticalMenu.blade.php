@php
use Illuminate\Support\Facades\Route;
$configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu" @foreach ($configData['menuAttributes'] as $attribute=>$value)
  {{ $attribute }}="{{ $value }}" @endforeach>

  <!-- ! Hide app brand if navbar-full -->
  @if (!isset($navbarFull))
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
          {{-- ✅ LOGO DIAMBIL DARI PROFIL UPT --}}
          <img src="{{ asset('logo.png') }}" alt="Logo" height="35">
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-3">
          {{-- ✅ NAMA APLIKASI DIAMBIL DARI PROFIL UPT --}}
          {{ (isset($uptProfile) ? $uptProfile->app_name : config('app.name')) }}
      </span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
      <i class="icon-base ti tabler-x d-block d-xl-none"></i>
    </a>
  </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
      <li class="menu-header small"><span class="menu-header-text">Beranda Utama</span></li>

      {{-- ✅ MENU DASHBOARD BERDASARKAN ROLE --}}
      @if (Auth::user()->role === 'admin')
          <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <a href="{{ route('admin.dashboard') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-layout-dashboard"></i>
                  <div data-i18n="Dashboard">Dashboard Admin</div>
              </a>
          </li>
      @elseif (Auth::user()->role === 'staff_pks')
          <li class="menu-item {{ request()->routeIs('staff-pks.dashboard') ? 'active' : '' }}">
              <a href="{{ route('staff-pks.dashboard') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-layout-dashboard"></i>
                  <div data-i18n="Dashboard PKS">Dashboard PKS</div>
              </a>
          </li>
      @elseif (Auth::user()->role === 'staff_keu')
          <li class="menu-item {{ request()->routeIs('staff-keuangan.dashboard') ? 'active' : '' }}">
              <a href="{{ route('staff-keuangan.dashboard') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-layout-dashboard"></i>
                  <div data-i18n="Dashboard Keuangan">Dashboard Keuangan</div>
              </a>
          </li>
      @elseif (Auth::user()->role === 'leader')
          <li class="menu-item {{ request()->routeIs('leader.dashboard') ? 'active' : '' }}">
              <a href="{{ route('leader.dashboard') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-layout-dashboard"></i>
                  <div data-i18n="Dashboard Pimpinan">Dashboard Pimpinan</div>
              </a>
          </li>
      @elseif (Auth::user()->role === 'treasurer')
          <li class="menu-item {{ request()->routeIs('treasurer.dashboard') ? 'active' : '' }}">
              <a href="{{ route('treasurer.dashboard') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-layout-dashboard"></i>
                  <div data-i18n="Dashboard Bendahara">Dashboard Bendahara</div>
              </a>
          </li>
      @elseif (Auth::user()->role === 'staff_kta_jukir')
          <li class="menu-item {{ request()->routeIs('staff-kta-jukir.dashboard') ? 'active' : '' }}">
              <a href="{{ route('staff-kta-jukir.dashboard') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-layout-dashboard"></i>
                  <div data-i18n="Dashboard">Dashboard KTA Jukir</div>
              </a>
          </li>
      @elseif (Auth::user()->role === 'field_coordinator')
          <li class="menu-item {{ request()->routeIs('field_coordinator.dashboard') ? 'active' : '' }}">
              <a href="{{ route('field_coordinator.dashboard') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-home"></i>
                  <div data-i18n="Dashboard Mitra">Dashboard Mitra</div>
              </a>
          </li>
      @endif

      {{-- ✅ MENU KHUSUS KOORDINATOR LAPANGAN (MITRA PKS) --}}
      @if (Auth::user()->role === 'field_coordinator')
          <li class="menu-header small"><span class="menu-header-text">Operasional</span></li>
          <li class="menu-item {{ request()->routeIs('field_coordinator.location-requests.*') ? 'active' : '' }}">
              <a href="{{ route('field_coordinator.location-requests.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-map-pin-plus"></i>
                  <div data-i18n="Pengajuan Titik">Pengajuan Titik</div>
              </a>
          </li>
      @endif

      {{-- MENU ADMINISTRASI --}}
      @if (in_array(Auth::user()->role, ['admin', 'staff_pks', 'staff_kta_jukir']))
          <li class="menu-header small"><span class="menu-header-text">Administrasi</span></li>
          <li class="menu-item {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.jukirs.*') || request()->routeIs('admin.leaders.*') || request()->routeIs('admin.treasurers.*') || request()->routeIs('admin.field-coordinators.*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <i class="icon-base menu-icon tf-icons ti tabler-user-bolt"></i>
                  <div data-i18n="Manage Users">Manage Users</div>
              </a>
              <ul class="menu-sub">
                  @if (Auth::user()->role === 'admin')
                  <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                      <a href="{{ route('admin.users.index') }}" class="menu-link"><div>All Users</div></a>
                  </li>
                  @endif
                  
                  @if (in_array(Auth::user()->role, ['admin', 'staff_kta_jukir']))
                  <li class="menu-item {{ request()->routeIs('admin.jukirs.*') ? 'active' : '' }}">
                      <a href="{{ route('admin.jukirs.index') }}" class="menu-link"><div>Data Jukir</div></a>
                  </li>
                  @endif
                  
                  @if (Auth::user()->role === 'admin')
                  <li class="menu-item {{ request()->routeIs('admin.leaders.*') ? 'active' : '' }}">
                      <a href="{{ route('admin.leaders.index') }}" class="menu-link"><div>Pimpinan UPT</div></a>
                  </li>
                  <li class="menu-item {{ request()->routeIs('admin.treasurers.*') ? 'active' : '' }}">
                      <a href="{{ route('admin.treasurers.index') }}" class="menu-link"><div>Bendahara</div></a>
                  </li>
                  @endif
                  
                  @if (in_array(Auth::user()->role, ['admin', 'staff_pks']))
                  <li class="menu-item {{ request()->routeIs('admin.field-coordinators.*') ? 'active' : '' }}">
                      <a href="{{ route('admin.field-coordinators.index') }}" class="menu-link"><div>Koordinator Lapangan</div></a>
                  </li>
                  @endif
              </ul>
          </li>
          @if (Auth::user()->role === 'admin')
          <li class="menu-item {{ request()->routeIs('admin.upt-profile.index') ? 'active' : '' }}">
              <a href="{{ route('admin.upt-profile.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-building"></i>
                  <div data-i18n="Profil UPT">Profil UPT</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('admin.blud-bank-accounts.*') ? 'active' : '' }}">
              <a href="{{ route('admin.blud-bank-accounts.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-building-bank"></i>
                  <div data-i18n="Rekening BLUD">Rekening BLUD</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
              <a href="{{ route('admin.backup.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-database"></i>
                  <div data-i18n="Backup Database">Backup Database</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('admin.app-versions.*') ? 'active' : '' }}">
              <a href="{{ route('admin.app-versions.manage') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-git-branch"></i>
                  <div data-i18n="Manajemen Versi">Manajemen Versi</div>
              </a>
          </li>
          @endif
      @endif

      {{-- ✅ LOKASI PARKIR & PKS (DIBUKA UNTUK LEADER JUGA) --}}
      @if (in_array(Auth::user()->role, ['admin', 'staff_pks', 'leader']))
          <li class="menu-header small"><span class="menu-header-text">Data Wilayah & PKS</span></li>
          <li class="menu-item {{ request()->routeIs('masterdata.road-sections.*') || (request()->routeIs('masterdata.parking-locations.*') && !request()->routeIs('masterdata.parking-locations.map')) ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <i class="icon-base menu-icon tf-icons ti tabler-map-pin"></i>
                  <div data-i18n="Master Lokasi">Master Lokasi</div>
              </a>
              <ul class="menu-sub">
                  <li class="menu-item {{ request()->routeIs('masterdata.road-sections.*') ? 'active' : '' }}">
                      <a href="{{ route('masterdata.road-sections.index') }}" class="menu-link"><div data-i18n="Ruas Jalan">Ruas Jalan</div></a>
                  </li>
                  <li class="menu-item {{ request()->routeIs('masterdata.parking-locations.*') && !request()->routeIs('masterdata.parking-locations.map') ? 'active' : '' }}">
                      <a href="{{ route('masterdata.parking-locations.index') }}" class="menu-link"><div data-i18n="Titik Parkir">Titik Parkir</div></a>
                  </li>
              </ul>
          </li>

          <li class="menu-item {{ request()->routeIs('masterdata.agreements.*') ? 'active' : '' }}">
              <a href="{{ route('masterdata.agreements.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-file-like"></i>
                  <div data-i18n="Perjanjian Kerjasama">Perjanjian PKS</div>
              </a>
          </li>
          
          <li class="menu-item {{ request()->routeIs('masterdata.agreement-histories.*') ? 'active' : '' }}">
              <a href="{{ route('masterdata.agreement-histories.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-history"></i>
                  <div data-i18n="Histori Perjanjian">Riwayat PKS</div>
              </a>
          </li>

          @if (in_array(Auth::user()->role, ['admin', 'staff_pks']))
          <li class="menu-item {{ request()->routeIs('masterdata.location-requests.*') ? 'active' : '' }}">
              <a href="{{ route('masterdata.location-requests.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-map-pin-plus"></i>
                  <div data-i18n="Pengajuan Titik">Pengajuan Titik</div>
              </a>
          </li>
          @endif

          @if (Auth::user()->role === 'admin')
          <li class="menu-item {{ request()->routeIs('admin.parking-locations.report') ? 'active' : '' }}">
              <a href="{{ route('admin.parking-locations.report') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-file-text"></i>
                  <div data-i18n="Laporan Titik Parkir">Laporan Titik Parkir</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('admin.survey-parking-locations.*') ? 'active' : '' }}">
              <a href="{{ route('admin.survey-parking-locations.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-clipboard-check"></i>
                  <div data-i18n="Survey Lokasi Parkir">Survey Lokasi Parkir</div>
              </a>
          </li>
          @endif
      @endif

      {{-- ✅ PETA WILAYAH PARKIR --}}
      @if (in_array(Auth::user()->role, ['admin', 'leader', 'staff_pks', 'treasurer']))
          <li class="menu-header small"><span class="menu-header-text">Pemetaan</span></li>
          <li class="menu-item {{ request()->routeIs('masterdata.parking-locations.map') ? 'active' : '' }}">
              <a href="{{ route('masterdata.parking-locations.map') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-map"></i>
                  <div data-i18n="Peta Wilayah Parkir">Peta Wilayah Parkir</div>
              </a>
          </li>
      @endif

      {{-- ✅ BLOK KEUANGAN (DIBUKA UNTUK LEADER JUGA) --}}
      @if (in_array(Auth::user()->role, ['admin', 'staff_keu', 'treasurer', 'leader']))
          <li class="menu-header small"><span class="menu-header-text">Keuangan & Setoran</span></li>
          
          @if (in_array(Auth::user()->role, ['admin', 'staff_keu', 'leader']))
          <li class="menu-item {{ request()->routeIs('masterdata.deposit-targets.*') ? 'active' : '' }}">
              <a href="{{ route('masterdata.deposit-targets.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-coins"></i>
                  <div data-i18n="Target Setoran">Target Pendapatan</div>
              </a>
          </li>
          @endif

          @if (in_array(Auth::user()->role, ['admin', 'staff_keu', 'treasurer']))
          <li class="menu-item {{ request()->routeIs('masterdata.deposit-transactions.*') ? 'active' : '' }}">
              <a href="{{ route('masterdata.deposit-transactions.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-cash-plus"></i>
                  <div data-i18n="Input Setoran">Input Setoran</div>
              </a>
          </li>
          @endif

          <li class="menu-item {{ request()->routeIs('masterdata.deposit-reports.*') ? 'active' : '' }}">
              <a href="{{ route('masterdata.deposit-reports.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-report-money"></i>
                  <div data-i18n="Laporan Setoran">Laporan Setoran</div>
              </a>
          </li>
      @endif

      {{-- ✅ PENGADUAN & LAYANAN MASYARAKAT --}}
      @if (in_array(Auth::user()->role, ['admin', 'leader', 'staff_kta_jukir', 'staff_pks']))
          @php
              $pendingComplaintsCount = \App\Models\JukirComplaint::where('status', 'pending')->count();
          @endphp
          <li class="menu-header small"><span class="menu-header-text">Layanan Masyarakat</span></li>
          <li class="menu-item {{ request()->routeIs('admin.jukir-complaints.*') ? 'active' : '' }}">
              <a href="{{ route('admin.jukir-complaints.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-messages"></i>
                  <div data-i18n="Pengaduan Jukir">Pengaduan Jukir</div>
                  @if($pendingComplaintsCount > 0)
                      <div class="badge bg-danger rounded-pill ms-auto">{{ $pendingComplaintsCount }}</div>
                  @endif
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('admin.kontak-masyarakat.*') ? 'active' : '' }}">
              <a href="{{ route('admin.kontak-masyarakat.index') }}" class="menu-link">
                  <i class="icon-base menu-icon tf-icons ti tabler-address-book"></i>
                  <div data-i18n="Kontak Masyarakat">Kontak Masyarakat</div>
              </a>
          </li>
      @endif

      <li class="menu-header small"><span class="menu-header-text">Sistem</span></li>
      <li class="menu-item {{ request()->routeIs('profile.settings') ? 'active' : '' }}">
          <a href="{{ route('profile.settings') }}" class="menu-link">
              <i class="icon-base menu-icon tf-icons ti tabler-settings"></i>
              <div data-i18n="Setting Profil">Pengaturan Akun</div>
          </a>
      </li>
  </ul>
</aside>