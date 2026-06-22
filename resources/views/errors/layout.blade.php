<!doctype html>
<html lang="id" class="light-style layout-wide customizer-hide" dir="ltr" data-skin="default" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>@yield('title') - SiPKS</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    
    {{-- Core CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />

    <style>
        :root {
            --mesh-color-1: hsla(238, 100%, 80%, 0.3);
            --mesh-color-2: hsla(280, 100%, 80%, 0.3);
            --mesh-color-3: hsla(190, 100%, 80%, 0.3);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --text-dark: #233446;
        }

        body {
            font-family: 'Outfit', sans-serif !important;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #f8f9fa;
            background-image: 
                radial-gradient(at 0% 0%, var(--mesh-color-1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, var(--mesh-color-2) 0px, transparent 50%),
                radial-gradient(at 100% 100%, var(--mesh-color-3) 0px, transparent 50%),
                radial-gradient(at 0% 100%, var(--mesh-color-1) 0px, transparent 50%);
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .dark-style body {
            background-color: #222834;
            --mesh-color-1: hsla(238, 80%, 30%, 0.3);
            --mesh-color-2: hsla(280, 80%, 30%, 0.3);
            --mesh-color-3: hsla(190, 80%, 30%, 0.3);
            --glass-bg: rgba(34, 40, 52, 0.7);
            --glass-border: rgba(255, 255, 255, 0.05);
            --text-dark: #e4e6eb;
        }

        .glass-container {
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 2rem;
            padding: 4rem 3rem 3rem 3rem;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            max-width: 650px;
            width: 90%;
            position: relative;
            z-index: 10;
            animation: floatUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes floatUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-wrapper {
            display: flex;
            justify-content: center;
            margin-top: -6rem;
            margin-bottom: 1.5rem;
        }

        .logo-frame {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(105, 108, 255, 0.4), inset 0 0 10px rgba(105, 108, 255, 0.1);
            border: 4px solid #fff;
            position: relative;
            z-index: 2;
            animation: glowPulse 2s infinite alternate;
        }

        @keyframes glowPulse {
            0% { box-shadow: 0 0 15px rgba(105, 108, 255, 0.4), inset 0 0 10px rgba(105, 108, 255, 0.1); }
            100% { box-shadow: 0 0 30px rgba(105, 108, 255, 0.8), inset 0 0 15px rgba(105, 108, 255, 0.3); }
        }

        .logo-frame img {
            width: 70px;
            height: auto;
            object-fit: contain;
        }

        .branding-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 2rem;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.85;
        }

        .dark-style .branding-title { color: #e4e6eb; }

        .error-code {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #696cff, #8a8dff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            display: inline-block;
            text-shadow: 0px 10px 30px rgba(105, 108, 255, 0.3);
        }

        .error-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .error-message {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .dark-style .error-message { color: #a3a4cc; }

        .btn-home {
            background: linear-gradient(135deg, #696cff, #5a5ce6);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(105, 108, 255, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(105, 108, 255, 0.4);
            color: white;
        }

        .floating-shape {
            position: absolute;
            z-index: 1;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.15), rgba(105, 108, 255, 0.02));
            backdrop-filter: blur(5px);
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 { width: 300px; height: 300px; top: -5%; left: -5%; animation-delay: 0s; }
        .shape-2 { width: 200px; height: 200px; bottom: 5%; right: 5%; animation-delay: -2s; }
        .shape-3 { width: 80px; height: 80px; top: 30%; right: 20%; animation-delay: -4s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0); }
            50% { transform: translateY(-25px) rotate(10deg); }
        }
    </style>
</head>

<body>
    <!-- Floating background shapes -->
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <div class="glass-container">
        <!-- Logo Wrapper with glow frame -->
        <div class="logo-wrapper">
            <div class="logo-frame">
                <img src="{{ asset('logo.png') }}" alt="Logo SiPKS" onerror="this.src='{{ asset('assets/img/illustrations/image-light.png') }}'">
            </div>
        </div>
        
        <!-- Branding Title -->
        <div class="branding-title">
            Sistem Informasi Kerjasama Perparkiran<br>
            <span style="font-size: 0.9em; opacity: 0.7;">UPT Perparkiran Dinas Perhubungan Kota Pekanbaru</span>
        </div>

        <div class="error-code">@yield('code')</div>
        <h3 class="error-title">@yield('title')</h3>
        <p class="error-message">@yield('message')</p>
        
        <a href="{{ url('/') }}" class="btn-home">
            <i class="ti tabler-home-4"></i> Kembali ke Beranda
        </a>
    </div>

</body>
</html>
