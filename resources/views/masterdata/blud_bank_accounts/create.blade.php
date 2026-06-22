@extends('layouts.contentNavbarLayout')

@section('title', 'Tambah Rekening BLUD Baru')



@section('content')
    {{-- Page Title & Breadcrumb --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Tambah Rekening BLUD Baru</h4>
        <div class="d-flex align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.blud-bank-accounts.index') }}">Rekening BLUD</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Menampilkan Pesan Error Validasi --}}
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
            <form action="{{ route('admin.blud-bank-accounts.store') }}" method="POST">
                @csrf
                <div class="row g-6">
                    {{-- Nama Bank --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="bank_name" name="bank_name"
                                placeholder="Contoh: Bank BRI" value="{{ old('bank_name') }}" required />
                            <label for="bank_name">Nama Bank</label>
                        </div>
                    </div>

                    {{-- Nomor Rekening --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="account_number" name="account_number"
                                placeholder="Masukkan nomor rekening" value="{{ old('account_number') }}" required />
                            <label for="account_number">Nomor Rekening</label>
                        </div>
                    </div>

                    {{-- Atas Nama --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="account_name" name="account_name"
                                placeholder="Masukkan nama pemilik rekening" value="{{ old('account_name') }}" required />
                            <label for="account_name">Atas Nama</label>
                        </div>
                    </div>

                    {{-- Tanggal Mulai Efektif --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control flatpickr-date" id="start_date" name="start_date"
                                value="{{ old('start_date', date('Y-m-d')) }}" required />
                            <label for="start_date">Tanggal Mulai Efektif</label>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-6 text-end">
                    <a href="{{ route('admin.blud-bank-accounts.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Rekening</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')

    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flatpickrDates = document.querySelectorAll('.flatpickr-date');
            if (flatpickrDates) {
                flatpickr('.flatpickr-date', {
                    dateFormat: "Y-m-d",
                    appendTo: document.body
                });
            }
        });
    </script>

@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<style>
    .flatpickr-calendar {
        z-index: 99999 !important;
    }
</style>

@endsection
