@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Agreement;
use App\Models\DepositTransaction;
use Carbon\Carbon;

$userRole = Auth::check() ? Auth::user()->role : null;

// Build shortcuts based on role
$shortcuts = [];
if ($userRole === 'admin') {
    $shortcuts = [
        ['name' => 'Dashboard', 'url' => url('/'), 'icon' => 'tabler-device-desktop-analytics', 'desc' => 'Ringkasan Sistem'],
        ['name' => 'Manajemen PKS', 'url' => route('masterdata.agreements.index'), 'icon' => 'tabler-file-signature', 'desc' => 'Kelola Perjanjian'],
        ['name' => 'Master User', 'url' => route('admin.users.index'), 'icon' => 'tabler-users', 'desc' => 'Kelola Pengguna'],
        ['name' => 'Laporan Keuangan', 'url' => route('masterdata.deposit-reports.index'), 'icon' => 'tabler-report-money', 'desc' => 'Rekap Setoran'],
    ];
} elseif ($userRole === 'staff-pks') {
    $shortcuts = [
        ['name' => 'Dashboard', 'url' => url('/'), 'icon' => 'tabler-device-desktop-analytics', 'desc' => 'Ringkasan PKS'],
        ['name' => 'Manajemen PKS', 'url' => route('masterdata.agreements.index'), 'icon' => 'tabler-file-signature', 'desc' => 'Kelola Perjanjian'],
        ['name' => 'Titik Parkir', 'url' => route('masterdata.parking-locations.index'), 'icon' => 'tabler-map-pin', 'desc' => 'Lokasi Parkir'],
        ['name' => 'Master Jukir', 'url' => route('admin.leaders.index'), 'icon' => 'tabler-user', 'desc' => 'Kelola Jukir'],
    ];
} elseif ($userRole === 'staff-keuangan') {
    $shortcuts = [
        ['name' => 'Dashboard', 'url' => url('/'), 'icon' => 'tabler-device-desktop-analytics', 'desc' => 'Ringkasan Keuangan'],
        ['name' => 'Validasi Setoran', 'url' => route('masterdata.deposit-transactions.index'), 'icon' => 'tabler-cash', 'desc' => 'Data Setoran'],
        ['name' => 'Laporan Keuangan', 'url' => route('masterdata.deposit-reports.index'), 'icon' => 'tabler-report-money', 'desc' => 'Rekap Keuangan'],
        ['name' => 'Data Jukir', 'url' => route('admin.leaders.index'), 'icon' => 'tabler-user', 'desc' => 'Koordinator/Jukir'],
    ];
}

// Fetch real notifications
$notificationsRaw = [];

if (Auth::check() && in_array($userRole, ['admin', 'staff-pks'])) {
    $expiringAgreements = Agreement::where('status', 'active')
        ->where('end_date', '<=', Carbon::now()->addDays(30))
        ->get();
        
    foreach ($expiringAgreements as $agr) {
        $daysLeft = (int) Carbon::now()->startOfDay()->diffInDays(Carbon::parse($agr->end_date)->startOfDay(), false);
        $statusText = $daysLeft < 0 ? 'Sudah Berakhir' : ($daysLeft == 0 ? 'Berakhir Hari Ini' : "Berakhir dalam $daysLeft hari");
        $notifId = 'agr_' . $agr->id;
        $notificationsRaw[] = [
            'id' => $notifId,
            'title' => 'PKS ' . $statusText,
            'desc' => "PKS {$agr->agreement_number} {$statusText}.",
            'timestamp' => Carbon::parse($agr->updated_at ?? $agr->created_at),
            'icon' => 'tabler-file-alert',
            'color' => $daysLeft < 0 ? 'danger' : 'warning',
            'url' => route('masterdata.agreements.show', $agr->id),
        ];
    }
}

if (Auth::check() && in_array($userRole, ['admin', 'staff-keuangan'])) {
    $pendingDeposits = DepositTransaction::where('is_validated', false)->get();
    foreach ($pendingDeposits as $deposit) {
        $notifId = 'dep_pend_' . $deposit->id;
        $notificationsRaw[] = [
            'id' => $notifId,
            'title' => 'Setoran Belum Divalidasi',
            'desc' => "Setoran Rp " . number_format($deposit->amount, 0, ',', '.') . " menunggu validasi.",
            'timestamp' => Carbon::parse($deposit->created_at),
            'icon' => 'tabler-cash',
            'color' => 'info',
            'url' => route('masterdata.deposit-transactions.index'),
        ];
    }
    
    $recentValidated = DepositTransaction::where('is_validated', true)
        ->orderBy('validation_date', 'desc')
        ->take(10)
        ->get();
    foreach ($recentValidated as $deposit) {
        $notifId = 'dep_val_' . $deposit->id;
        $notificationsRaw[] = [
            'id' => $notifId,
            'title' => 'Setoran Divalidasi',
            'desc' => "Setoran Rp " . number_format($deposit->amount, 0, ',', '.') . " telah divalidasi.",
            'timestamp' => Carbon::parse($deposit->validation_date),
            'icon' => 'tabler-check',
            'color' => 'success',
            'url' => route('masterdata.deposit-transactions.index'),
        ];
    }
}

// Sort by timestamp descending
usort($notificationsRaw, function($a, $b) {
    return $b['timestamp'] <=> $a['timestamp'];
});

// Take top 5 for dropdown, all for modal
$allNotifications = $notificationsRaw;
$navNotifications = array_slice($notificationsRaw, 0, 5);
$unreadCount = count($notificationsRaw); // Total unique
@endphp

<style>
.hover-lift-subtle {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.hover-lift-subtle:hover {
    transform: translateY(-2px) scale(1.01);
    box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15) !important;
}
.notif-item {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.notif-item.removing {
    opacity: 0;
    transform: scale(0.95);
}
</style>

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
<div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
  <a href="{{ url('/') }}" class="app-brand-link">
    <span class="app-brand-logo demo">@include('_partials.macros')</span>
    <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName') }}</span>
  </a>

  <!-- Display menu close icon only for horizontal-menu with navbar-full -->
  @if (isset($menuHorizontal))
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
    <i class="icon-base ti tabler-x icon-sm d-flex align-items-center justify-content-center"></i>
  </a>
  @endif
</div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
<div
  class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
  <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
    <i class="icon-base ti tabler-menu-2 icon-md"></i>
  </a>
</div>
@endif

<div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

  @if (!isset($menuHorizontal))
  <!-- Search -->
  <div class="navbar-nav align-items-center flex-grow-1 position-relative me-3">
      <div class="nav-item d-flex align-items-center w-100">
          <i class="ti tabler-search ti-md text-muted me-2"></i>
          <input type="text" id="global-search-input" class="form-control border-0 shadow-none bg-transparent ps-1" placeholder="Cari PKS, Titik Parkir, User... (Ctrl+K atau Ctrl+/)" autocomplete="off">
      </div>
      
      {{-- Dropdown Hasil Pencarian --}}
      <div id="global-search-results" class="dropdown-menu dropdown-menu-start w-100 mt-2 shadow-lg border-0 py-2" style="display: none; position: absolute; top: 100%; left: 0; max-height: 400px; overflow-y: auto;">
          <div class="text-center py-3" id="global-search-loading" style="display: none;">
              <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
          </div>
          <div id="global-search-content">
              {{-- Hasil AJAX akan dirender di sini --}}
          </div>
      </div>
  </div>
  <!-- /Search -->
  @endif

  <ul class="navbar-nav flex-row align-items-center ms-md-auto">
    @if (isset($menuHorizontal))
    <!-- Search -->
    <li class="nav-item navbar-search-wrapper btn btn-text-secondary btn-icon rounded-pill">
      <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
      </a>
    </li>
    <!-- /Search -->
    @endif

    <!-- Language (Removed) -->

    @if ($configData['hasCustomizer'] == true)
    <!-- Style Switcher -->
    <li class="nav-item dropdown-style-switcher dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill" id="nav-theme"
        href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="icon-base ti icon-22px theme-icon-active text-heading
        @if(isset($configData['theme']) && $configData['theme'] === 'dark')
            tabler-moon-stars
        @elseif(isset($configData['theme']) && $configData['theme'] === 'system')
            tabler-device-desktop-analytics
        @else
            tabler-sun
        @endif"></i>
        <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
        <li>
          <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light"
            aria-pressed="false">
            <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark" aria-pressed="true">
            <span><i class="icon-base ti tabler-moon-stars icon-22px me-3" data-icon="moon-stars"></i>Dark</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
            aria-pressed="false">
            <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                data-icon="device-desktop-analytics"></i>System</span>
          </button>
        </li>
      </ul>
    </li>
    <!-- / Style Switcher-->
    @endif

    <!-- Quick links  -->
    <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <i class="icon-base ti tabler-layout-grid-add icon-22px text-heading"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end p-0">
        <div class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">Shortcuts</h6>
            <a href="javascript:void(0)"
              class="dropdown-shortcuts-add py-2 btn btn-text-secondary rounded-pill btn-icon" data-bs-toggle="tooltip"
              data-bs-placement="top" title="Add shortcuts"><i
                class="icon-base ti tabler-plus icon-20px text-heading"></i></a>
          </div>
        </div>
        <div class="dropdown-shortcuts-list scrollable-container">
          @foreach(array_chunk($shortcuts, 2) as $chunk)
          <div class="row row-bordered overflow-visible g-0">
            @foreach($chunk as $shortcut)
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti {{ $shortcut['icon'] }} icon-26px text-heading"></i>
              </span>
              <a href="{{ $shortcut['url'] }}" class="stretched-link">{{ $shortcut['name'] }}</a>
              <small>{{ $shortcut['desc'] }}</small>
            </div>
            @endforeach
            @if(count($chunk) == 1)
            <div class="dropdown-shortcuts-item col empty-col"></div>
            @endif
          </div>
          @endforeach
        </div>
      </div>
    </li>
    <!-- Quick links -->

    <!-- Notification -->
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <span class="position-relative">
          <i class="icon-base ti tabler-bell icon-22px text-heading"></i>
          @if($unreadCount > 0)
          <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
          @endif
        </span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end p-0">
        <li class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">Notification</h6>
            <div class="d-flex align-items-center h6 mb-0">
              <span class="badge bg-label-primary me-2 notif-count-badge">{{ $unreadCount }} New</span>
              <a href="javascript:void(0)" class="dropdown-notifications-all p-2 btn btn-icon" data-bs-toggle="tooltip"
                data-bs-placement="top" title="Mark all as read" id="mark-all-read-btn"><i
                  class="icon-base ti tabler-mail-opened text-heading"></i></a>
            </div>
          </div>
        </li>
        <li class="dropdown-notifications-list scrollable-container">
          <ul class="list-group list-group-flush" id="dropdown-notif-list">
            @forelse($navNotifications as $notif)
            <li class="list-group-item list-group-item-action dropdown-notifications-item notif-item" data-notif-id="{{ $notif['id'] }}">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar">
                    <span class="avatar-initial rounded-circle bg-label-{{ $notif['color'] }}"><i class="icon-base ti {{ $notif['icon'] }}"></i></span>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <a href="{{ $notif['url'] }}" class="text-body d-block text-decoration-none">
                    <h6 class="mb-1 small fw-bold">{{ $notif['title'] }}</h6>
                    <small class="mb-1 d-block text-body">{{ $notif['desc'] }}</small>
                  </a>
                  <small class="text-body-secondary">{{ $notif['timestamp']->diffForHumans() }}</small>
                </div>
                <div class="flex-shrink-0 dropdown-notifications-actions">
                  <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                      class="badge badge-dot"></span></a>
                  <a href="javascript:void(0)" class="dropdown-notifications-archive btn-dismiss-notif" data-id="{{ $notif['id'] }}"><span
                      class="icon-base ti tabler-x text-danger"></span></a>
                </div>
              </div>
            </li>
            @empty
            <li class="list-group-item text-center p-4 empty-notif-state">
              <span class="text-muted">Tidak ada notifikasi baru</span>
            </li>
            @endforelse
          </ul>
        </li>
        <li class="border-top">
          <div class="d-grid p-4">
            <a class="btn btn-primary btn-sm d-flex justify-content-center" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#premiumNotificationsModal">
              <small class="align-middle fw-bold">Lihat Semua Notifikasi</small>
            </a>
          </div>
        </li>
      </ul>
    </li>
    <!--/ Notification -->
    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown ms-1">
        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
                @if (Auth::user() && Auth::user()->img)
                    <img src="{{ asset('storage/' . Auth::user()->img) }}" alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 100%; height: 100%;" />
                @elseif (Auth::user())
                    <span class="avatar-initial rounded-circle bg-primary text-white fw-bold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                @else
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="rounded-circle" />
                @endif
            </div>
        </a>
        
        <ul class="dropdown-menu dropdown-menu-end mt-3 py-2 rounded-3 shadow-lg border-0" style="min-width: 230px;">
            @if (Auth::check())
            <li>
                <a class="dropdown-item px-4 py-3" href="{{ route('profile.index') }}">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                                @if (Auth::user()->img)
                                    <img src="{{ asset('storage/' . Auth::user()->img) }}" alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 100%; height: 100%;" />
                                @else
                                    <span class="avatar-initial rounded-circle bg-primary text-white fw-bold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <span class="fw-bold d-block text-dark">{{ Str::limit(Auth::user()->name, 15) }}</span>
                            <small class="text-muted text-uppercase fw-medium" style="font-size: 0.7rem;">{{ str_replace('_', ' ', Auth::user()->role) }}</small>
                        </div>
                    </div>
                </a>
            </li>
            <li><div class="dropdown-divider border-light"></div></li>
            
            <li>
                <a class="dropdown-item px-4 py-2" href="{{ route('profile.index') }}">
                    <i class="icon-base ti tabler-user ti-md me-3 text-muted"></i><span class="align-middle fw-medium">Profil Saya</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item px-4 py-2" href="{{ route('profile.settings') }}">
                    <i class="icon-base ti tabler-settings ti-md me-3 text-muted"></i><span class="align-middle fw-medium">Pengaturan Akun</span>
                </a>
            </li>
            
            <li><div class="dropdown-divider border-light my-2"></div></li>
            
            <li class="px-3 pb-2 pt-1">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                </form>
                <button type="button" class="btn btn-danger w-100 fw-bold d-flex justify-content-center align-items-center shadow-sm" id="logout-button">
                    <i class="icon-base ti tabler-logout me-2"></i> Keluar
                </button>
            </li>
            @else
            <li>
                <div class="d-grid px-2 pt-2 pb-1">
                    <a class="btn btn-sm btn-danger d-flex" href="{{ route('login') }}">
                        <small class="align-middle">Login</small>
                        <i class="icon-base ti tabler-login ms-2 icon-14px"></i>
                    </a>
                </div>
            </li>
            @endif
        </ul>
    </li>
    <!--/ User -->
  </ul>
</div>

<script type="module">
        document.addEventListener("DOMContentLoaded", function() {

    // --- A. LOGIKA NOTIFIKASI TEMPORARI (LOCALSTORAGE) ---
    const dismissedNotifs = JSON.parse(localStorage.getItem('dismissed_notifs') || '[]');
    
    function refreshNotifications() {
        let allIds = new Set();
        let totalItems = 0;
        
        // Sembunyikan elemen yang sudah di dismiss
        document.querySelectorAll('.notif-item').forEach(el => {
            let id = el.getAttribute('data-notif-id');
            if (id) {
                allIds.add(id);
                if (dismissedNotifs.includes(id)) {
                    el.style.display = 'none';
                } else {
                    totalItems++;
                }
            }
        });
        
        // Hitung jumlah unik yang belum di-dismiss
        let activeCount = 0;
        allIds.forEach(id => {
            if (!dismissedNotifs.includes(id)) activeCount++;
        });
        
        // Update badge count
        document.querySelectorAll('.notif-count-badge').forEach(el => {
            el.innerText = activeCount + ' New';
        });
        
        // Update lonceng merah
        let bellDot = document.querySelector('.badge-notifications');
        if(bellDot) {
            bellDot.style.display = activeCount > 0 ? 'inline-block' : 'none';
        }
        
        // Tampilkan empty state jika tidak ada notif (Dropdown)
        let dropdownList = document.getElementById('dropdown-notif-list');
        if(dropdownList) {
            let visibleInDropdown = dropdownList.querySelectorAll('.notif-item:not([style*="display: none"])').length;
            let emptyState = dropdownList.querySelector('.empty-notif-state');
            if (visibleInDropdown === 0 && !emptyState) {
                dropdownList.insertAdjacentHTML('beforeend', '<li class="list-group-item text-center p-4 empty-notif-state"><span class="text-muted">Tidak ada notifikasi baru</span></li>');
            } else if (visibleInDropdown > 0 && emptyState) {
                emptyState.remove();
            }
        }
        
        // Tampilkan empty state jika tidak ada notif (Modal)
        let modalList = document.querySelector('#premiumNotificationsModal .row');
        if(modalList) {
            let visibleInModal = modalList.querySelectorAll('.notif-item:not([style*="display: none"])').length;
            let modalEmptyState = modalList.querySelector('.modal-empty-state');
            if (visibleInModal === 0 && !modalEmptyState) {
                modalList.insertAdjacentHTML('beforeend', `
                <div class="col-12 text-center py-5 modal-empty-state" style="animation: fadeIn 0.5s ease;">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-4" style="width: 100px; height: 100px; box-shadow: 0 8px 20px -6px rgba(115, 103, 240, 0.3);">
                        <i class="ti tabler-bell-z text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-dark fw-bold mb-2">Semua Bersih! ✨</h5>
                    <p class="text-muted mb-0">Anda sudah membaca semua notifikasi. Kerja bagus!</p>
                </div>
                `);
            } else if (visibleInModal > 0 && modalEmptyState) {
                modalEmptyState.remove();
            }
        }
    }
    
    // Inisialisasi awal
    refreshNotifications();
    
    // Event listener tombol dismiss (X atau Trash)
    document.querySelectorAll('.btn-dismiss-notif').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let id = this.getAttribute('data-id');
            if (!dismissedNotifs.includes(id)) {
                dismissedNotifs.push(id);
                localStorage.setItem('dismissed_notifs', JSON.stringify(dismissedNotifs));
            }
            
            // Animasi menghilang
            let items = document.querySelectorAll(`.notif-item[data-notif-id="${id}"]`);
            items.forEach(item => {
                item.classList.add('removing');
                setTimeout(() => {
                    item.style.display = 'none';
                    refreshNotifications();
                }, 300);
            });
        });
    });

    // Mark all as read
    const markAllReadBtn = document.getElementById('mark-all-read-btn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            document.querySelectorAll('.notif-item').forEach(el => {
                let id = el.getAttribute('data-notif-id');
                if (id && !dismissedNotifs.includes(id)) {
                    dismissedNotifs.push(id);
                }
            });
            localStorage.setItem('dismissed_notifs', JSON.stringify(dismissedNotifs));
            
            document.querySelectorAll('.notif-item').forEach(item => {
                item.classList.add('removing');
                setTimeout(() => {
                    item.style.display = 'none';
                    refreshNotifications();
                }, 300);
            });
        });
    }

    // Pindahkan modal ke body untuk menghindari masalah z-index dan backdrop "macet"
    const premiumModal = document.getElementById('premiumNotificationsModal');
    if (premiumModal) {
        document.body.appendChild(premiumModal);
    }

    // --- B. LOGIKA GLOBAL SEARCH (LIVE AJAX) ---
    const searchInput = document.getElementById('global-search-input');
    const searchResults = document.getElementById('global-search-results');
    const searchContent = document.getElementById('global-search-content');
    const searchLoading = document.getElementById('global-search-loading');
    let searchTimeout = null;

    if (searchInput) {
        document.addEventListener('keydown', function(e) {
            if (((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') || (e.ctrlKey && e.key === '/')) {
                if (document.activeElement !== searchInput) {
                    e.preventDefault();
                    searchInput.focus();
                }
            }
            if (e.key === 'Escape') {
                searchResults.style.display = 'none';
                searchInput.blur();
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchResults.style.display = 'block';
            searchContent.innerHTML = '';
            searchLoading.style.display = 'block';

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`{{ route('masterdata.global-search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchLoading.style.display = 'none';
                        let html = '';

                        if (data.length === 0) {
                            html = `<div class="px-4 py-3 text-center text-muted">
                                        <i class="ti tabler-zoom-in ti-lg opacity-50 mb-2"></i>
                                        Tidak ada data ditemukan untuk "<b>${query}</b>"
                                    </div>`;
                        } else {
                            data.forEach(item => {
                                html += `
                                <a class="dropdown-item px-4 py-2" href="${item.url}">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle ${item.icon}"><i class="${item.icon.split(' ')[0]}"></i></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 text-dark fw-bold">${item.title}</h6>
                                            <small class="text-muted">${item.subtitle}</small>
                                        </div>
                                    </div>
                                </a>`;
                            });
                        }
                        searchContent.innerHTML = html;
                    })
                    .catch(error => {
                        searchLoading.style.display = 'none';
                        searchContent.innerHTML = `<div class="px-4 py-3 text-center text-danger small">Gagal memuat data. Periksa koneksi atau rute.</div>`;
                    });
            }, 500); 
        });

        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                searchResults.style.display = 'block';
            }
        });
    }

    // ==========================================
    // 3. LOGOUT CONFIRMATION (SWEETALERT / NATIVE)
    // ==========================================
    const logoutBtn = document.getElementById('logout-button');
    const logoutForm = document.getElementById('logout-form');

    if (logoutBtn && logoutForm) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault(); 
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Keluar Aplikasi?',
                    text: "Sesi Anda akan diakhiri dan harus login kembali.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Keluar!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger rounded-pill px-4 mx-2',
                        cancelButton: 'btn btn-outline-secondary rounded-pill px-4 mx-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutForm.submit(); 
                    }
                });
            } else {
                if (confirm('Keluar Aplikasi? Sesi Anda akan diakhiri dan harus login kembali.')) {
                    logoutForm.submit();
                }
            }
        });
    }
});
</script>

<!-- Premium Notifications Modal -->
<div class="modal fade" id="premiumNotificationsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);">
      <div class="modal-header border-0 pb-0 pt-4 px-4 px-md-5 d-flex align-items-center">
        <div class="d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 54px; height: 54px;">
                <i class="ti tabler-bell-ringing text-primary icon-28px"></i>
            </div>
            <div>
                <h4 class="modal-title fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">Pusat Notifikasi</h4>
                <small class="text-muted fw-medium">Pantau aktivitas dan pengingat terbaru Anda</small>
            </div>
        </div>
        <button type="button" class="btn-close shadow-none bg-secondary bg-opacity-10 rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 p-md-5 pt-4">
        <div class="row gy-3">
            @forelse($allNotifications as $notif)
            <div class="col-12 notif-item" data-notif-id="{{ $notif['id'] }}">
                <div class="card border-0 shadow-none bg-label-secondary bg-opacity-25 rounded-4 overflow-hidden position-relative hover-lift-subtle">
                    <div class="card-body p-3 p-md-4 d-flex align-items-center">
                        <div class="flex-shrink-0 me-3 me-md-4">
                            <div class="avatar avatar-md">
                                <span class="avatar-initial rounded-circle bg-{{ $notif['color'] }} shadow-sm text-white">
                                    <i class="icon-base ti {{ $notif['icon'] }} icon-md"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <a href="{{ $notif['url'] }}" class="text-decoration-none d-block">
                                <h6 class="mb-1 text-dark fw-bold fs-6">{{ $notif['title'] }}</h6>
                                <p class="mb-2 text-muted small" style="line-height: 1.4;">{{ $notif['desc'] }}</p>
                                <span class="badge bg-white text-dark shadow-sm border small px-2 py-1"><i class="ti tabler-clock icon-14px me-1 text-muted"></i> {{ $notif['timestamp']->diffForHumans() }}</span>
                            </a>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <button class="btn btn-icon btn-text-secondary rounded-circle btn-dismiss-notif" data-id="{{ $notif['id'] }}" data-bs-toggle="tooltip" title="Hapus Notifikasi ini">
                                <i class="ti tabler-trash text-danger icon-22px"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 modal-empty-state">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-4" style="width: 100px; height: 100px; box-shadow: 0 8px 20px -6px rgba(115, 103, 240, 0.3);">
                    <i class="ti tabler-bell-z text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-dark fw-bold mb-2">Semua Bersih! ✨</h5>
                <p class="text-muted mb-0">Anda sudah membaca semua notifikasi. Kerja bagus!</p>
            </div>
            @endforelse
        </div>
      </div>
    </div>
  </div>
</div>