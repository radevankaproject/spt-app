<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Buat role 'admin' jika belum ada
        // $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Buat user admin
        // 'firstOrCreate' akan mencari user dengan email tersebut,
        // jika tidak ada, maka akan membuatnya. Ini mencegah duplikasi.
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('@m3ck1nd4h'), // Ganti dengan password yang aman
                'role' => 'admin', // Menetapkan peran langsung di kolom role
                // 'status' => 'active',
            ]
        );

        // Berikan role 'admin' ke user tersebut
        // $adminUser->assignRole($adminRole);
    }
}
