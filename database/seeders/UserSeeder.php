<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'hendrik123',
                'password' => Hash::make('112233'),
                'role' => 'user',
                'nama_lengkap' => 'Hendrikus Jonathan Kurnianto',
                'ekskul' => 'Telskustik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kadavi158',
                'password' => Hash::make('12345'),
                'role' => 'user',
                'nama_lengkap' => 'Kadavai Raditya AlVino',
                'ekskul' => 'Syntax',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Rafli213',
                'password' => Hash::make('125678'),
                'role' => 'user',
                'nama_lengkap' => 'Rafli Adi Wijaya',
                'ekskul' => 'Futsal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Glenn258',
                'password' => Hash::make('glenn2506'),
                'role' => 'admin',
                'nama_lengkap' => 'Glenn Timothy Prasadi',
                'ekskul' => '-',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Fairuz345',
                'password' => Hash::make('fairuz508'),
                'role' => 'admin',
                'nama_lengkap' => 'Fairuz Ziyaad Purnomo',
                'ekskul' => '-',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 