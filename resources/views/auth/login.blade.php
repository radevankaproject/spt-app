@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.guest')

@section('title', 'Login - SiPKS Pekanbaru')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<!-- Login -->
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
    <h4 class="mb-1">Selamat Datang di SiPKS! 👋</h4>
    <p class="mb-6">Sistem Informasi Perjanjian Kerjasama Perparkiran Dinas Perhubungan Kota Pekanbaru</p>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form id="formAuthentication" class="mb-4" action="{{ route('login') }}" method="POST">
      @csrf
      <div class="mb-6 form-control-validation">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username"
          placeholder="Masukkan username Anda" value="{{ old('username') }}" required autofocus autocomplete="username" />
      </div>
      <div class="mb-6 form-password-toggle form-control-validation">
        <label class="form-label" for="password">Kata Sandi</label>
        <div class="input-group input-group-merge">
          <input type="password" id="password" class="form-control" name="password"
            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
            required autocomplete="current-password" aria-describedby="password" />
          <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
        </div>
      </div>
      <div class="my-8">
        <div class="d-flex justify-content-between">
          <div class="form-check mb-0 ms-2">
            <input class="form-check-input" type="checkbox" name="remember" id="remember-me" />
            <label class="form-check-label" for="remember-me"> Ingat Saya </label>
          </div>
          @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}">
            <p class="mb-0">Lupa Kata Sandi?</p>
          </a>
          @endif
        </div>
      </div>
      <div class="mb-6">
        <button class="btn btn-primary d-grid w-100" type="submit" id="submitBtn">
          <span class="indicator-label">Masuk</span>
          <span class="indicator-progress d-none">
              <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              Memuat...
          </span>
        </button>
      </div>
    </form>

  </div>
</div>
<!-- /Login -->
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
