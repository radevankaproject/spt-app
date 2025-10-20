<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan membuat request ini.
     */

    protected $errorBag = 'updatePassword';
    public function authorize(): bool
    {
        // Izinkan semua user yang sudah login untuk mencoba mengubah password mereka
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if (! \Hash::check($value, auth()->user()->password)) {
                        $fail('Kata sandi saat ini yang Anda masukkan salah.');
                    }
                },
            ],
            'password'         => [
                'required',
                'string',
                'confirmed',
                                 // ✅ Aturan diperketat di sini!
                Password::min(8) // Minimal 8 karakter
                    ->letters()      // Wajib ada huruf
                    ->mixedCase()    // Wajib ada huruf besar & kecil
                    ->numbers()      // Wajib ada angka
                    ->symbols(),     // Wajib ada simbol
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min'              => 'Kata sandi baru minimal harus 8 karakter.',
            'password.letters'          => 'Kata sandi baru wajib mengandung huruf.',
            'password.mixed'            => 'Kata sandi baru wajib mengandung huruf besar dan kecil.',
            'password.numbers'          => 'Kata sandi baru wajib mengandung angka.',
            'password.symbols'          => 'Kata sandi baru wajib mengandung simbol (contoh: !@#$%).',
        ];
    }
}
