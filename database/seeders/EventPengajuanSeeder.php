<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventPengajuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('event_pengajuan')->insert([
            [
                'user_id' => 1,
                'judul_event' => 'Live Music Corner',
                'jenis_kegiatan' => 'Penampilan Musik',
                'total_pembiayaan' => '500000',
                'proposal' => 'Proposal_Live_Music_Corner.pdf',
                'deskripsi' => 'Live Music Corner adalah kegiatan pertunjukan musik langsung oleh siswa sebagai ajang ekspresi dan unjuk bakat dalam bidang seni musik. Kegiatan ini bertujuan menumbuhkan kreativitas, percaya diri, serta menciptakan suasana sekolah yang positif dan inspiratif. Acara diisi dengan penampilan solo atau grup dari anggota ekstrakurikuler musik.',
                'tanggal_pengajuan' => '2025-05-01',
                'status' => 'Disetujui',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'judul_event' => 'Pameran Edukasi',
                'jenis_kegiatan' => 'Pameran',
                'total_pembiayaan' => '1500000',
                'proposal' => 'Proposal_Pameran_Edukasi.pdf',
                'deskripsi' => 'Pameran Edukasi',
                'tanggal_pengajuan' => '2025-05-02',
                'status' => 'Ditolak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'judul_event' => 'Turnamen Futsal antar sekolah (SMP)',
                'jenis_kegiatan' => 'Turnamen',
                'total_pembiayaan' => '2500000',
                'proposal' => 'Proposal_Turnamen_Antar_Sekolah.pdf',
                'deskripsi' => 'Turnamen Futsal Antar Sekolah adalah ajang kompetisi olahraga yang mempertemukan tim-tim futsal dari berbagai sekolah. Kegiatan ini bertujuan untuk meningkatkan sportivitas, mempererat hubungan antarsekolah, serta mendorong siswa untuk aktif dalam kegiatan positif dan kompetitif.',
                'tanggal_pengajuan' => '2025-05-03',
                'status' => 'Menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1, // Diubah dari 6 ke 1 karena user_id 6 tidak ada
                'judul_event' => 'Lomba/Kompetisi Teknologi dan Multimedia',
                'jenis_kegiatan' => 'Kompetisi',
                'total_pembiayaan' => '3000000',
                'proposal' => 'Proposal_Tomcat.pdf',
                'deskripsi' => 'TOMCAT adalah kegiatan tahunan berbentuk turnamen teknologi dan multimedia yang diselenggarakan oleh ekstrakurikuler IT sekolah. Kegiatan ini mencakup berbagai lomba seperti desain grafis, editing video, cerdas cermat IT, dan kompetisi coding antar pelajar. Tujuannya adalah untuk menggali potensi siswa dalam bidang teknologi, meningkatkan daya saing, serta memperluas jaringan antar pelajar di bidang TIK.',
                'tanggal_pengajuan' => '2025-05-04',
                'status' => 'Disetujui',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2, // Diubah dari 7 ke 2 karena user_id 7 tidak ada
                'judul_event' => 'Turnamen Basket antar Sekolah',
                'jenis_kegiatan' => 'Turnamen',
                'total_pembiayaan' => '4000000',
                'proposal' => 'Proposal_Turnamen_Basket.pdf',
                'deskripsi' => 'Turnamen Basket Antar Sekolah merupakan kegiatan kompetisi olahraga yang melibatkan tim-tim basket dari berbagai sekolah. Kegiatan ini bertujuan untuk meningkatkan semangat sportivitas, melatih kerja sama tim, serta mempererat hubungan antarsekolah melalui pertandingan yang sehat dan kompetitif.',
                'tanggal_pengajuan' => '2025-05-05',
                'status' => 'Disetujui',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 