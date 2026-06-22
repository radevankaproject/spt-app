@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.guest')

@section('title', 'Verifikasi OTP - SiPKS Pekanbaru')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
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
        border-color: #7367f0; /* Vuexy Primary Color */
        box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.2);
        outline: none;
    }
</style>
@endsection

@section('content')
<!-- Verify OTP -->
<div class="card">
  <div class="card-body">
    <!-- Logo -->
    <div class="app-brand justify-content-center mb-6">
      <a href="{{ url('/') }}" class="app-brand-link">
        <span class="app-brand-logo demo">
            <img src="{{ asset('logo.png') }}" alt="Logo SiPKS" style="height: 40px;">
        </span>
        <span class="app-brand-text demo text-heading fw-bold">SiPKS</span>
      </a>
    </div>
    <!-- /Logo -->
    <h4 class="mb-1">Verifikasi OTP 📱</h4>
    <p class="mb-6">Masukkan 6 digit kode yang telah kami kirimkan ke nomor <br><span class="fw-bold text-heading">{{ session('reset_phone_number') ?? request('phone_number') }}</span></p>

    @if (session('status'))
        <div class="alert alert-success mb-4">
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

    <form id="formAuthentication" class="mb-4" action="{{ route('password.otp.verify') }}" method="POST">
      @csrf
      <input type="hidden" name="phone_number" value="{{ session('reset_phone_number') ?? request('phone_number') }}">
      
      <div class="d-flex justify-content-between gap-2 mb-6">
          <input type="text" class="otp-input form-control text-center px-0" maxlength="1" id="otp-1" autofocus>
          <input type="text" class="otp-input form-control text-center px-0" maxlength="1" id="otp-2">
          <input type="text" class="otp-input form-control text-center px-0" maxlength="1" id="otp-3">
          <input type="text" class="otp-input form-control text-center px-0" maxlength="1" id="otp-4">
          <input type="text" class="otp-input form-control text-center px-0" maxlength="1" id="otp-5">
          <input type="text" class="otp-input form-control text-center px-0" maxlength="1" id="otp-6">
      </div>
      <input type="hidden" name="otp_code" id="otp_code">

      <button class="btn btn-primary d-grid w-100" type="submit" id="submitBtn">
          <span class="indicator-label">Verifikasi</span>
          <span class="indicator-progress d-none">
              <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              Memverifikasi...
          </span>
      </button>
    </form>

    <form action="{{ route('password.otp.send') }}" method="POST" class="text-center mt-4" id="resendForm">
        @csrf
        <input type="hidden" name="phone_number" value="{{ session('reset_phone_number') ?? request('phone_number') }}">
        <p class="text-center">Belum menerima kode? 
            <button type="submit" class="btn btn-link p-0 m-0 align-baseline text-primary fw-medium" id="resendBtn">Kirim Ulang</button>
            <span id="countdownText" class="d-none text-muted">dalam <span id="timer">60</span>s</span>
        </p>
    </form>

    <div class="text-center">
      <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
        <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl me-1_5"></i>
        Kembali ke Login
      </a>
    </div>

  </div>
</div>
<!-- /Verify OTP -->
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('otp_code');
        const form = document.getElementById('formAuthentication');
        const submitBtn = document.getElementById('submitBtn');

        function triggerLoading() {
            submitBtn.disabled = true;
            submitBtn.querySelector('.indicator-label').classList.add('d-none');
            submitBtn.querySelector('.indicator-progress').classList.remove('d-none');
        }

        // Auto focus next input and auto submit
        inputs.forEach((input, index) => {
            input.addEventListener('keyup', function(e) {
                if (this.value.length === 1) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    } else {
                        // last input filled -> auto submit
                        let otp = '';
                        inputs.forEach(i => otp += i.value);
                        if (otp.length === 6) {
                            hiddenInput.value = otp;
                            triggerLoading();
                            form.submit();
                        }
                    }
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
                    let otp = '';
                    for (let i = 0; i < inputs.length; i++) {
                        if (i < pasteData.length) {
                            inputs[i].value = pasteData[i];
                            otp += pasteData[i];
                            if (i === inputs.length - 1 || i === pasteData.length - 1) {
                                inputs[i].focus();
                            }
                        }
                    }
                    if (otp.length === 6) {
                        hiddenInput.value = otp;
                        triggerLoading();
                        form.submit();
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
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Kode Belum Lengkap',
                        text: 'Silakan masukkan 6 digit kode OTP yang kami kirim ke WhatsApp Anda.',
                        icon: 'warning',
                        customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
                        buttonsStyling: false
                    });
                } else {
                    alert('Silakan lengkapi 6 digit kode OTP');
                }
            } else {
                triggerLoading();
            }
        });

        // Countdown timer for resend
        const resendForm = document.getElementById('resendForm');
        const resendBtn = document.getElementById('resendBtn');
        const countdownText = document.getElementById('countdownText');
        const timerSpan = document.getElementById('timer');
        
        resendForm.addEventListener('submit', function() {
            localStorage.removeItem('otpTimerExpiresAt');
        });

        let expiresAt = localStorage.getItem('otpTimerExpiresAt');
        const nowMs = Date.now();
        
        // If no timer or it's expired by more than a long time (safety check), start a new one
        if (!expiresAt || parseInt(expiresAt) < nowMs) {
            // Also check session variable if it's a fresh send from server
            let serverResendTime = {{ session('otp_resend_time', 0) }} * 1000;
            if (serverResendTime > nowMs) {
                expiresAt = serverResendTime;
            } else {
                expiresAt = nowMs + 60000; // default 60s
            }
            localStorage.setItem('otpTimerExpiresAt', expiresAt);
        }
        
        let timeLeft = Math.floor((parseInt(expiresAt) - nowMs) / 1000);
        
        if (timeLeft > 0) {
            resendBtn.disabled = true;
            resendBtn.classList.add('text-muted');
            resendBtn.classList.remove('text-primary');
            countdownText.classList.remove('d-none');
            timerSpan.innerText = timeLeft;
        } else {
            resendBtn.disabled = false;
            resendBtn.classList.remove('text-muted');
            resendBtn.classList.add('text-primary');
            countdownText.classList.add('d-none');
        }
        
        const timer = setInterval(() => {
            let currentLeft = Math.floor((parseInt(expiresAt) - Date.now()) / 1000);
            if (currentLeft <= 0) {
                clearInterval(timer);
                resendBtn.disabled = false;
                resendBtn.classList.remove('text-muted');
                resendBtn.classList.add('text-primary');
                countdownText.classList.add('d-none');
            } else {
                timerSpan.innerText = currentLeft;
            }
        }, 1000);
    });
</script>
@endsection
