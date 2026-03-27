@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="card shadow-lg border-0">
        <div class="card-body p-sm-7 p-5">

            {{-- ✅ Branding & Logo Aplikasi --}}
            <div class="app-brand justify-content-center mb-5">
                <a href="{{ url('/') }}" class="app-brand-link gap-3">
                    <span class="app-brand-logo demo">
                        <img src="{{ asset('logo.png') }}" alt="Logo SiPKS" style="height: 50px;">
                    </span>
                </a>
            </div>
            <div class="text-center mb-5">
                 <h5 class="mb-2">Sistem Informasi Perjanjian Kerjasama Perparkiran Pekanbaru</h5>
                 <p class="mb-0">Silakan masuk untuk melanjutkan.</p>
            </div>


            {{-- Menampilkan status (misal: setelah reset password) --}}

            <form id="formAuthentication" action="{{ route('login') }}" method="POST">
                @csrf
                {{-- Input Username --}}
                <div class="form-floating form-floating-outline mb-4">
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username"
                        placeholder="Masukkan username Anda" value="{{ old('username') }}" required autofocus />
                    <label for="username">Username</label>
                </div>

                {{-- Input Password --}}
                <div class="form-password-toggle mb-4">
                    <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline">
                            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                required autocomplete="current-password" />
                            <label for="password">Password</label>
                        </div>
                        <span class="input-group-text cursor-pointer"><i class="icon-base ri ri-eye-off-line"></i></span>
                    </div>
                </div>

                {{-- Opsi Ingat Saya & Lupa Password --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember-me" />
                        <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                    </div>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-primary">
                        <span>Lupa Password?</span>
                    </a>
                    @endif
                </div>

                {{-- Tombol Login --}}
                <div class="mt-5">
                    <button class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
                </div>
            </form>
        </div>
    </div>
@endsection
