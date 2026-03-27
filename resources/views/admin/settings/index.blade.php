<x-app-layout>
    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Setting App</h2>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ $settings['app_name'] ?? '' }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Judul Login</label>
                    <input type="text" name="login_title" value="{{ $settings['login_title'] ?? '' }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">API Token Fonnte</label>
                    <input type="text" name="fonnte_token" value="{{ $settings['fonnte_token'] ?? '' }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Logo App / Favicon</label>
                    @if(isset($settings['app_logo']))
                        <img src="{{ asset($settings['app_logo']) }}" alt="Logo" class="h-16 mb-2">
                    @endif
                    <input type="file" name="app_logo" accept="image/*" class="w-full text-gray-700 border border-gray-300 rounded-lg bg-gray-50">
                    <small class="text-gray-500">Maks 2MB. Otomatis akan menimpa file favicon.ico.</small>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">About Me</label>
                    <textarea name="about_me" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm">{{ $settings['about_me'] ?? '' }}</textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Privacy Policy</label>
                    <textarea name="privacy_policy" rows="6" class="w-full border-gray-300 rounded-lg shadow-sm">{{ $settings['privacy_policy'] ?? '' }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
