@extends('layouts.guest')

@section('title', 'Login')

@push('styles')
<style>
    /* Meng-override background bawaan dari layout guest */
    body {
        background: #f8fafc !important; /* Soft off-white */
        color: #334155;
        position: relative;
        overflow-x: hidden;
    }
    
    /* Subtle decorative blobs untuk versi terang */
    .bg-blob-1 {
        position: absolute;
        top: -10%;
        left: -5%;
        width: 50vw;
        height: 50vw;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99,102,241,0.05) 0%, rgba(255,255,255,0) 70%);
        z-index: -1;
        animation: floatLight 15s ease-in-out infinite;
    }
    .bg-blob-2 {
        position: absolute;
        bottom: -20%;
        right: -10%;
        width: 60vw;
        height: 60vw;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(168,85,247,0.05) 0%, rgba(255,255,255,0) 70%);
        z-index: -1;
        animation: floatLight 18s ease-in-out infinite reverse;
    }
    
    @keyframes floatLight {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-30px) scale(1.05); }
    }

    /* Premium Clean Card */
    .premium-card {
        background: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.03) !important;
        border-radius: 24px !important;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.05) !important;
        overflow: hidden;
        position: relative;
        padding: 1rem;
    }
    
    /* Typography adjustments */
    .premium-card h4 {
        color: #1e293b !important;
        font-weight: 700;
    }
    .text-muted-custom {
        color: #64748b !important;
    }

    /* Hapus override form-floating agar tidak bentrok dengan Vuexy */


    /* Primary Button Premium */
    .btn-premium {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border: none !important;
        color: white !important;
        font-weight: 600;
        font-size: 1.05rem;
        letter-spacing: 0.5px;
        border-radius: 12px;
        padding: 14px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        z-index: 1;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .btn-premium::after {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        opacity: 0;
        z-index: -1;
        transition: opacity 0.3s ease;
    }
    .btn-premium:hover::after {
        opacity: 1;
    }
    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -5px rgba(79, 70, 229, 0.5);
    }
    
    /* Custom Checkbox */
    .premium-card .form-check-input {
        background-color: #f1f5f9;
        border: 1px solid #cbd5e1;
    }
    .premium-card .form-check-input:checked {
        background-color: #6366f1;
        border-color: #6366f1;
    }
    
    /* Links */
    .text-premium-link {
        color: #6366f1 !important;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .text-premium-link:hover {
        color: #4338ca !important;
        text-decoration: underline;
    }
    
    /* Custom Invalid Feedback (Error) */
    .custom-invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.4rem;
        font-size: 0.85em;
        color: #ef4444; 
        padding-left: 0.5rem;
    }

    /* Logo animation */
    .logo-wrapper {
        position: relative;
        display: inline-block;
        padding: 10px;
        background: #f8fafc;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .logo-glow {
        transition: transform 0.5s ease;
    }
    .logo-glow:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
    <div class="bg-blob-1"></div>
    <div class="bg-blob-2"></div>

    <div class="card premium-card animate__animated animate__fadeInUp animate__fast">
        <div class="card-body p-sm-5 p-4">

            {{-- ✅ Branding & Logo Aplikasi --}}
            <div class="app-brand justify-content-center mb-4 mt-2">
                <a href="{{ url('/') }}" class="app-brand-link gap-3">
                    <span class="app-brand-logo demo logo-wrapper">
                        <img src="{{ asset('logo.png') }}" alt="Logo SiPKS" class="logo-glow" style="height: 55px;">
                    </span>
                </a>
            </div>
            
            <div class="text-center mb-5">
                 <h4 class="mb-2 fw-bold" style="letter-spacing: 0.5px;">SiPKS Pekanbaru</h4>
                 <p class="text-muted-custom mb-0">Sistem Informasi Perjanjian Kerjasama Perparkiran.</p>
                 <p class="text-muted-custom mb-0 mt-1" style="font-size: 0.9rem;">Silakan masuk ke akun Anda.</p>
            </div>

            {{-- Menampilkan status (misal: setelah reset password) --}}
            @if (session('status'))
                <div class="alert alert-success mb-4" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;">
                    {{ session('status') }}
                </div>
            @endif

            <form id="formAuthentication" action="{{ route('login') }}" method="POST">
                @csrf
                
                {{-- Input Username --}}
                <div class="mb-4">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Masukkan username Anda" value="{{ old('username') }}" required autofocus autocomplete="username" />
                        <label for="username">Username</label>
                    </div>
                </div>

                {{-- Input Password --}}
                <div class="mb-4">
                    <div class="form-password-toggle">
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <input type="password" id="password" class="form-control"
                                    name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    required autocomplete="current-password" />
                                <label for="password">Kata Sandi</label>
                            </div>
                            <span class="input-group-text cursor-pointer"><i class="icon-base ri ri-eye-off-line"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Opsi Ingat Saya & Lupa Password --}}
                <div class="d-flex justify-content-between align-items-center mb-5 mt-3">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember-me" />
                        <label class="form-check-label text-muted-custom" for="remember-me"> Ingat Saya </label>
                    </div>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-premium-link">
                        <small>Lupa Kata Sandi?</small>
                    </a>
                    @endif
                </div>

                {{-- Tombol Login --}}
                <div class="mb-2">
                    <button class="btn btn-premium d-grid w-100" type="submit">
                        <span class="d-flex align-items-center justify-content-center">
                            Masuk <i class="ri-arrow-right-line ms-2"></i>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
