<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'nama_lengkap' => 'Administrator Utama',
                'ekskul' => null
            ],
            [
                'username' => 'kesiswaan',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'nama_lengkap' => 'Admin Kesiswaan',
                'ekskul' => null
            ],
            [
                'username' => 'pembina',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'nama_lengkap' => 'Pembina Ekstrakurikuler',
                'ekskul' => null
            ]
        ];

        foreach ($admins as $admin) {
            User::create($admin);
        }
    }
} 