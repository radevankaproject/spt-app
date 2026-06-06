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
            ['username' => 'bangameck'],
            [
                'name' => 'Rahmad Riskiadi, ST',
                'email' => 'rahmad.looker@gmail.com',
                'password' => Hash::make('@m3ck1nd4h'), // Password sesuai permintaan
                'role' => 'admin',
                'employee_number' => '199503312025211089',
                'phone_number' => '082288445265',
                // 'status' => 'active',
            ]
        );

        // Berikan role 'admin' ke user tersebut
        // $adminUser->assignRole($adminRole);
    }
}
