<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'siswa1',
                'password' => Hash::make('siswa123'),
                'role' => 'user',
                'nama_lengkap' => 'Budi Santoso',
                'ekskul' => 'Basket'
            ],
            [
                'username' => 'siswa2',
                'password' => Hash::make('siswa123'),
                'role' => 'user',
                'nama_lengkap' => 'Siti Rahayu',
                'ekskul' => 'Pramuka'
            ],
            [
                'username' => 'siswa3',
                'password' => Hash::make('siswa123'),
                'role' => 'user',
                'nama_lengkap' => 'Ahmad Rizki',
                'ekskul' => 'Futsal'
            ],
            [
                'username' => 'siswa4',
                'password' => Hash::make('siswa123'),
                'role' => 'user',
                'nama_lengkap' => 'Dewi Lestari',
                'ekskul' => 'Paduan Suara'
            ],
            [
                'username' => 'siswa5',
                'password' => Hash::make('siswa123'),
                'role' => 'user',
                'nama_lengkap' => 'Rudi Hermawan',
                'ekskul' => 'Robotik'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
} 