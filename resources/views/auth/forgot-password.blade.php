@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.guest')

@section('title', 'Lupa Kata Sandi - SiPKS Pekanbaru')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<!-- Forgot Password -->
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
    <h4 class="mb-1">Lupa Kata Sandi? 🔒</h4>
    <p class="mb-6">Masukkan Nomor HP yang terdaftar. Kami akan mengirimkan 6 digit kode OTP untuk mereset kata sandi Anda.</p>

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

    <form id="formAuthentication" class="mb-4" action="{{ route('password.otp.send') }}" method="POST">
      @csrf
      <div class="mb-6 form-control-validation">
        <label for="phone_number" class="form-label">No. Handphone</label>
        <input type="text" class="form-control" id="phone_number" name="phone_number"
          placeholder="0812xxxxxx" value="{{ old('phone_number') }}" required autofocus />
      </div>
      
      <div class="mb-6">
        <button class="btn btn-primary d-grid w-100" type="submit" id="submitBtn">
          <span class="indicator-label">Kirim Kode OTP</span>
          <span class="indicator-progress d-none">
              <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              Mengirim...
          </span>
        </button>
      </div>
    </form>

    <div class="text-center">
      <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
        <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl me-1_5"></i>
        Kembali ke Login
      </a>
    </div>

  </div>
</div>
<!-- /Forgot Password -->
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formAuthentication');
        const submitBtn = document.getElementById('submitBtn');

        if(form && submitBtn) {
            form.addEventListener('submit', function() {
                if(form.checkValidity()) {
                    submitBtn.disabled = true;
                    submitBtn.querySelector('.indicator-label').classList.add('d-none');
                    submitBtn.querySelector('.indicator-progress').classList.remove('d-none');
                }
            });
        }
    });
</script>
@endsection
