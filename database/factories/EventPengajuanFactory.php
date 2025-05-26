<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventPengajuan>
 */
class EventPengajuanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $judul_event_list = [
            'Seminar Teknologi Informasi',
            'Workshop Pengembangan Web',
            'Kompetisi Robotik Nasional',
            'Pelatihan Bahasa Pemrograman',
            'Webinar Kewirausahaan Digital'
        ];

        $deskripsi_list = [
            'Acara ini bertujuan untuk meningkatkan kemampuan teknologi informasi.',
            'Workshop yang membahas pengembangan web modern dengan Laravel.',
            'Kompetisi robotik untuk pelajar dan mahasiswa tingkat nasional.',
            'Pelatihan intensif bahasa pemrograman Python dan Java.',
            'Webinar tentang peluang bisnis di era digital saat ini.'
        ];

        return [
            'user_id' => User::inRandomOrder()->first()->user_id,
            'judul_event' => fake()->randomElement($judul_event_list),
            'deskripsi' => fake()->randomElement($deskripsi_list),
            'jenis_kegiatan' => fake()->randomElement(['seminar', 'workshop', 'kompetisi']),
            'total_pembiayaan' => fake()->numberBetween(100000, 1000000),
            'proposal' => fake()->word() . '.pdf',
            'tanggal_pengajuan' => fake()->dateTimeBetween('-1 month', 'now'),
            'status' => fake()->randomElement(['menunggu', 'disetujui', 'ditolak']),
        ];
    }
}
