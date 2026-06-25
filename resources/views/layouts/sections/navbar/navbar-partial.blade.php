@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Controllers\UserShortcutController;

$userRole = Auth::check() ? Auth::user()->role : null;

$shortcuts = [];
$notificationsRaw = [];

if (Auth::check()) {
    $user = Auth::user();
    
    // 1. Load Shortcuts
    if ($user->shortcuts && $user->shortcuts->count() > 0) {
        $shortcuts = $user->shortcuts->map(function($s) {
            return ['name' => $s->name, 'url' => $s->url, 'icon' => $s->icon, 'desc' => $s->description];
        })->toArray();
    } else {
        $shortcuts = UserShortcutController::getDefaultShortcuts($userRole);
    }

    // 2. Load DB Notifications
    foreach ($user->unreadNotifications as $notif) {
        $notificationsRaw[] = [
            'id' => $notif->id,
            'title' => $notif->data['title'] ?? 'Notifikasi',
            'desc' => $notif->data['desc'] ?? '',
            'timestamp' => $notif->created_at,
            'icon' => $notif->data['icon'] ?? 'tabler-bell',
            'color' => $notif->data['color'] ?? 'primary',
            'url' => $notif->data['url'] ?? '#',
        ];
    }
}

// Sort by timestamp descending
usort($notificationsRaw, function($a, $b) {
    return $b['timestamp'] <=> $a['timestamp'];
});

$allNotifications = $notificationsRaw;
$navNotifications = array_slice($notificationsRaw, 0, 5);
$unreadCount = count($notificationsRaw);
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
/* Custom Thin Scrollbar for Search Modal */
.search-modal-scroll::-webkit-scrollbar {
    width: 6px;
}
.search-modal-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.search-modal-scroll::-webkit-scrollbar-thumb {
    background: rgba(150, 150, 150, 0.2);
    border-radius: 10px;
}
.search-modal-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(150, 150, 150, 0.4);
}
/* Premium Smoky Backdrop for Modals */
.modal-backdrop.show {
    opacity: 0.75 !important;
    backdrop-filter: blur(12px);
    background-color: rgba(15, 23, 42, 0.8) !important;
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

  <!-- Letak kiri untuk menu toggle, dll -->
  
  <!-- Search -->
  <div class="navbar-nav align-items-center flex-grow-1 position-relative me-3">
      <div class="nav-item d-flex align-items-center w-100" data-bs-toggle="modal" data-bs-target="#globalSearchModal" style="cursor: pointer;" data-bs-placement="bottom" title="Buka Pencarian Global">
          <i class="ti tabler-search ti-md text-muted me-2"></i>
          <span class="text-muted d-none d-md-inline-block">Search (CTRL+K)</span>
          <span class="text-muted d-inline-block d-md-none">Search...</span>
      </div>
  </div>
  <!-- /Search -->
  
  <ul class="navbar-nav flex-row align-items-center ms-md-auto">

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
              class="dropdown-shortcuts-add py-2 btn btn-text-secondary rounded-pill btn-icon" data-bs-toggle="modal" data-bs-target="#manageShortcutsModal"
              data-bs-placement="top" title="Manage shortcuts"><i
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
            <li>
                <a class="dropdown-item px-4 py-2" href="{{ route('profile.index', ['tab' => 'aktivitas']) }}">
                    <i class="icon-base ti tabler-activity ti-md me-3 text-muted"></i><span class="align-middle fw-medium">Aktivitas Akun</span>
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

    // --- B. LOGIKA GLOBAL SEARCH (MODAL) ---
    const searchModalEl = document.getElementById('globalSearchModal');
    const searchInput = document.getElementById('modal-global-search-input');
    const searchContent = document.getElementById('modal-global-search-content');
    const searchLoading = document.getElementById('modal-global-search-loading');
    let searchTimeout = null;
    let searchModal = null;
    
    const searchEmptyStateHtml = `
      <div class="p-4" id="modal-global-search-empty">
          <div class="row g-4">
              <div class="col-md-6">
                  <p class="text-muted text-uppercase mb-3" style="font-size: 0.7rem; letter-spacing: 1px;">Popular Searches</p>
                  <div class="list-group list-group-flush">
                      <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0 px-2 py-2 rounded text-body d-flex align-items-center mb-1" onclick="document.getElementById('modal-global-search-input').value='Parkir'; document.getElementById('modal-global-search-input').dispatchEvent(new Event('input'))">
                          <i class="ti tabler-map-pin me-3 text-muted" style="font-size: 1.2rem;"></i> Titik Parkir
                      </a>
                      <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0 px-2 py-2 rounded text-body d-flex align-items-center mb-1" onclick="document.getElementById('modal-global-search-input').value='PKS'; document.getElementById('modal-global-search-input').dispatchEvent(new Event('input'))">
                          <i class="ti tabler-file-type-doc me-3 text-muted" style="font-size: 1.2rem;"></i> Perjanjian PKS
                      </a>
                      <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0 px-2 py-2 rounded text-body d-flex align-items-center mb-1" onclick="document.getElementById('modal-global-search-input').value='User'; document.getElementById('modal-global-search-input').dispatchEvent(new Event('input'))">
                          <i class="ti tabler-users me-3 text-muted" style="font-size: 1.2rem;"></i> Daftar User
                      </a>
                      <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0 px-2 py-2 rounded text-body d-flex align-items-center mb-1" onclick="document.getElementById('modal-global-search-input').value='Jalan'; document.getElementById('modal-global-search-input').dispatchEvent(new Event('input'))">
                          <i class="ti tabler-road me-3 text-muted" style="font-size: 1.2rem;"></i> Ruas Jalan
                      </a>
                  </div>
              </div>
              <div class="col-md-6">
                  <p class="text-muted text-uppercase mb-3" style="font-size: 0.7rem; letter-spacing: 1px;">Apps & Pages</p>
                  <div class="list-group list-group-flush">
                      <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action border-0 px-2 py-2 rounded text-body d-flex align-items-center mb-1">
                          <i class="ti tabler-user-circle me-3 text-muted" style="font-size: 1.2rem;"></i> Profil Saya
                      </a>
                      <a href="{{ route('masterdata.parking-locations.index') }}" class="list-group-item list-group-item-action border-0 px-2 py-2 rounded text-body d-flex align-items-center mb-1">
                          <i class="ti tabler-map me-3 text-muted" style="font-size: 1.2rem;"></i> Kelola Parkir
                      </a>
                      <a href="{{ route('masterdata.agreements.index') }}" class="list-group-item list-group-item-action border-0 px-2 py-2 rounded text-body d-flex align-items-center mb-1">
                          <i class="ti tabler-file-type-doc me-3 text-muted" style="font-size: 1.2rem;"></i> Kelola PKS
                      </a>
                      <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action border-0 px-2 py-2 rounded text-body d-flex align-items-center mb-1">
                          <i class="ti tabler-home me-3 text-muted" style="font-size: 1.2rem;"></i> Dashboard
                      </a>
                  </div>
              </div>
          </div>
      </div>
    `;

    // Inisialisasi modal jika ada
    if (searchModalEl) {
        document.body.appendChild(searchModalEl);
        if (typeof bootstrap !== 'undefined') {
            searchModal = new bootstrap.Modal(searchModalEl);
        }
        
        // Auto focus input ketika modal dibuka
        searchModalEl.addEventListener('shown.bs.modal', function () {
            if (searchInput) {
                searchInput.focus();
                // Jika sudah ada isinya, langsung trigger pencarian
                if (searchInput.value.trim().length >= 2) {
                    searchInput.dispatchEvent(new Event('input'));
                }
            }
        });
        
        // Clear isi saat ditutup agar fresh
        searchModalEl.addEventListener('hidden.bs.modal', function () {
            if (searchInput) {
                searchInput.value = '';
            }
            if (searchContent) {
                searchContent.innerHTML = searchEmptyStateHtml;
            }
        });
    }

    // Ctrl+K atau Ctrl+/ untuk membuka modal pencarian
    document.addEventListener('keydown', function(e) {
        if (((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') || (e.ctrlKey && e.key === '/')) {
            e.preventDefault();
            if (searchModal) {
                searchModal.show();
            }
        }
    });

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length < 2) {
                searchContent.innerHTML = searchEmptyStateHtml;
                return;
            }

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
                            html = `<div class="px-4 py-5 my-4 text-center">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-label-secondary rounded-circle mb-4" style="width: 80px; height: 80px;">
                                            <i class="ti tabler-file-search text-muted" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h5 class="text-dark fw-bold mb-2">Tidak Ditemukan</h5>
                                        <p class="text-muted mb-0">Maaf, tidak ada data yang cocok dengan kata kunci "<b>${query}</b>"</p>
                                    </div>`;
                        } else {
                            // Group data by subtitle
                            const groupedData = data.reduce((acc, item) => {
                                const key = item.subtitle || 'Lainnya';
                                if (!acc[key]) acc[key] = [];
                                acc[key].push(item);
                                return acc;
                            }, {});

                            for (const [category, items] of Object.entries(groupedData)) {
                                html += `<div class="px-4 py-2 mt-2">
                                            <p class="text-muted text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">${category}</p>
                                            <div class="list-group list-group-flush">`;
                                items.forEach(item => {
                                    let avatarDisplay = '';
                                    if (item.avatar_type === 'image') {
                                        avatarDisplay = `<div class="avatar avatar-sm me-3"><span class="avatar-initial rounded-circle overflow-hidden shadow-sm">${item.avatar_html}</span></div>`;
                                    } else {
                                        avatarDisplay = `<div class="avatar avatar-sm me-3"><span class="avatar-initial rounded-circle ${item.color_class} shadow-sm">${item.avatar_html}</span></div>`;
                                    }
                                    
                                    html += `
                                    <a class="list-group-item list-group-item-action border-0 px-2 py-2 rounded text-body mb-1" href="${item.url}">
                                        <div class="d-flex align-items-center">
                                            ${avatarDisplay}
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.85rem;">${item.title}</h6>
                                                <small class="text-muted d-block" style="font-size: 0.7rem;">${item.subtitle}</small>
                                            </div>
                                            <div class="flex-shrink-0 text-muted ms-2">
                                                <i class="ti tabler-chevron-right icon-sm opacity-50"></i>
                                            </div>
                                        </div>
                                    </a>`;
                                });
                                html += `</div></div>`;
                            }
                        }
                        searchContent.innerHTML = html;
                    })
                    .catch(error => {
                        searchLoading.style.display = 'none';
                        searchContent.innerHTML = `<div class="px-4 py-5 text-center text-danger small"><i class="ti tabler-alert-triangle ti-xl mb-3 d-block"></i>Gagal memuat data. Periksa koneksi atau rute.</div>`;
                    });
            }, 500); 
        });
    }

    // ==========================================
    // 3. LOGOUT CONFIRMATION (PREMIUM MODAL)
    // ==========================================
    const logoutBtn = document.getElementById('logout-button');
    const logoutForm = document.getElementById('logout-form');
    const premiumLogoutModal = document.getElementById('premiumLogoutModal');
    
    if (premiumLogoutModal) {
        document.body.appendChild(premiumLogoutModal);
    }

    if (logoutBtn && logoutForm) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault(); 
            
            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(document.getElementById('premiumLogoutModal'));
                modal.show();
            } else {
                if (confirm('Keluar Aplikasi? Sesi Anda akan diakhiri dan harus login kembali.')) {
                    logoutForm.submit();
                }
            }
        });
        
        const confirmLogoutBtn = document.getElementById('confirm-logout-btn');
        if (confirmLogoutBtn) {
            confirmLogoutBtn.addEventListener('click', function() {
                // Tampilkan animasi loading di tombol sebelum submit
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Keluar...';
                this.classList.add('disabled');
                logoutForm.submit();
            });
        }
    }

    // ==========================================
    // 4. LOGIKA MANAGE SHORTCUTS
    // ==========================================
    const manageShortcutsModal = document.getElementById('manageShortcutsModal');
    if (manageShortcutsModal) {
        document.body.appendChild(manageShortcutsModal);
        
        let shortcutsLoaded = false;
        
        manageShortcutsModal.addEventListener('show.bs.modal', function () {
            if (shortcutsLoaded) return;
            
            fetch("{{ route('shortcuts.get') }}")
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('shortcuts-checkbox-container');
                let html = '';
                if(data.length === 0) {
                    html = '<div class="col-12 text-center text-muted py-3">Tidak ada shortcut tersedia untuk role Anda.</div>';
                }
                data.forEach((item, index) => {
                    const checked = item.is_selected ? 'checked' : '';
                    html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="form-check custom-option custom-option-icon h-100 position-relative">
                            <label class="form-check-label custom-option-content h-100 p-3" for="shortcutOpt${index}">
                                <span class="custom-option-body text-center">
                                    <i class="icon-base ti ${item.icon} mb-2 fs-3 text-primary"></i>
                                    <span class="custom-option-title d-block mb-1 fw-bold">${item.name}</span>
                                    <small class="text-muted d-block">${item.desc}</small>
                                </span>
                                <input class="form-check-input" type="checkbox" value='${JSON.stringify(item)}' id="shortcutOpt${index}" ${checked} />
                            </label>
                        </div>
                    </div>
                    `;
                });
                container.innerHTML = html;
                document.getElementById('manage-shortcuts-loading').style.display = 'none';
                document.getElementById('form-manage-shortcuts').style.display = 'block';
                shortcutsLoaded = true;
            })
            .catch(err => {
                document.getElementById('manage-shortcuts-loading').innerHTML = '<p class="text-danger">Gagal memuat menu shortcut.</p>';
            });
        });
        
        document.getElementById('form-manage-shortcuts').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-shortcuts');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            btn.disabled = true;
            
            const checkedBoxes = document.querySelectorAll('#shortcuts-checkbox-container input[type="checkbox"]:checked');
            const selectedShortcuts = Array.from(checkedBoxes).map(cb => JSON.parse(cb.value));
            
            fetch("{{ route('shortcuts.save') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ shortcuts: selectedShortcuts })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload immediately to apply new shortcuts
                } else {
                    alert('Gagal menyimpan.');
                    btn.innerHTML = 'Simpan Pilihan';
                    btn.disabled = false;
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan jaringan.');
                btn.innerHTML = 'Simpan Pilihan';
                btn.disabled = false;
            });
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


<!-- Manage Shortcuts Modal -->
<div class="modal fade" id="manageShortcutsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);">
      <div class="modal-header border-0 pb-0 pt-4 px-4 px-md-5 d-flex align-items-center">
        <div class="d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 54px; height: 54px;">
                <i class="ti tabler-layout-grid-add text-primary icon-28px"></i>
            </div>
            <div>
                <h4 class="modal-title fw-bold mb-0 text-dark">Kelola Shortcut</h4>
                <small class="text-muted fw-medium">Pilih menu akses cepat sesuai preferensi Anda</small>
            </div>
        </div>
        <button type="button" class="btn-close shadow-none bg-secondary bg-opacity-10 rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 p-md-5 pt-4" id="manage-shortcuts-body">
         <div class="text-center py-4" id="manage-shortcuts-loading">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Memuat opsi menu...</p>
         </div>
         <form id="form-manage-shortcuts" style="display:none;">
            <div class="row g-3" id="shortcuts-checkbox-container">
                <!-- Injected via AJAX -->
            </div>
            <div class="text-end mt-4 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary me-2 rounded-pill fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4 shadow-sm" id="btn-save-shortcuts">Simpan Pilihan</button>
            </div>
         </form>
      </div>
    </div>
  </div>
</div>

<!-- Premium Logout Modal -->
<div class="modal fade" id="premiumLogoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);">
      <div class="modal-body p-4 text-center pt-5">
        <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle mb-4" style="width: 80px; height: 80px; box-shadow: 0 8px 20px -6px rgba(234, 84, 85, 0.3);">
            <i class="ti tabler-logout text-danger" style="font-size: 2.5rem;"></i>
        </div>
        <h4 class="text-dark fw-bold mb-2">Keluar Aplikasi?</h4>
        <p class="text-muted mb-4">Sesi Anda akan diakhiri dan Anda harus masuk kembali untuk mengakses sistem.</p>
        
        <div class="d-flex flex-column gap-2">
            <button type="button" class="btn btn-danger rounded-pill fw-bold py-2 shadow-sm" id="confirm-logout-btn">
                Ya, Keluar!
            </button>
            <button type="button" class="btn btn-label-secondary rounded-pill fw-bold py-2" data-bs-dismiss="modal">
                Batal
            </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Global Search Modal (Premium Vuexy Style) -->
<div class="modal fade" id="globalSearchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg mt-5">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; background: rgba(255,255,255,0.98); backdrop-filter: blur(25px); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.2) !important;">
      <div class="modal-header border-0 p-0 align-items-center">
        <div class="input-group input-group-merge border-0" style="padding: 0.5rem 1rem;">
          <span class="input-group-text border-0 bg-transparent text-primary ps-3"><i class="ti tabler-search ti-md"></i></span>
          <input type="text" id="modal-global-search-input" class="form-control border-0 shadow-none fs-5 py-3" placeholder="Search (CTRL+K)" autocomplete="off" style="background: transparent;">
          <span class="input-group-text border-0 bg-transparent text-muted px-2 d-flex align-items-center">
            <span class="d-none d-md-inline-block text-muted fw-medium me-3" style="font-size: 0.75rem;">[esc]</span>
            <button type="button" class="btn-close m-0 position-relative" style="right: 0.5rem;" data-bs-dismiss="modal" aria-label="Close"></button>
          </span>
        </div>
      </div>
      <hr class="m-0 text-muted" style="opacity: 0.15;">
      <div class="modal-body p-0 search-modal-scroll" style="min-height: 250px; max-height: 60vh; overflow-y: auto; overflow-x: hidden;">
          <div class="text-center py-5" id="modal-global-search-loading" style="display: none;">
              <div class="spinner-border spinner-border-lg text-primary" role="status"></div>
          </div>
          <div id="modal-global-search-content" class="rounded-bottom pb-3">
              <!-- Empty state injected by JS -->
          </div>
      </div>
    </div>
  </div>
</div>