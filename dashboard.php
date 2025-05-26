<?php
// filepath: c:\laragon\www\uji_level2\dashboard.php
session_start();
include 'koneksi.php';

// Cek login user
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

// Ambil user_id dari tabel users
$username = $_SESSION['username'];
$user = $koneksi->query("SELECT user_id FROM users WHERE username='$username'")->fetch_assoc();
$user_id = $user['user_id'];

// Handle pengajuan event baru
if (isset($_POST['ajukan'])) {
    $judul_event        = $koneksi->real_escape_string($_POST['judul_event']);
    $jenis_kegiatan     = $koneksi->real_escape_string($_POST['jenis_kegiatan']);
    $Total_pembiayaan   = $koneksi->real_escape_string($_POST['Total_pembiayaan']);
    $deskripsi          = $koneksi->real_escape_string($_POST['deskripsi']);
    $tanggal_pengajuan  = $koneksi->real_escape_string($_POST['tanggal_pengajuan']);

    // Handle upload file Proposal
    $Proposal = '';
    $upload_error = '';
    if (isset($_FILES['Proposal']) && $_FILES['Proposal']['error'] == 0) {
        $file = $_FILES['Proposal'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($ext != 'pdf') {
            $upload_error = "Proposal hanya boleh PDF!";
        } elseif ($file['size'] > $maxSize) {
            $upload_error = "Ukuran file maksimal 2MB!";
        } else {
            $namafile = 'proposal_' . time() . '_' . rand(100,999) . '.pdf';
            $tujuan = __DIR__ . '/uploads/' . $namafile;
            if (move_uploaded_file($file['tmp_name'], $tujuan)) {
                $Proposal = 'uploads/' . $namafile;
            } else {
                $upload_error = "Gagal upload file proposal!";
            }
        }
    }

    // Jika tidak upload file, bisa isi link manual
    if ($Proposal == '' && !empty($_POST['Proposal_link'])) {
        $Proposal = $koneksi->real_escape_string($_POST['Proposal_link']);
    }

    // Insert ke database jika tidak ada error upload
    if ($upload_error) {
        echo "<div style='color:red; text-align:center;'>$upload_error</div>";
    } else {
        $query = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, Total_pembiayaan, Proposal, deskripsi, tanggal_pengajuan, status) 
            VALUES ('$user_id', '$judul_event', '$jenis_kegiatan', '$Total_pembiayaan', '$Proposal', '$deskripsi', '$tanggal_pengajuan', 'open')";
        if (!$koneksi->query($query)) {
            echo "<div style='color:red; text-align:center;'>Gagal mengajukan event: " . $koneksi->error . "</div>";
        }
    }
}

// Handle close event
if (isset($_GET['close']) && is_numeric($_GET['close'])) {
    $event_id = intval($_GET['close']);
    // Pastikan hanya user yang punya event yang bisa close
    $cek = $koneksi->query("SELECT * FROM event_pengajuan WHERE event_id=$event_id AND user_id=$user_id");
    if ($cek && $cek->num_rows > 0) {
        $koneksi->query("UPDATE event_pengajuan SET status='closed' WHERE event_id=$event_id");
    }
}

// Ringkasan data
$total_pengajuan = $koneksi->query("SELECT COUNT(*) FROM event_pengajuan WHERE user_id=$user_id")->fetch_row()[0];
$total_closed = $koneksi->query("SELECT COUNT(*) FROM event_pengajuan WHERE user_id=$user_id AND status='closed'")->fetch_row()[0];

// Data pengajuan user
$pengajuan = $koneksi->query("SELECT * FROM event_pengajuan WHERE user_id=$user_id ORDER BY event_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard User</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 1100px;
            margin: 48px auto;
            background: rgba(255,255,255,0.98);
            border-radius: 22px;
            box-shadow: 0 12px 32px rgba(52, 73, 94, 0.13);
            padding: 40px 32px 32px 32px;
            position: relative;
        }
        .logout {
            position: absolute;
            right: 32px;
            top: 32px;
            color: #e17055;
            text-decoration: none;
            font-weight: 700;
            letter-spacing: 1px;
            transition: color 0.2s;
        }
        .logout:hover {
            color: #d35400;
        }
        h2 {
            text-align: center;
            color: #e17055;
            font-weight: 800;
            margin-bottom: 32px;
            letter-spacing: 2px;
            font-size: 2.2em;
            text-shadow: 0 2px 8px #f6d36544;
        }
        .rekap {
            display: flex;
            gap: 32px;
            margin-bottom: 32px;
            justify-content: center;
        }
        .rekap-box {
            flex: 1;
            background: linear-gradient(120deg, #fbc2eb 0%, #a6c1ee 100%);
            border-radius: 16px;
            padding: 32px 0 18px 0;
            text-align: center;
            box-shadow: 0 4px 16px rgba(52, 73, 94, 0.09);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 2px solid #f6d36533;
        }
        .rekap-box:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 8px 24px rgba(52, 73, 94, 0.13);
        }
        .rekap-box h3 {
            margin: 0 0 10px 0;
            color: #e17055;
            font-size: 1.2em;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .rekap-box div {
            font-size: 2.7em;
            color: #6c3483;
            font-weight: 800;
            letter-spacing: 2px;
        }
        .form-ajukan {
            margin-bottom: 40px;
            background: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(52, 73, 94, 0.04);
            padding: 32px 24px 24px 24px;
            border: 2px solid #fbc2eb55;
        }
        .form-ajukan h3 {
            color: #e17055;
            margin-bottom: 18px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .form-ajukan input, .form-ajukan textarea {
            width: 100%;
            padding: 13px;
            margin-bottom: 16px;
            border: 1.5px solid #e17055;
            border-radius: 8px;
            font-size: 1em;
            background: #fff8f0;
            transition: border 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px #f6d36522;
        }
        .form-ajukan input:focus, .form-ajukan textarea:focus {
            border: 1.5px solid #6c3483;
            outline: none;
            box-shadow: 0 2px 8px #fbc2eb44;
        }
        .form-ajukan button {
            background: linear-gradient(90deg, #e17055 60%, #f6d365 100%);
            color: #fff;
            border: none;
            padding: 13px 0;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px #f6d36533;
            letter-spacing: 1px;
        }
        .form-ajukan button:hover {
            background: linear-gradient(90deg, #f6d365 0%, #e17055 100%);
            box-shadow: 0 4px 16px #fbc2eb44;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px #f6d36533;
        }
        th, td {
            border: none;
            padding: 15px 10px;
            text-align: center;
            font-size: 1em;
        }
        th {
            background: linear-gradient(90deg, #e17055 60%, #f6d365 100%);
            color: #fff;
            font-weight: 700;
            letter-spacing: 1px;
        }
        tr:nth-child(even) {
            background: #f8fafd;
        }
        tr:hover {
            background: #fbc2eb33;
        }
        .btn-close {
            background: linear-gradient(90deg, #c0392b 60%, #e17055 100%);
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1em;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px #f6d36533;
        }
        .btn-close:disabled {
            background: #bbb;
            cursor: not-allowed;
        }
        @media (max-width: 900px) {
            .container { padding: 18px 4vw; }
            .rekap { flex-direction: column; gap: 18px; }
        }
        @media (max-width: 600px) {
            .container { padding: 8px 1vw; }
            th, td { font-size: 0.95em; padding: 8px 4px; }
            .form-ajukan { padding: 12px 6px; }
        }
    </style>
</head>
<body>
<div class="container">
    <a class="logout" href="logout.php">Logout</a>
    <h2>Dashboard Pengajuan Event</h2>
    <div class="rekap">
        <div class="rekap-box">
            <h3>Total Pengajuan</h3>
            <div style="font-size:2em;"><?= $total_pengajuan ?></div>
        </div>
        <div class="rekap-box">
            <h3>Event Closed</h3>
            <div style="font-size:2em;"><?= $total_closed ?></div>
        </div>
    </div>
    <div class="form-ajukan">
        <h3>Ajukan Event Baru</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="judul_event" placeholder="Judul Event" required>
            <input type="text" name="jenis_kegiatan" placeholder="Jenis Kegiatan" required>
            <input type="text" name="Total_pembiayaan" placeholder="Total Pembiayaan" required>
            <input type="file" name="Proposal" accept="application/pdf">
            <input type="text" name="Proposal_link" placeholder="Atau isi link proposal (opsional)">
            <textarea name="deskripsi" placeholder="Deskripsi Event" required></textarea>
            <input type="date" name="tanggal_pengajuan" required>
            <button type="submit" name="ajukan">Ajukan Event</button>
        </form>
    </div>
    <h3 style="color:#2980b9; margin-bottom:12px;">Daftar Pengajuan Event Anda</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Judul Event</th>
            <th>Jenis Kegiatan</th>
            <th>Total Pembiayaan</th>
            <th>Proposal</th>
            <th>Deskripsi</th>
            <th>Tanggal Pengajuan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        while ($row = $pengajuan->fetch_assoc()) {
            echo "<tr>
                <td>{$no}</td>
                <td>".htmlspecialchars($row['judul_event'])."</td>
                <td>".htmlspecialchars($row['jenis_kegiatan'])."</td>
                <td>".htmlspecialchars($row['Total_pembiayaan'])."</td>
                <td>";
            if (strpos($row['Proposal'], 'uploads/') === 0) {
                echo "<a href='{$row['Proposal']}' target='_blank'>Download</a>";
            } else {
                echo htmlspecialchars($row['Proposal']);
            }
            echo "</td>
                <td>".htmlspecialchars($row['deskripsi'])."</td>
                <td>".htmlspecialchars($row['tanggal_pengajuan'])."</td>
                <td>".htmlspecialchars(ucfirst($row['status']))."</td>
                <td>";
            if ($row['status'] == 'open') {
                echo "<a href='dashboard.php?close={$row['event_id']}' class='btn-close' onclick=\"return confirm('Tutup event ini?')\">Close</a>";
            } else {
                echo "<button class='btn-close' disabled>Closed</button>";
            }
            echo "</td></tr>";
            $no++;
        }
        ?>
    </table>
</div>
</body>
</html>