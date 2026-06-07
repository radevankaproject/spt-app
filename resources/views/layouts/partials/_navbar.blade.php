<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    
    {{-- ========================================== --}}
    {{-- 1. TOMBOL HAMBURGER (MOBILE)               --}}
    {{-- ========================================== --}}
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="icon-base ri ri-menu-line ri-24px"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
        
        {{-- ========================================== --}}
        {{-- 2. FORM PENCARIAN GLOBAL (LIVE SEARCH)     --}}
        {{-- ========================================== --}}
        <div class="navbar-nav align-items-center flex-grow-1 position-relative me-3">
            <div class="nav-item d-flex align-items-center w-100">
                <i class="ri ri-search-line ri-22px text-muted me-2"></i>
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

        <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">
            
            {{-- ========================================== --}}
            {{-- 3. THEME SWITCHER (DARK/LIGHT/SYSTEM)      --}}
            {{-- ========================================== --}}
            <li class="nav-item dropdown-style-switcher dropdown">
                <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                    id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="icon-base ri ri-sun-line ri-22px theme-icon-active text-warning"></i>
                    <span id="nav-theme-text" class="d-none">Toggle theme</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end mt-2 rounded-3 shadow-sm py-2" aria-labelledby="nav-theme-text">
                    <li>
                        <button type="button" class="dropdown-item align-items-center theme-switch-btn" data-bs-theme-value="light">
                            <i class="icon-base ri ri-sun-line ri-20px me-3 text-warning" data-icon="sun-line"></i><span class="fw-medium">Light</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item align-items-center theme-switch-btn" data-bs-theme-value="dark">
                            <i class="icon-base ri ri-moon-clear-fill ri-20px me-3 text-primary" data-icon="moon-clear-fill"></i><span class="fw-medium">Dark</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item align-items-center theme-switch-btn" data-bs-theme-value="system">
                            <i class="icon-base ri ri-computer-line ri-20px me-3 text-secondary" data-icon="computer-line"></i><span class="fw-medium">System</span>
                        </button>
                    </li>
                </ul>
            </li>

            {{-- ========================================== --}}
            {{-- 4. USER PROFILE DROPDOWN                   --}}
            {{-- ========================================== --}}
            <li class="nav-item navbar-dropdown dropdown-user dropdown ms-1">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        @if (Auth::user()->img)
                            <img src="{{ asset('storage/' . Auth::user()->img) }}" alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 100%; height: 100%;" />
                        @else
                            <span class="avatar-initial rounded-circle bg-primary text-white fw-bold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        @endif
                    </div>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end mt-3 py-2 rounded-3 shadow-lg border-0" style="min-width: 230px;">
                    <li>
                        <a class="dropdown-item px-4 py-3" href="{{ route('profile.edit') }}">
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
                        <a class="dropdown-item px-4 py-2" href="{{ route('profile.edit') }}">
                            <i class="icon-base ri ri-user-3-line ri-20px me-3 text-muted"></i><span class="align-middle fw-medium">Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item px-4 py-2" href="{{ route('profile.settings') }}">
                            <i class="icon-base ri ri-settings-4-line ri-20px me-3 text-muted"></i><span class="align-middle fw-medium">Pengaturan Akun</span>
                        </a>
                    </li>
                    
                    <li><div class="dropdown-divider border-light my-2"></div></li>
                    
                    <li class="px-3 pb-2 pt-1">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                        </form>
                        {{-- Tombol Logout --}}
                        <button type="button" class="btn btn-danger w-100 fw-bold d-flex justify-content-center align-items-center shadow-sm" id="logout-button">
                            <i class="icon-base ri ri-logout-box-r-line me-2"></i> Keluar
                        </button>
                    </li>
                </ul>
            </li>
            </ul>
    </div>
</nav>

{{-- ========================================== --}}
{{-- 5. JAVASCRIPT (TEMA & PENCARIAN)           --}}
{{-- ========================================== --}}
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {

    // --- A. LOGIKA TEMA (DARK/LIGHT MODE) ---
    const htmlEl = document.documentElement;
    const themeIcon = document.querySelector('.theme-icon-active');
    const themeBtns = document.querySelectorAll('.theme-switch-btn');
    
    // Ambil dari LocalStorage, default 'light'
    let currentTheme = localStorage.getItem('spkp-theme') || 'light';
    applyTheme(currentTheme);

    function applyTheme(theme) {
        localStorage.setItem('spkp-theme', theme);
        let actualTheme = theme;

        if (theme === 'system') {
            actualTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        // Terapkan ke attribute HTML
        htmlEl.setAttribute('data-bs-theme', actualTheme);

        // Ubah Icon di Navbar Utama
        if (theme === 'dark') themeIcon.className = 'icon-base ri ri-moon-clear-fill ri-22px theme-icon-active text-primary';
        else if (theme === 'light') themeIcon.className = 'icon-base ri ri-sun-line ri-22px theme-icon-active text-warning';
        else themeIcon.className = 'icon-base ri ri-computer-line ri-22px theme-icon-active text-secondary';

        // Update UI tombol di dalam dropdown
        themeBtns.forEach(btn => {
            if (btn.getAttribute('data-bs-theme-value') === theme) {
                btn.classList.add('active', 'bg-primary', 'text-white');
                btn.querySelector('i').className = btn.querySelector('i').className.replace(/text-\w+/, 'text-white');
            } else {
                btn.classList.remove('active', 'bg-primary', 'text-white');
                let iconClass = 'text-secondary';
                if(btn.getAttribute('data-bs-theme-value') === 'light') iconClass = 'text-warning';
                if(btn.getAttribute('data-bs-theme-value') === 'dark') iconClass = 'text-primary';
                btn.querySelector('i').className = btn.querySelector('i').className.replace(/text-white/, iconClass);
            }
        });
    }

    themeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            applyTheme(this.getAttribute('data-bs-theme-value'));
        });
    });


    // --- B. LOGIKA GLOBAL SEARCH (LIVE AJAX) ---
    const searchInput = document.getElementById('global-search-input');
    const searchResults = document.getElementById('global-search-results');
    const searchContent = document.getElementById('global-search-content');
    const searchLoading = document.getElementById('global-search-loading');
    let searchTimeout = null;

    // Shortcut Keyboard (Tekan Ctrl+K atau Ctrl+/)
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

    // Menutup dropdown kalau klik di luar area
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Event saat ngetik (Debounce 500ms)
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
            // ✅ PASTIKAN ROUTE INI SUDAH ADA DI WEB.PHP (di dalam grup 'masterdata.')
            fetch(`{{ route('masterdata.global-search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchLoading.style.display = 'none';
                    let html = '';

                    if (data.length === 0) {
                        html = `<div class="px-4 py-3 text-center text-muted">
                                    <i class="ri ri-search-eye-line ri-2x opacity-50 mb-2"></i>
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

    // Munculkan lagi kalau input di-klik
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            searchResults.style.display = 'block';
        }
    });

    // ==========================================
    // 3. SWEETALERT LOGOUT GANTENG
    // ==========================================
    const logoutBtn = document.getElementById('logout-button');
    const logoutForm = document.getElementById('logout-form');

    if (logoutBtn && logoutForm) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Cegah reload langsung
            
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
                    logoutForm.submit(); // Eksekusi Form Logout
                }
            });
        });
    }

});
</script>
@endpush