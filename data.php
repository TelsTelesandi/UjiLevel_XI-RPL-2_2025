<?php
// Data jenis kegiatan (opsi untuk dropdown)
$jenisKegiatanOptions = ['Seni', 'Olahraga', 'Pendidikan', 'Sosial'];

// File untuk menyimpan data pengajuan
$dataFile = 'pengajuan_data.txt';

// Fungsi untuk membaca data dari file
function loadPengajuanData() {
    global $dataFile;
    $pengajuanData = [];
    if (file_exists($dataFile)) {
        $lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $data = unserialize($line);
            if ($data) {
                $pengajuanData[] = $data;
            }
        }
    } else {
        // Data awal jika file belum ada
        $pengajuanData = [
            [
                'event_id' => 1,
                'user_id' => 1,
                'judul_event' => 'Lomba Seni Tari',
                'jenis_kegiatan' => 'Seni',
                'total_pembiayaan' => 1500000,
                'proposal' => 'proposal_tari.pdf',
                'deskripsi' => 'Lomba seni tari antar sekolah.',
                'tanggal_pengajuan' => '2025-05-01',
                'status' => 'disetujui',
                'catatan_admin' => 'Disetujui pada 2025-05-02',
                'tanggal_diproses' => '2025-05-02',
                'tanggal_closed' => ''
            ],
            [
                'event_id' => 2,
                'user_id' => 2,
                'judul_event' => 'Festival Paduan Suara',
                'jenis_kegiatan' => 'Seni',
                'total_pembiayaan' => 2000000,
                'proposal' => 'proposal_paduan.pdf',
                'deskripsi' => 'Festival paduan suara tahunan.',
                'tanggal_pengajuan' => '2025-05-03',
                'status' => 'disetujui',
                'catatan_admin' => '',
                'tanggal_diproses' => '2025-05-04',
                'tanggal_closed' => ''
            ],
            [
                'event_id' => 5,
                'user_id' => 2,
                'judul_event' => 'Pentas Seni Akhir Tahun',
                'jenis_kegiatan' => 'Seni',
                'total_pembiayaan' => 2500000,
                'proposal' => 'proposal_pentas.pdf',
                'deskripsi' => 'Pentas seni penutup tahun ajaran.',
                'tanggal_pengajuan' => '2025-05-10',
                'status' => 'disetujui',
                'catatan_admin' => '',
                'tanggal_diproses' => '2025-05-11',
                'tanggal_closed' => ''
            ]
        ];
    }
    return $pengajuanData;
}

// Fungsi untuk menyimpan data ke file
function savePengajuanData($data) {
    global $dataFile;
    $fileContent = '';
    foreach ($data as $item) {
        $fileContent .= serialize($item) . PHP_EOL;
    }
    file_put_contents($dataFile, $fileContent);
}

// Load data awal
$pengajuanData = loadPengajuanData();
?>