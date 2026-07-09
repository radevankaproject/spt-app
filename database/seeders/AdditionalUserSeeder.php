<?php

namespace Database\Seeders;

use App\Models\Leader;
use App\Models\Treasurer;
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
            ['username' => 'rafit'],
            [
                'name' => 'Rafit Dwi Febri S.STP',
                'email' => 'rafit@spt-app.com',
                'password' => Hash::make('password'),
                'role' => 'leader',
                'employee_number' => '199602242018081002',
                'phone_number' => '081398823727',
            ]
        );

        if ($leaderUser->wasRecentlyCreated) {
            Leader::create([
                'user_id' => $leaderUser->id,
                'employee_number' => '199602242018081002',
                'phone_number' => '081398823727',
                'start_date' => now()->subYear(),
                'end_date' => now()->addYears(4),
            ]);
        }

        // 2. Membuat User untuk Bendahara (Treasurer)
        $treasurerUser = User::firstOrCreate(
            ['username' => 'adimas'],
            [
                'name' => 'Adimas Hidayat, STr.Tra',
                'email' => 'adimas@spt-app.com',
                'password' => Hash::make('password'),
                'role' => 'treasurer',
                'employee_number' => '199901012024010121',
                'phone_number' => '081266988014',
            ]
        );

        if ($treasurerUser->wasRecentlyCreated) {
            Treasurer::create([
                'user_id' => $treasurerUser->id,
                'employee_number' => '199901012024010121',
                'phone_number' => '081266988014',
                'start_date' => now()->subYear(),
                'end_date' => now()->addYears(4),
            ]);
        }

        // 3. Membuat User untuk Staff PKS
        User::firstOrCreate(
            ['username' => 'melati'],
            [
                'name' => 'Melati',
                'email' => 'melati@spt-app.com',
                'password' => Hash::make('password'),
                'role' => 'staff_pks',
                'employee_number' => '200005212025212044',
                'phone_number' => '082169849154',
            ]
        );

        // 4. Membuat User untuk Staff Keuangan
        User::firstOrCreate(
            ['username' => 'tari'],
            [
                'name' => 'Wan Yuliantari, ST',
                'email' => 'tari@spt-app.com',
                'password' => Hash::make('password'),
                'role' => 'staff_keu',
                'employee_number' => null,
                'phone_number' => '081365676299',
            ]
        );

        // 5. Membuat User untuk Staff KTA Jukir
        User::firstOrCreate(
            ['username' => 'ilham'],
            [
                'name' => 'Ilham',
                'email' => 'ilham@spt-app.com',
                'password' => Hash::make('password'),
                'role' => 'staff_kta_jukir',
                'employee_number' => '199308142025211071',
                'phone_number' => '085213369345',
            ]
        );
    }
}
