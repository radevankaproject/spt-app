<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>@yield('title', 'Sistem Perparkiran') - SPKP</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/remixicon/remixicon.css') }}" />

  <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
    class="template-customizer-theme-css" />
  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

  @stack('styles')

  <style>
    /* Paksa background body terang */
    body {
      background-color: #f4f5fa;
    }

    /* Modifikasi Navbar Horizontal */
    .layout-navbar-horizontal {
      background: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 0 2rem;
      z-index: 100;
    }

    .navbar-brand img {
      height: 40px;
    }

    .nav-link {
      font-weight: 600;
      color: #434a54;
      padding: 1.2rem 1rem !important;
      transition: all 0.2s;
    }

    .nav-link:hover,
    .nav-link.active {
      color: #696cff;
      border-bottom: 3px solid #696cff;
    }

    .content-wrapper {
      padding-top: 2rem;
    }
  </style>
</head>

<body>
  <div class="layout-wrapper layout-content-navbar layout-without-menu">
    <div class="layout-container">

      <div class="layout-page">

        {{-- ✅ NAVBAR HORIZONTAL --}}
        <nav class="layout-navbar-horizontal navbar navbar-expand-lg align-items-center" id="layout-navbar">
          <div class="container-xxl d-flex justify-content-between">

            <div class="navbar-brand d-flex align-items-center">
              <i class="ri ri-car-line ri-2x text-primary me-2"></i>
              <span class="fw-bold fs-4 text-dark">SPKP <span class="fw-light">Pekanbaru</span></span>
            </div>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarCollapse">
              <i class="ri-menu-line ri-2x"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
              <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('leader.dashboard', 'treasurer.dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="ri ri-dashboard-line me-1 align-bottom"></i> Dashboard
                  </a>
                </li>
                @if(Auth::user()->role === 'treasurer')
                <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('masterdata.deposit-transactions.*') ? 'active' : '' }}"
                    href="{{ route('masterdata.deposit-transactions.index') }}">
                    <i class="ri ri-safe-2-line me-1 align-bottom"></i> Validasi Setoran
                  </a>
                </li>
                @endif
                <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('masterdata.deposit-reports.*') ? 'active' : '' }}"
                    href="{{ route('masterdata.deposit-reports.index') }}">
                    <i class="ri ri-file-chart-line me-1 align-bottom"></i> Laporan
                  </a>
                </li>
              </ul>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <li class="nav-item dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center" href="javascript:void(0);"
                  data-bs-toggle="dropdown">
                  <div class="avatar avatar-online me-2">
                    <img
                      src="{{ Auth::user()->img ? asset('storage/' . Auth::user()->img) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=696cff&color=fff' }}"
                      class="w-px-40 h-auto rounded-circle" style="object-fit: cover;" />
                  </div>
                  <div class="d-none d-md-block text-start">
                    <span class="d-block fw-bold" style="line-height: 1;">{{ Str::limit(Auth::user()->name, 15)
                      }}</span>
                    <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ str_replace('_', ' ',
                      Auth::user()->role) }}</small>
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="{{ route('profile.settings') }}">
                      <i class="ri-user-settings-line me-2"></i><span class="align-middle">Pengaturan Profil</span>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button class="dropdown-item text-danger" type="submit">
                        <i class="ri-logout-box-r-line me-2"></i><span class="align-middle">Log Out</span>
                      </button>
                    </form>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">
            @yield('content')
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  @stack('scripts')
</body>

</html>