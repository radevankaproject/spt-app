<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserShortcut;
use Illuminate\Support\Facades\Auth;

class UserShortcutController extends Controller
{
    // List available shortcuts per role
    public static function getAvailableShortcuts($role)
    {
        $shortcuts = [
            'admin' => [
                ['name' => 'Dashboard', 'url' => url('/'), 'icon' => 'tabler-device-desktop-analytics', 'desc' => 'Ringkasan Sistem'],
                ['name' => 'Manajemen PKS', 'url' => route('masterdata.agreements.index'), 'icon' => 'tabler-files', 'desc' => 'Kelola Perjanjian'],
                ['name' => 'Master User', 'url' => route('admin.users.index'), 'icon' => 'tabler-users', 'desc' => 'Kelola Pengguna'],
                ['name' => 'Laporan Keuangan', 'url' => route('masterdata.deposit-reports.index'), 'icon' => 'tabler-report-money', 'desc' => 'Rekap Setoran'],
                ['name' => 'Titik Parkir', 'url' => route('masterdata.parking-locations.index'), 'icon' => 'tabler-map-pin', 'desc' => 'Lokasi Parkir'],
                ['name' => 'Master Jukir', 'url' => route('admin.leaders.index'), 'icon' => 'tabler-user', 'desc' => 'Kelola Jukir'],
                ['name' => 'Master Bendahara', 'url' => route('admin.treasurers.index'), 'icon' => 'tabler-calculator', 'desc' => 'Kelola Bendahara'],
                ['name' => 'Validasi Setoran', 'url' => route('masterdata.deposit-transactions.index'), 'icon' => 'tabler-cash', 'desc' => 'Validasi Setoran'],
            ],
            'staff_pks' => [
                ['name' => 'Dashboard', 'url' => url('/'), 'icon' => 'tabler-device-desktop-analytics', 'desc' => 'Ringkasan PKS'],
                ['name' => 'Manajemen PKS', 'url' => route('masterdata.agreements.index'), 'icon' => 'tabler-files', 'desc' => 'Kelola Perjanjian'],
                ['name' => 'Titik Parkir', 'url' => route('masterdata.parking-locations.index'), 'icon' => 'tabler-map-pin', 'desc' => 'Lokasi Parkir'],
                ['name' => 'Master Jukir', 'url' => route('admin.leaders.index'), 'icon' => 'tabler-user', 'desc' => 'Kelola Jukir'],
            ],
            'staff_keu' => [
                ['name' => 'Dashboard', 'url' => url('/'), 'icon' => 'tabler-device-desktop-analytics', 'desc' => 'Ringkasan Keuangan'],
                ['name' => 'Validasi Setoran', 'url' => route('masterdata.deposit-transactions.index'), 'icon' => 'tabler-cash', 'desc' => 'Data Setoran'],
                ['name' => 'Laporan Keuangan', 'url' => route('masterdata.deposit-reports.index'), 'icon' => 'tabler-report-money', 'desc' => 'Rekap Keuangan'],
                ['name' => 'Data Jukir', 'url' => route('admin.leaders.index'), 'icon' => 'tabler-user', 'desc' => 'Koordinator/Jukir'],
            ],
            'leader' => [
                ['name' => 'Dashboard', 'url' => url('/'), 'icon' => 'tabler-device-desktop-analytics', 'desc' => 'Ringkasan Koor'],
                ['name' => 'Profil Saya', 'url' => route('profile.index'), 'icon' => 'tabler-user', 'desc' => 'Informasi Profil'],
                ['name' => 'Titik Parkir', 'url' => route('masterdata.parking-locations.index'), 'icon' => 'tabler-map-pin', 'desc' => 'Kelola Titik Parkir'],
            ],
            'treasurer' => [
                ['name' => 'Dashboard', 'url' => url('/'), 'icon' => 'tabler-device-desktop-analytics', 'desc' => 'Ringkasan Bendahara'],
                ['name' => 'Riwayat Setoran', 'url' => route('masterdata.deposit-transactions.index'), 'icon' => 'tabler-cash', 'desc' => 'Validasi Setoran'],
                ['name' => 'Profil Saya', 'url' => route('profile.index'), 'icon' => 'tabler-user', 'desc' => 'Pengaturan Profil'],
            ]
        ];

        return $shortcuts[$role] ?? [];
    }

    public static function getDefaultShortcuts($role)
    {
        $all = self::getAvailableShortcuts($role);
        return array_slice($all, 0, 4);
    }

    public function getAvailable()
    {
        $user = Auth::user();
        if (!$user) return response()->json([]);

        $available = self::getAvailableShortcuts($user->role);
        $saved = $user->shortcuts()->pluck('name')->toArray();

        // Mark which ones are currently selected
        foreach ($available as &$item) {
            $item['is_selected'] = in_array($item['name'], $saved);
        }

        return response()->json($available);
    }

    public function saveShortcuts(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $request->validate([
            'shortcuts' => 'required|array',
            'shortcuts.*.name' => 'required|string',
            'shortcuts.*.url' => 'required|string',
            'shortcuts.*.icon' => 'required|string',
            'shortcuts.*.desc' => 'nullable|string',
        ]);

        // Delete old shortcuts
        $user->shortcuts()->delete();

        // Save new
        $toInsert = [];
        foreach ($request->shortcuts as $s) {
            $toInsert[] = [
                'user_id' => $user->id,
                'name' => $s['name'],
                'url' => $s['url'],
                'icon' => $s['icon'],
                'description' => $s['desc'] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        UserShortcut::insert($toInsert);

        return response()->json(['success' => true]);
    }
}
