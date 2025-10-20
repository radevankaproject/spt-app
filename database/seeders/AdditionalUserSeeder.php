<?php
namespace Database\Seeders;

use App\Models\Leader;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
// 👈 PENTING: Tambahkan ini

class AdditionalUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Membuat User untuk Pimpinan (Leader)
        $leaderUser = User::firstOrCreate(
            ['email' => 'pimpinan@spt-app.com'],
            [
                'name'     => 'Dr. Pimpinan, M.Si.',
                'username' => 'pimpinan',
                'password' => Hash::make('password'),
                'role'     => 'leader',
            ]
        );

        // Setelah user dibuat, buat juga entri di tabel 'leaders'
        // Ini akan mencegah error "Attempt to read property on null"
        if ($leaderUser->wasRecentlyCreated) {
            Leader::create([
                'user_id'         => $leaderUser->id,
                'employee_number' => '197001012000121001',
                'start_date'      => now()->subYear(), // Menjabat sejak setahun yang lalu
                'end_date'        => now()->addYears(4),
            ]);
        }

        // 2. Membuat User untuk Staff PKS
        User::firstOrCreate(
            ['email' => 'staffpks@spt-app.com'],
            [
                'name'     => 'Staff PKS',
                'username' => 'staffpks',
                'password' => Hash::make('password'),
                'role'     => 'staff_pks',
            ]
        );

        // 3. Membuat User untuk Staff Keuangan
        User::firstOrCreate(
            ['email' => 'staffkeu@spt-app.com'],
            [
                'name'     => 'Staff Keuangan',
                'username' => 'staffkeu',
                'password' => Hash::make('password'),
                'role'     => 'staff_keu',
            ]
        );
    }
}
