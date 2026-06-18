@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.guest')

@section('title', 'Buat Kata Sandi Baru - SiPKS Pekanbaru')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<!-- Reset Password -->
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
    <h4 class="mb-1">Buat Kata Sandi Baru 🔐</h4>
    <p class="mb-6">Silakan masukkan kata sandi baru untuk akun Anda.</p>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="formAuthentication" class="mb-4" action="{{ route('password.store') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ $request->route('token') }}">
      <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

      <div class="mb-6 form-password-toggle form-control-validation">
        <label class="form-label" for="password">Kata Sandi Baru</label>
        <div class="input-group input-group-merge">
          <input type="password" id="password" class="form-control" name="password"
            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
            required autofocus aria-describedby="password" />
          <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
        </div>
      </div>
      
      <div class="mb-6 form-password-toggle form-control-validation">
        <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
        <div class="input-group input-group-merge">
          <input type="password" id="password_confirmation" class="form-control" name="password_confirmation"
            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
            required aria-describedby="password_confirmation" />
          <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
        </div>
      </div>

      <div class="mb-6">
        <button class="btn btn-primary d-grid w-100" type="submit">Reset Kata Sandi</button>
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
<!-- /Reset Password -->
@endsection
