<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view or redirect if already authenticated.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'leader') {
                return redirect()->route('leader.dashboard');
            } elseif ($user->role === 'treasurer') {
                return redirect()->route('treasurer.dashboard');
            } elseif ($user->role === 'staff_keu') {
                return redirect()->route('staff_keu.dashboard');
            } elseif ($user->role === 'staff_pks') {
                return redirect()->route('staff_pks.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // === LOGIC REDIRECT BERDASARKAN ROLE ===
        $user = Auth::user();

        \App\Models\UserActivity::create([
            'user_id' => $user->id,
            'action' => 'Login',
            'description' => 'User berhasil login ke sistem.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        switch ($user->role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard', absolute: false));
            case 'leader':
                return redirect()->intended(route('leader.dashboard', absolute: false));
            case 'field_coordinator':
                return redirect()->intended(route('field_coordinator.dashboard', absolute: false));
            case 'staff_pks':
                return redirect()->intended(route('staff-pks.dashboard', absolute: false));
            case 'staff_keu':
                return redirect()->intended(route('staff-keuangan.dashboard', absolute: false));
            default:
                // Fallback jika role tidak terdefinisi
                return redirect()->intended(route('dashboard', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            \App\Models\UserActivity::create([
                'user_id' => Auth::id(),
                'action' => 'Logout',
                'description' => 'User keluar dari sistem.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
