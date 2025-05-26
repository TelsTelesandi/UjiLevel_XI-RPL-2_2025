<?php

namespace Database\Factories;

use App\Models\EventPengajuan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VerifikasiEvent>
 */
class VerifikasiEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $catatan_admin_list = [
            'Perlu diperiksa kembali dokumennya.',
            'Dokumen sudah lengkap dan valid.',
            'Mohon lengkapi data yang kurang.',
            'Pengajuan disetujui tanpa revisi.',
            'Terdapat beberapa kesalahan pada formulir.',
        ];

        return [
            'admin_id' => User::inRandomOrder()->first()->user_id,
            'event_id' => EventPengajuan::inRandomOrder()->first()->event_id,
            'status' => fake()->randomElement(['unclosed', 'closed']),
            'catatan_admin' => fake()->randomElement($catatan_admin_list),
            'tanggal_verifikasi' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
