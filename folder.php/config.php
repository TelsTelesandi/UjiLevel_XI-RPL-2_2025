<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_ujilevel_nyimas');

// Konfigurasi Aplikasi
define('APP_NAME', 'Sistem Pengajuan Event Ekstrakurikuler');
define('APP_VERSION', '1.0.0');

// Konfigurasi Upload File
define('UPLOAD_PATH', '../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// Status Pengajuan Event
define('STATUS_PENDING', 'Pending');
define('STATUS_DISETUJUI', 'Disetujui');
define('STATUS_DITOLAK', 'Ditolak');
define('STATUS_REVISI', 'Perlu Revisi');

// Daftar Ekstrakurikuler
$ESKUL_LIST = [
    'Pramuka' => [
        'kode' => 'PMK',
        'pembina' => 'Pembina Pramuka',
        'max_peserta' => 100
    ],
    'PMR' => [
        'kode' => 'PMR',
        'pembina' => 'Pembina PMR',
        'max_peserta' => 50
    ],
    'Paskibra' => [
        'kode' => 'PSK',
        'pembina' => 'Pembina Paskibra',
        'max_peserta' => 40
    ],
    'Basket' => [
        'kode' => 'BSK',
        'pembina' => 'Pembina Basket',
        'max_peserta' => 30
    ],
    'Futsal' => [
        'kode' => 'FTS',
        'pembina' => 'Pembina Futsal',
        'max_peserta' => 30
    ]
];

// Jenis Event yang Diizinkan
$JENIS_EVENT = [
    'Lomba' => [
        'kode' => 'LMB',
        'durasi_max' => 3, // dalam hari
        'persyaratan' => ['proposal', 'surat_izin', 'anggaran']
    ],
    'Latihan Rutin' => [
        'kode' => 'LTH',
        'durasi_max' => 1, // dalam hari
        'persyaratan' => ['jadwal', 'daftar_peserta']
    ],
    'Workshop' => [
        'kode' => 'WKS',
        'durasi_max' => 2, // dalam hari
        'persyaratan' => ['proposal', 'materi', 'anggaran']
    ],
    'Pertandingan' => [
        'kode' => 'PTD',
        'durasi_max' => 1, // dalam hari
        'persyaratan' => ['surat_izin', 'daftar_pemain', 'jadwal']
    ],
    'Pentas Seni' => [
        'kode' => 'PTS',
        'durasi_max' => 1, // dalam hari
        'persyaratan' => ['proposal', 'rundown', 'anggaran']
    ]
];

// Fungsi Helper untuk Validasi
function isValidEventType($type) {
    global $JENIS_EVENT;
    return isset($JENIS_EVENT[$type]);
}

function isValidEskul($eskul) {
    global $ESKUL_LIST;
    return isset($ESKUL_LIST[$eskul]);
}

function getEventRequirements($type) {
    global $JENIS_EVENT;
    return isset($JENIS_EVENT[$type]) ? $JENIS_EVENT[$type]['persyaratan'] : [];
}

function generateEventCode($eskul, $jenis_event) {
    global $ESKUL_LIST, $JENIS_EVENT;
    $tahun = date('Y');
    $bulan = date('m');
    $random = rand(100, 999);
    
    $eskul_kode = isset($ESKUL_LIST[$eskul]) ? $ESKUL_LIST[$eskul]['kode'] : 'XXX';
    $event_kode = isset($JENIS_EVENT[$jenis_event]) ? $JENIS_EVENT[$jenis_event]['kode'] : 'XXX';
    
    return $eskul_kode . '-' . $event_kode . '-' . $tahun . $bulan . '-' . $random;
}

// Koneksi Database
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
