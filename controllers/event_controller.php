<?php
session_start();
include '../config/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['ajukan_event'])) {
    $user_id           = $_SESSION['user_id'];
    $judul_event       = mysqli_real_escape_string($conn, $_POST['judul_event']);
    $jenis_kegiatan    = mysqli_real_escape_string($conn, $_POST['jenis_kegiatan']);
    $total_pembiayaan  = mysqli_real_escape_string($conn, $_POST['total_pembiayaan']);
    $tanggal_pengajuan = mysqli_real_escape_string($conn, $_POST['tanggal_pengajuan']);
    $deskripsi         = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    // Handle upload proposal
    $proposal_name = $_FILES['proposal']['name'];
    $proposal_tmp  = $_FILES['proposal']['tmp_name'];
    $proposal_ext  = strtolower(pathinfo($proposal_name, PATHINFO_EXTENSION));
    $proposal_new  = uniqid('proposal_') . '.' . $proposal_ext;
    $upload_dir    = '../uploads/';

    // Validasi file
    if ($proposal_ext != 'pdf') {
        echo "<script>alert('File harus PDF!');window.location='../views/form_pengajuan.php';</script>";
        exit;
    }
    if ($_FILES['proposal']['size'] > 5 * 1024 * 1024) {
        echo "<script>alert('Ukuran file maksimal 5MB!');window.location='../views/form_pengajuan.php';</script>";
        exit;
    }
    if (!move_uploaded_file($proposal_tmp, $upload_dir . $proposal_new)) {
        echo "<script>alert('Gagal upload file!');window.location='../views/form_pengajuan.php';</script>";
        exit;
    }

    // Simpan ke database
    $query = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiayaan, proposal, deskripsi, tanggal_pengajuan, status)
              VALUES ('$user_id', '$judul_event', '$jenis_kegiatan', '$total_pembiayaan', '$proposal_new', '$deskripsi', '$tanggal_pengajuan', 'menunggu')";
    if (mysqli_query($conn, $query)) {
        $event_id = mysqli_insert_id($conn); // Dapatkan id event terakhir
        mysqli_query($conn, "INSERT INTO verifikasi_event (event_id, status, tanggal_verifikasi) VALUES ($event_id, 'unclosed', NOW())");
        header("Location: ../views/form_pengajuan.php?success=1");
        exit;
    } else {
        header("Location: ../views/form_pengajuan.php?error=1");
        exit;
    }
}

// Rekap status event_pengajuan
$rekap = [
    'menunggu' => 0,
    'disetujui' => 0,
    'ditolak' => 0,
];

// Rekap menunggu, disetujui, ditolak
$q = mysqli_query($conn, "SELECT status, COUNT(*) as jml FROM event_pengajuan WHERE user_id='$user_id' GROUP BY status");
while ($row = mysqli_fetch_assoc($q)) {
    $status = strtolower($row['status']);
    if (isset($rekap[$status])) $rekap[$status] = $row['jml'];
}

?>