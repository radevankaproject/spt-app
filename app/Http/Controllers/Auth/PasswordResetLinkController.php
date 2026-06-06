<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'numeric', 'digits_between:10,14'],
        ]);

        $user = \App\Models\User::where('phone_number', $request->phone_number)
            ->orWhereHas('leader', function ($query) use ($request) {
                $query->where('phone_number', $request->phone_number);
            })
            ->orWhereHas('treasurer', function ($query) use ($request) {
                $query->where('phone_number', $request->phone_number);
            })
            ->orWhereHas('fieldCoordinator', function ($query) use ($request) {
                $query->where('phone_number', $request->phone_number);
            })
            ->first();

        if (!$user) {
            return back()->withInput($request->only('phone_number'))
                ->withErrors(['phone_number' => 'Nomor HP tidak terdaftar dalam sistem.']);
        }

        // Generate 6 digit OTP
        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // In a real app, send OTP via SMS/WhatsApp here. 
        // Menggunakan Fonnte API dengan Laravel Http Facade
        $uptProfile = \App\Models\UptProfile::first();
        $apiToken = $uptProfile ? $uptProfile->api_token_fonnte : null;

        if ($apiToken) {
            $message = "Halo {$user->name},\n\nIni adalah kode OTP Anda untuk mereset kata sandi: *{$otpCode}*.\n\nKode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapa pun.";

            try {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $apiToken,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $request->phone_number,
                    'message' => $message,
                    'countryCode' => '62', // Default Indonesia
                ]);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                \Illuminate\Support\Facades\Log::error('Fonnte API Connection Error: ' . $e->getMessage());
                return back()->withInput($request->only('phone_number'))
                    ->withErrors(['phone_number' => 'Gagal terhubung ke server WhatsApp. Silakan coba beberapa saat lagi.']);
            } catch (\Exception $e) {
                return back()->withInput($request->only('phone_number'))
                    ->withErrors(['phone_number' => 'Gagal terhubung ke server WhatsApp. Silakan coba beberapa saat lagi.']);
            }
        }
        
        \App\Models\OtpReset::updateOrCreate(
            ['phone_number' => $request->phone_number],
            [
                'otp_code' => $otpCode,
                'attempts' => 0,
                'expires_at' => now()->addMinutes(5)
            ]
        );

        // We will just flash it for testing, or assume user gets it.
        session(['reset_phone_number' => $request->phone_number]);
        
        // Uncomment next line to see OTP in UI during testing (or comment it if production ready)
        session()->flash('status', 'Kode OTP telah dikirimkan ke nomor WhatsApp Anda.');

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'otp_code' => 'required|digits:6',
        ]);

        $otpRecord = \App\Models\OtpReset::where('phone_number', $request->phone_number)->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp_code' => 'Permintaan OTP tidak ditemukan. Silakan kirim ulang.']);
        }

        if (now()->greaterThan($otpRecord->expires_at)) {
            $otpRecord->delete();
            return back()->withErrors(['otp_code' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.']);
        }

        if ($otpRecord->attempts >= 5) {
            $otpRecord->delete();
            return back()->withErrors(['otp_code' => 'Terlalu banyak percobaan yang salah. Silakan minta kode OTP baru.']);
        }

        if ($otpRecord->otp_code !== $request->otp_code) {
            $otpRecord->increment('attempts');
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid. Percobaan: ' . $otpRecord->attempts . '/5']);
        }

        // OTP Valid. Login user directly and redirect to their dashboard, or redirect to a form to reset password.
        // For security, usually we redirect to reset password form.
        // I will log them in directly and let them change password in profile, or show reset password form.
        // To be safe, generate a token and redirect to reset-password.
        
        $user = \App\Models\User::where('phone_number', $request->phone_number)
            ->orWhereHas('leader', function ($query) use ($request) {
                $query->where('phone_number', $request->phone_number);
            })
            ->orWhereHas('treasurer', function ($query) use ($request) {
                $query->where('phone_number', $request->phone_number);
            })
            ->orWhereHas('fieldCoordinator', function ($query) use ($request) {
                $query->where('phone_number', $request->phone_number);
            })
            ->first();
        if ($user) {
            $token = Password::createToken($user);
            $otpRecord->delete();
            return redirect()->route('password.reset', ['token' => $token, 'email' => $user->email])
                ->with('status', 'OTP berhasil diverifikasi. Silakan masukkan kata sandi baru Anda.');
        }

        return back()->withErrors(['phone_number' => 'Pengguna tidak ditemukan.']);
    }
}
