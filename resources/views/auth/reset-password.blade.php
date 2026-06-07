@extends('layouts.guest')

@section('title', 'Buat Kata Sandi Baru')

@push('styles')
<style>
    body {
        background: #f8fafc !important; /* Soft off-white */
        color: #334155;
        position: relative;
        overflow-x: hidden;
    }
    
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

    .premium-card {
        background: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.03) !important;
        border-radius: 24px !important;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.05) !important;
        overflow: hidden;
        position: relative;
        padding: 1rem;
    }
    
    .premium-card h4 {
        color: #1e293b !important;
        font-weight: 700;
    }
    .text-muted-custom {
        color: #64748b !important;
    }

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

</style>
@endpush

@section('content')
    <div class="bg-blob-1"></div>
    <div class="bg-blob-2"></div>

    <div class="card premium-card animate__animated animate__fadeInUp animate__fast">
        <div class="card-body p-sm-5 p-4">

            <div class="app-brand justify-content-center mb-4 mt-2">
                <a href="{{ url('/') }}" class="app-brand-link gap-3">
                    <span class="app-brand-logo demo logo-wrapper">
                        <img src="{{ asset('logo.png') }}" alt="Logo SiPKS" class="logo-glow" style="height: 55px;">
                    </span>
                </a>
            </div>
            
            <div class="text-center mb-5">
                 <h4 class="mb-2 fw-bold" style="letter-spacing: 0.5px;">Buat Kata Sandi Baru 🔐</h4>
                 <p class="text-muted-custom mb-0">Silakan masukkan kata sandi baru untuk akun Anda.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="formAuthentication" action="{{ route('password.store') }}" method="POST">
                @csrf
                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                
                <!-- Email Address -->
                <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                <div class="mb-4">
                    <div class="form-password-toggle">
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <input type="password" id="password" class="form-control" name="password" 
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                    aria-describedby="password" required autofocus />
                                <label for="password">Kata Sandi Baru</label>
                            </div>
                            <span class="input-group-text cursor-pointer"><i class="icon-base ri ri-eye-off-line"></i></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-password-toggle">
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" 
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                    aria-describedby="password_confirmation" required />
                                <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                            </div>
                            <span class="input-group-text cursor-pointer"><i class="icon-base ri ri-eye-off-line"></i></span>
                        </div>
                    </div>
                </div>

                <div class="mb-2 mt-5">
                    <button class="btn btn-premium d-grid w-100" type="submit">
                        <span class="d-flex align-items-center justify-content-center">
                            Reset Kata Sandi <i class="ri-refresh-line ms-2"></i>
                        </span>
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="d-flex justify-content-center align-items-center text-decoration-none">
                    <i class="ri-arrow-left-s-line scaleX-n1-rtl me-1"></i>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
@endsection
