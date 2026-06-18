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
    </style>
  @endif

  <!-- Include Scripts for customizer, helper, analytics, config -->
  <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scriptsIncludes' . $isFront)

  <!-- Premium Preloader CSS -->
  <style>
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
  </style>
</head>

<body>
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
            setTimeout(() => { preloader.remove(); }, 500);
        }
    });
  </script>
</body>

</html>
