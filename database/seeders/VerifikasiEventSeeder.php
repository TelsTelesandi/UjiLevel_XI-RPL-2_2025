<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerifikasiEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('verifikasi_event')->insert([
            [
                'event_id' => 1,
                'admin_id' => 4,
                'tanggal_verifikasi' => '2025-05-02',
                'catatan_admin' => 'Event yang menarik',
                'status' => 'Closed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id' => 2,
                'admin_id' => 5,
                'tanggal_verifikasi' => '2025-05-03',
                'catatan_admin' => 'Event yang mengedukasi masyarakat pengunjung',
                'status' => 'Closed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id' => 3,
                'admin_id' => 5,
                'tanggal_verifikasi' => null,
                'catatan_admin' => null,
                'status' => 'Unclosed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id' => 4,
                'admin_id' => 4,
                'tanggal_verifikasi' => null,
                'catatan_admin' => null,
                'status' => 'unclosed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id' => 5,
                'admin_id' => 4,
                'tanggal_verifikasi' => null,
                'catatan_admin' => null,
                'status' => 'unclosed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 