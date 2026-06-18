@extends('layouts.contentNavbarLayout')

@section('title', 'Pengaturan Profil')

@section('content')
    <div class="container-fluid">
        <h4 class="text-default-900 text-2xl font-bold mb-6">Pengaturan Profil</h4>

        {{-- Pesan Sukses --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Pesan Error Validasi --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('profile.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Kolom Kiri: Info Profil -->
                        <div class="md:col-span-1">
                            <h5 class="text-lg font-semibold">Detail Profil</h5>
                            <p class="text-sm text-gray-500">Perbarui foto dan detail pribadi Anda.</p>
                        </div>

                        <!-- Kolom Kanan: Form Input -->
                        <div class="md:col-span-2">
                            <!-- Foto Profil -->
                            <div class="mb-4">
                                <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">Foto
                                    Profil</label>
                                <div class="flex items-center gap-4">
                                    <img id="image-preview"
                                        src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/images/users/avatar-1.jpg') }}"
                                        alt="Foto Profil" class="h-20 w-20 rounded-full object-cover">
                                    <div>
                                        <input type="file" name="profile_image" id="profile_image"
                                            class="block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-violet-50 file:text-violet-700
                                            hover:file:bg-violet-100"
                                            onchange="previewImage(event)">
                                        <p class="text-xs text-gray-500 mt-1">JPG, GIF atau PNG. Ukuran maks 2MB.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Nama -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    required>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    required>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Kolom Kiri: Ganti Password -->
                        <div class="md:col-span-1">
                            <h5 class="text-lg font-semibold">Ganti Password</h5>
                            <p class="text-sm text-gray-500">Pastikan akun Anda menggunakan password yang panjang dan acak
                                agar tetap aman.</p>
                        </div>

                        <!-- Kolom Kanan: Form Password -->
                        <div class="md:col-span-2">
                            <!-- Password Baru -->
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                <input type="password" name="password" id="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    autocomplete="new-password">
                                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="mb-4">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('image-preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
