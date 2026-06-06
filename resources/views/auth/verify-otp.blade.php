@extends('layouts.guest')

@section('title', 'Verifikasi OTP')

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

    .otp-input {
        width: 3rem;
        height: 3rem;
        text-align: center;
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
    }
    .otp-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        outline: none;
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
                 <h4 class="mb-2 fw-bold" style="letter-spacing: 0.5px;">Verifikasi OTP 📱</h4>
                 <p class="text-muted-custom mb-0">Masukkan 6 digit kode yang telah kami kirimkan ke nomor</p>
                 <p class="text-dark fw-bold mb-0 mt-1">{{ session('reset_phone_number') ?? request('phone_number') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="formAuthentication" action="{{ route('password.otp.verify') }}" method="POST">
                @csrf
                <input type="hidden" name="phone_number" value="{{ session('reset_phone_number') ?? request('phone_number') }}">
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <input type="text" class="otp-input form-control text-center" maxlength="1" id="otp-1" autofocus>
                    <input type="text" class="otp-input form-control text-center" maxlength="1" id="otp-2">
                    <input type="text" class="otp-input form-control text-center" maxlength="1" id="otp-3">
                    <input type="text" class="otp-input form-control text-center" maxlength="1" id="otp-4">
                    <input type="text" class="otp-input form-control text-center" maxlength="1" id="otp-5">
                    <input type="text" class="otp-input form-control text-center" maxlength="1" id="otp-6">
                </div>
                <input type="hidden" name="otp_code" id="otp_code">

                <div class="mb-2 mt-4">
                    <button class="btn btn-premium d-grid w-100" type="submit">
                        <span class="d-flex align-items-center justify-content-center">
                            Verifikasi <i class="ri-check-line ms-2"></i>
                        </span>
                    </button>
                </div>
            </form>

            <form action="{{ route('password.otp.send') }}" method="POST" class="text-center mt-3" id="resendForm">
                @csrf
                <input type="hidden" name="phone_number" value="{{ session('reset_phone_number') ?? request('phone_number') }}">
                <p class="text-muted-custom">Belum menerima kode? 
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline text-primary fw-medium" id="resendBtn">Kirim Ulang</button>
                    <span id="countdownText" class="d-none text-muted">dalam <span id="timer">60</span>s</span>
                </p>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('otp_code');
        const form = document.getElementById('formAuthentication');

        // Auto focus next input
        inputs.forEach((input, index) => {
            input.addEventListener('keyup', function(e) {
                if (this.value.length === 1) {
                    if (index < inputs.length - 1) inputs[index + 1].focus();
                }
                
                // Backspace
                if (e.key === 'Backspace' && this.value.length === 0) {
                    if (index > 0) inputs[index - 1].focus();
                }
            });

            // Prevent non-numeric
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                let pasteData = e.clipboardData.getData('text/plain').replace(/[^0-9]/g, '');
                if (pasteData.length > 0) {
                    for (let i = 0; i < inputs.length; i++) {
                        if (i < pasteData.length) {
                            inputs[i].value = pasteData[i];
                            if (i === inputs.length - 1 || i === pasteData.length - 1) {
                                inputs[i].focus();
                            }
                        }
                    }
                }
            });
        });

        // Set hidden input value before submit
        form.addEventListener('submit', function(e) {
            let otp = '';
            inputs.forEach(input => {
                otp += input.value;
            });
            hiddenInput.value = otp;
            
            if (otp.length < 6) {
                e.preventDefault();
                alert('Silakan lengkapi 6 digit kode OTP');
            }
        });

        // Countdown timer for resend
        const resendBtn = document.getElementById('resendBtn');
        const countdownText = document.getElementById('countdownText');
        const timerSpan = document.getElementById('timer');
        
        // Disable resend button initially and start countdown
        let timeLeft = 60; // 60 seconds
        resendBtn.disabled = true;
        resendBtn.classList.add('text-muted');
        resendBtn.classList.remove('text-primary');
        countdownText.classList.remove('d-none');
        
        const timer = setInterval(() => {
            timeLeft--;
            timerSpan.innerText = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                resendBtn.disabled = false;
                resendBtn.classList.remove('text-muted');
                resendBtn.classList.add('text-primary');
                countdownText.classList.add('d-none');
            }
        }, 1000);
    });
</script>
@endpush
