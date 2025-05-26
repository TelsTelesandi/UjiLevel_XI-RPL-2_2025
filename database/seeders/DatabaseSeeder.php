<?php

namespace Database\Seeders;

use App\Models\EventPengajuan;
use App\Models\User;
use App\Models\VerifikasiEvent;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        EventPengajuan::factory(10)->create();
        VerifikasiEvent::factory(10)->create();
    }
}
