<!DOCTYPE html>
@php
  use Illuminate\Support\Str;
  use App\Helpers\Helpers;

  $menuFixed =
      $configData['layout'] === 'vertical'
          ? $menuFixed ?? ''
          : ($configData['layout'] === 'front'
              ? ''
              : $configData['headerType']);
  $navbarType =
      $configData['layout'] === 'vertical'
          ? $configData['navbarType']
          : ($configData['layout'] === 'front'
              ? 'layout-navbar-fixed'
              : '');
  $isFront = ($isFront ?? '') == true ? 'Front' : '';
  $contentLayout = isset($container) ? ($container === 'container-xxl' ? 'layout-compact' : 'layout-wide') : '';

  // Get skin name from configData - only applies to admin layouts
  $isAdminLayout = !Str::contains($configData['layout'] ?? '', 'front');
  $skinName = $isAdminLayout ? $configData['skinName'] ?? 'default' : 'default';

  // Get semiDark value from configData - only applies to admin layouts
  $semiDarkEnabled = $isAdminLayout && filter_var($configData['semiDark'] ?? false, FILTER_VALIDATE_BOOLEAN);

  // Generate primary color CSS if color is set
  $primaryColorCSS = '';
  if (isset($configData['color']) && $configData['color']) {
      $primaryColorCSS = Helpers::generatePrimaryColorCSS($configData['color']);
  }

@endphp

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}"
  class="{{ $navbarType ?? '' }} {{ $contentLayout ?? '' }} {{ $menuFixed ?? '' }} {{ $menuCollapsed ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}"
  dir="{{ $configData['textDirection'] }}" data-skin="{{ $skinName }}" data-assets-path="{{ asset('/assets') . '/' }}"
  data-base-url="{{ url('/') }}" data-framework="laravel" data-template="{{ $configData['layout'] }}-menu-template"
  data-bs-theme="{{ $configData['theme'] }}" @if ($isAdminLayout && $semiDarkEnabled) data-semidark-menu="true" @endif>

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>
    SiPKS - @yield('title')
  </title>
  <meta name="description"
    content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
  <meta name="keywords"
    content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}" />
  <meta property="og:title" content="{{ config('variables.ogTitle') ? config('variables.ogTitle') : '' }}" />
  <meta property="og:type" content="{{ config('variables.ogType') ? config('variables.ogType') : '' }}" />
  <meta property="og:url" content="{{ config('variables.productPage') ? config('variables.productPage') : '' }}" />
  <meta property="og:image" content="{{ config('variables.ogImage') ? config('variables.ogImage') : '' }}" />
  <meta property="og:description"
    content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
  <meta property="og:site_name"
    content="{{ config('variables.creatorName') ? config('variables.creatorName') : '' }}" />
  <meta name="robots" content="noindex, nofollow" />
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <!-- Canonical SEO -->
  <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}" />
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

  <!-- Include Styles -->
  <!-- $isFront is used to append the front layout styles only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/styles' . $isFront)

  @if (
      $primaryColorCSS &&
          (config('custom.custom.primaryColor') ||
              isset($_COOKIE['admin-primaryColor']) ||
              isset($_COOKIE['front-primaryColor'])))
    <!-- Primary Color Style -->
    <style id="primary-color-style">
      {!! $primaryColorCSS !!}
    
    /* Fintech Card Styles */
    .fintech-card {
        background: linear-gradient(135deg, #2b4162 0%, #fa9c7a 100%);
        border-radius: 20px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(43, 65, 98, 0.2);
    }
    .fintech-card::before {
        content: '';
        position: absolute;
        top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .fintech-card-green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        box-shadow: 0 15px 35px rgba(17, 153, 142, 0.2);
    }
    .fintech-card-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.2);
    }
    .fintech-card-blue {
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        box-shadow: 0 15px 35px rgba(44, 83, 100, 0.2);
    }
    .fintech-card-orange {
        background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
        box-shadow: 0 15px 35px rgba(245, 175, 25, 0.2);
    }
    
    .credit-card-ui {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 20px;
    }
    
    .stat-glow-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

  
    /* Fintech Card Typography Overrides */
    .fintech-card h1, .fintech-card h2, .fintech-card h3, .fintech-card h4, .fintech-card h5, .fintech-card h6, 
    .fintech-card p, .fintech-card span, .fintech-card div {
        color: #ffffff !important;
    }
    .fintech-card .text-muted {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    .fintech-card .text-primary, .fintech-card .text-info, .fintech-card .text-success, 
    .fintech-card .text-warning, .fintech-card .text-danger, .fintech-card .text-dark, 
    .fintech-card .text-secondary {
        color: #ffffff !important;
    }
    .fintech-card .bg-white.text-primary, .fintech-card .bg-white.text-dark {
        color: #696cff !important; /* Keep badge text readable if background is white */
    }
    .fintech-card .bg-white.text-primary * {
        color: #696cff !important;
    }
    .fintech-card .bg-white.text-dark * {
        color: #233446 !important;
    }
    .fintech-card .stat-glow-icon.bg-white {
        color: #696cff !important;
    }
    .fintech-card .stat-glow-icon.bg-white i {
        color: #696cff !important;
    }

  
    /* Gold Blink Animation */
    .gold-blink {
        color: #ffd700 !important;
        opacity: 0.8 !important;
        animation: goldBlink 1.5s infinite alternate !important;
    }
    @keyframes goldBlink {
        0% { opacity: 0.3; filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.4)); }
        100% { opacity: 1; filter: drop-shadow(0 0 20px rgba(255, 215, 0, 1)); }
    }

  </style>
  @endif

  <!-- Include Scripts for customizer, helper, analytics, config -->
  <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scriptsIncludes' . $isFront)

  <!-- Premium Global Styles & Preloader CSS -->
  <style>
    /* Premium Thin Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(140, 140, 140, 0.3); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(140, 140, 140, 0.5); }
    * { scrollbar-width: thin; scrollbar-color: rgba(140, 140, 140, 0.3) transparent; }

    .premium-preloader { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); z-index: 99999; display: flex; justify-content: center; align-items: center; transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.5s; }
    .dark-style .premium-preloader { background: rgba(34, 40, 52, 0.98); }
    .preloader-content { text-align: center; display: flex; flex-direction: column; align-items: center; }
    
    .m3-wavy-wrapper { position: relative; width: 150px; height: 150px; display: flex; justify-content: center; align-items: center; margin-bottom: 20px; }
    
    .m3-wavy-svg { position: absolute; width: 100%; height: 100%; animation: m3-spin 4s linear infinite; }
    
    .m3-wavy-center { position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; }
    
    .m3-wavy-logo { width: 45px; height: auto; margin-bottom: 5px; animation: pulse-logo 2s ease-in-out infinite; }
    
    #m3-wavy-progress { animation: m3-dash 1.5s ease-in-out infinite; }

    @keyframes m3-spin { 100% { transform: rotate(360deg); } }
    @keyframes m3-dash {
        0% { stroke-dasharray: 1, 320; stroke-dashoffset: 0; }
        50% { stroke-dasharray: 180, 320; stroke-dashoffset: -50; }
        100% { stroke-dasharray: 180, 320; stroke-dashoffset: -320; }
    }
    @keyframes pulse-logo { 0% { transform: scale(0.95); opacity: 0.9; } 50% { transform: scale(1.05); opacity: 1; } 100% { transform: scale(0.95); opacity: 0.9; } }
    
    .typewriter-wrapper { display: inline-block; }
    .typewriter-text {
        font-family: inherit;
        font-weight: 600;
        color: #566a7f;
        letter-spacing: 1px;
        overflow: hidden;
        white-space: nowrap;
        margin: 0 auto;
        border-right: 0.15em solid #666cff;
        width: 0;
        animation: typing 1.5s steps(14, end) infinite alternate, blink-caret 0.75s step-end infinite;
    }
    .dark-style .typewriter-text { color: #a3a4cc; }
    
    @keyframes typing { from { width: 0; } to { width: 135px; } }
    @keyframes blink-caret { from, to { border-color: transparent; } 50% { border-color: #666cff; } }
    
    /* Pause animations while preloader is visible */
    body.is-loading .animate__animated {
        animation-play-state: paused !important;
    }

    /* Premium Global Form Floating Label - Cut Border */
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .form-floating > .form-select ~ label,
    .form-floating > .form-control:valid ~ label {
        background-color: #ffffff !important;
        padding: 0 8px !important;
        left: 5px !important;
        height: auto !important;
        line-height: 1 !important;
        border-radius: 4px;
        color: inherit;
        transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem) !important;
    }
    .dark-style .form-floating > .form-control:focus ~ label,
    .dark-style .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .dark-style .form-floating > .form-select ~ label,
    .dark-style .form-floating > .form-control:valid ~ label {
        background-color: #222834 !important;
    }

    /* Make form-floating inputs more fit (kurangi padding atas yang terlalu lebar) */
    .form-floating > .form-control,
    .form-floating > .form-select {
        height: 45px !important;
        min-height: 45px !important;
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
        display: flex;
        align-items: center;
    }
    .form-floating > label {
        padding: 0.75rem !important;
    }

    /* Premium Dashboard Styles */
    .hero-mesh-primary {
        background-color: #696cff;
        background-image: 
            radial-gradient(at 0% 0%, hsla(238, 100%, 75%, 1) 0px, transparent 50%),
            radial-gradient(at 100% 0%, hsla(238, 100%, 65%, 1) 0px, transparent 50%),
            radial-gradient(at 100% 100%, hsla(238, 100%, 55%, 1) 0px, transparent 50%),
            radial-gradient(at 0% 100%, hsla(238, 100%, 45%, 1) 0px, transparent 50%);
        border-radius: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    
    @keyframes moveMesh {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .hero-mesh-primary::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 10%, transparent 10%);
        background-size: 20px 20px;
        animation: moveMesh 20s linear infinite;
        opacity: 0.3;
        z-index: 0;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 1.25rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        transition: all 0.3s ease;
    }
    .dark-style .glass-card {
        background: rgba(34, 40, 52, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1);
        border: 1px solid rgba(105, 108, 255, 0.3);
    }

    .icon-glass {
        width: 55px; height: 55px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.4));
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .dark-style .icon-glass {
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
    }

    .premium-list-item {
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
        border-radius: 8px;
    }
    .premium-list-item:hover {
        background-color: rgba(105, 108, 255, 0.04);
        border-left: 4px solid #696cff;
        transform: scale(1.01);
    }

    /* Premium Profile Gold Glow */
    .gold-frame-glow {
        border: 4px solid #ffd700 !important;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.6), inset 0 0 10px rgba(255, 215, 0, 0.4);
        animation: goldGlow 2.5s infinite alternate;
        padding: 4px !important;
        background: linear-gradient(135deg, #fff 0%, #fff 100%) !important;
    }
    @keyframes goldGlow {
        0% { box-shadow: 0 0 10px rgba(255, 215, 0, 0.4), inset 0 0 5px rgba(255, 215, 0, 0.2); }
        100% { box-shadow: 0 0 25px rgba(255, 215, 0, 0.9), inset 0 0 15px rgba(255, 215, 0, 0.6); }
    }

    /* Waving Hand Animation */
    .waving-hand {
        display: inline-block;
        transform-origin: 70% 70%;
        animation: wave-animation 2.5s infinite;
    }
    @keyframes wave-animation {
        0% { transform: rotate( 0.0deg) }
        10% { transform: rotate(14.0deg) }  
        20% { transform: rotate(-8.0deg) }
        30% { transform: rotate(14.0deg) }
        40% { transform: rotate(-4.0deg) }
        50% { transform: rotate(10.0deg) }
        60% { transform: rotate( 0.0deg) }
        100% { transform: rotate( 0.0deg) }
    }
  
    /* Fintech Card Styles */
    .fintech-card {
        background: linear-gradient(135deg, #2b4162 0%, #fa9c7a 100%);
        border-radius: 20px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(43, 65, 98, 0.2);
    }
    .fintech-card::before {
        content: '';
        position: absolute;
        top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .fintech-card-green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        box-shadow: 0 15px 35px rgba(17, 153, 142, 0.2);
    }
    .fintech-card-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.2);
    }
    .fintech-card-blue {
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        box-shadow: 0 15px 35px rgba(44, 83, 100, 0.2);
    }
    .fintech-card-orange {
        background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
        box-shadow: 0 15px 35px rgba(245, 175, 25, 0.2);
    }
    
    .credit-card-ui {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 20px;
    }
    
    .stat-glow-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

  
    /* Fintech Card Typography Overrides */
    .fintech-card h1, .fintech-card h2, .fintech-card h3, .fintech-card h4, .fintech-card h5, .fintech-card h6, 
    .fintech-card p, .fintech-card span, .fintech-card div {
        color: #ffffff !important;
    }
    .fintech-card .text-muted {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    .fintech-card .text-primary, .fintech-card .text-info, .fintech-card .text-success, 
    .fintech-card .text-warning, .fintech-card .text-danger, .fintech-card .text-dark, 
    .fintech-card .text-secondary {
        color: #ffffff !important;
    }
    .fintech-card .bg-white.text-primary, .fintech-card .bg-white.text-dark {
        color: #696cff !important; /* Keep badge text readable if background is white */
    }
    .fintech-card .bg-white.text-primary * {
        color: #696cff !important;
    }
    .fintech-card .bg-white.text-dark * {
        color: #233446 !important;
    }
    .fintech-card .stat-glow-icon.bg-white {
        color: #696cff !important;
    }
    .fintech-card .stat-glow-icon.bg-white i {
        color: #696cff !important;
    }

  
    /* Gold Blink Animation */
    .gold-blink {
        color: #ffd700 !important;
        opacity: 0.8 !important;
        animation: goldBlink 1.5s infinite alternate !important;
    }
    @keyframes goldBlink {
        0% { opacity: 0.3; filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.4)); }
        100% { opacity: 1; filter: drop-shadow(0 0 20px rgba(255, 215, 0, 1)); }
    }

  </style>
</head>

<body class="is-loading">
  <!-- Premium Global Preloader -->
  <div id="global-premium-preloader" class="premium-preloader">
      <div class="preloader-content">
          <div class="m3-wavy-wrapper">
              <svg class="m3-wavy-svg" viewBox="0 0 120 120">
                  <path id="m3-wavy-track" fill="none" stroke="rgba(102,108,255,0.15)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                  <path id="m3-wavy-progress" fill="none" stroke="#666cff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <div class="m3-wavy-center">
                  <img src="{{ asset('logo.png') }}" alt="SiPKS" class="m3-wavy-logo" onerror="this.src='{{ asset('assets/img/illustrations/image-light.png') }}'">
              </div>
          </div>
          <div class="typewriter-wrapper mt-2">
              <h5 class="typewriter-text mb-0">Memuat Data...</h5>
          </div>
      </div>
  </div>
  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->

  

  <!-- Include Scripts -->
  <!-- $isFront is used to append the front layout scripts only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scripts' . $isFront)

  <script>
    // Generate Material 3 squiggly/wavy circle path
    function generateWavyCirclePath(cx, cy, R, A, N, resolution = 300) {
        let d = "";
        for(let i=0; i<=resolution; i++) {
            let theta = (i / resolution) * 2 * Math.PI - (Math.PI / 2);
            let waveTheta = (i / resolution) * 2 * Math.PI; 
            let r = R + A * Math.sin(N * waveTheta);
            let x = cx + r * Math.cos(theta);
            let y = cy + r * Math.sin(theta);
            if(i===0) d += `M ${x} ${y} `;
            else d += `L ${x} ${y} `;
        }
        return d;
    }

    const preloader = document.getElementById('global-premium-preloader');
    const track = document.getElementById('m3-wavy-track');
    const prog = document.getElementById('m3-wavy-progress');
    
    if(track && prog) {
        // cx=60, cy=60, radius=50, amplitude=2 (softer), waves=16
        const pathData = generateWavyCirclePath(60, 60, 50, 2, 16);
        track.setAttribute('d', pathData);
        prog.setAttribute('d', pathData);
    }
    
    window.addEventListener('load', function() {
        if (preloader) {
            preloader.style.opacity = '0';
            preloader.style.visibility = 'hidden';
            setTimeout(() => { 
                preloader.remove(); 
                document.body.classList.remove('is-loading');
            }, 500);
        } else {
            document.body.classList.remove('is-loading');
        }
    });
  </script>
</body>

</html>
