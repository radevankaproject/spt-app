@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Rekening BLUD')



@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Rekening BLUD</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.blud-bank-accounts.index') }}">Rekening BLUD</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-0"><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
            <ul class="mt-2 mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.blud-bank-accounts.update', $account->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row g-6">
                    {{-- Nama Bank --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="bank_name" name="bank_name"
                                placeholder="Contoh: Bank BRI" value="{{ old('bank_name', $account->bank_name) }}"
                                required />
                            <label for="bank_name">Nama Bank</label>
                        </div>
                    </div>

                    {{-- Nomor Rekening --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="account_number" name="account_number"
                                placeholder="Masukkan nomor rekening"
                                value="{{ old('account_number', $account->account_number) }}" required />
                            <label for="account_number">Nomor Rekening</label>
                        </div>
                    </div>

                    {{-- Atas Nama --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="account_name" name="account_name"
                                placeholder="Masukkan nama pemilik rekening"
                                value="{{ old('account_name', $account->account_name) }}" required />
                            <label for="account_name">Atas Nama</label>
                        </div>
                    </div>

                    {{-- Tanggal Mulai Efektif --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ old('start_date', $account->start_date->format('Y-m-d')) }}" required />
                            <label for="start_date">Tanggal Mulai Efektif</label>
                        </div>
                    </div>

                    {{-- Tanggal Berakhir --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ old('end_date', $account->end_date ? $account->end_date->format('Y-m-d') : '') }}" />
                            <label for="end_date">Tanggal Berakhir (Opsional)</label>
                        </div>
                    </div>

                    {{-- ✅ PERUBAHAN: Status menggunakan Switch --}}
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Jadikan Rekening Aktif?</label>
                        </div>
                        {{-- Input tersembunyi untuk memastikan nilai '0' terkirim jika switch tidak dicentang --}}
                        <input type="hidden" name="is_active" value="0" />
                    </div>

                </div>
                <div class="col-12 mt-4">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded-3"><i class="icon-base ti tabler-alert ti-md"></i></span>
                        <div class="alert-text">
                            Mengaktifkan rekening ini akan menonaktifkan rekening aktif lainnya secara otomatis.
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-6 text-end">
                    <a href="{{ route('admin.blud-bank-accounts.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // Script khusus untuk form ini (jika ada di masa depan)
        document.addEventListener("DOMContentLoaded", function() {
            const isActiveSwitch = document.querySelector('#is_active');
            const hiddenInput = document.querySelector('input[type="hidden"][name="is_active"]');

            isActiveSwitch.addEventListener('change', function() {
                // Trik untuk mengirim nilai 0 jika checkbox tidak dicentang
                hiddenInput.disabled = this.checked;
            });

            // Panggil saat load untuk set kondisi awal
            hiddenInput.disabled = isActiveSwitch.checked;
        });
    </script>
@endsection
