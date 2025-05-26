<?php
// filepath: c:\laragon\www\uji_level2\dasboardadmin.php
session_start();
include 'koneksi.php';

// Cek login admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// CRUD USER
// Tambah user
if (isset($_POST['tambah_user'])) {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $koneksi->real_escape_string($_POST['role']);
    $nama_lengkap = $koneksi->real_escape_string($_POST['nama_lengkap']);
    $eskul = $koneksi->real_escape_string($_POST['eskul']  );
    $cek = $koneksi->query("SELECT * FROM users WHERE username='$username'");
    if ($cek->num_rows == 0) {
        $koneksi->query("INSERT INTO users (username, password, role, nama_lengkap, Eskul) VALUES ('$username', '$password', '$role', '$nama_lengkap', '$eskul')");
    }
}
// Hapus user
if (isset($_GET['hapus_user'])) {
    $id = intval($_GET['hapus_user']);
    $koneksi->query("DELETE FROM users WHERE user_id=$id");
}

// Approval kegiatan
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    // Hanya approve jika status masih 'open'
    $cek = $koneksi->query("SELECT status FROM event_pengajuan WHERE event_id=$id");
    if ($cek && $cek->num_rows > 0) {
        $row = $cek->fetch_assoc();
        if ($row['status'] == 'open') {
            $koneksi->query("UPDATE event_pengajuan SET status='approved' WHERE event_id=$id");
        }
    }
}
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    // Hanya reject jika status masih 'open'
    $cek = $koneksi->query("SELECT status FROM event_pengajuan WHERE event_id=$id");
    if ($cek && $cek->num_rows > 0) {
        $row = $cek->fetch_assoc();
        if ($row['status'] == 'open') {
            $koneksi->query("UPDATE event_pengajuan SET status='rejected' WHERE event_id=$id");
        }
    }
}

// Ringkasan data
$total_pengajuan = $koneksi->query("SELECT COUNT(*) FROM event_pengajuan")->fetch_row()[0];
$total_closed = $koneksi->query("SELECT COUNT(*) FROM event_pengajuan WHERE status='closed'")->fetch_row()[0];
$total_approved = $koneksi->query("SELECT COUNT(*) FROM event_pengajuan WHERE status='approved'")->fetch_row()[0];
$total_rejected = $koneksi->query("SELECT COUNT(*) FROM event_pengajuan WHERE status='rejected'")->fetch_row()[0];

// Data user
$data_user = $koneksi->query("SELECT * FROM users ORDER BY user_id DESC");

// Data pengajuan
$data_pengajuan = $koneksi->query("SELECT ep.*, u.nama_lengkap, u.username FROM event_pengajuan ep LEFT JOIN users u ON ep.user_id=u.user_id ORDER BY ep.event_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(135deg, #c471f5 0%, #2980b9 100%);
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 48px auto;
            background: rgba(255,255,255,0.97);
            border-radius: 18px;
            box-shadow: 0 12px 32px rgba(52, 73, 94, 0.13);
            padding: 40px 32px;
            position: relative;
        }
        .logout {
            position: absolute;
            right: 32px;
            top: 32px;
            color: #c0392b;
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 1px;
            transition: color 0.2s;
        }
        .logout:hover {
            color: #e74c3c;
        }
        h2 {
            text-align: center;
            color: #c0392b;
            font-weight: 700;
            margin-bottom: 32px;
            letter-spacing: 1px;
        }
        .rekap {
            display: flex;
            gap: 32px;
            margin-bottom: 32px;
            justify-content: center;
        }
        .rekap-box {
            flex: 1;
            background: linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%);
            border-radius: 12px;
            padding: 28px 0;
            text-align: center;
            box-shadow: 0 4px 16px rgba(52, 73, 94, 0.07);
            transition: transform 0.2s;
        }
        .rekap-box:hover {
            transform: translateY(-6px) scale(1.03);
        }
        .rekap-box h3 {
            margin: 0 0 10px 0;
            color: #2980b9;
            font-size: 1.1em;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .rekap-box div {
            font-size: 2.5em;
            color: #8e44ad;
            font-weight: 700;
        }
        .section {
            margin-bottom: 40px;
        }
        form[method="POST"] {
            background: #f8fafd;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(52, 73, 94, 0.04);
            padding: 18px 14px;
            margin-bottom: 18px;
        }
        form[method="POST"] input,
        form[method="POST"] select {
            padding: 10px;
            border: 1.5px solid #8e44ad;
            border-radius: 7px;
            font-size: 1em;
            background: #f4f6fa;
            margin-bottom: 8px;
            margin-right: 8px;
            transition: border 0.2s;
        }
        form[method="POST"] input:focus,
        form[method="POST"] select:focus {
            border: 1.5px solid #2980b9;
            outline: none;
        }
        form[method="POST"] button {
            background: linear-gradient(90deg, #8e44ad 60%, #2980b9 100%);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 7px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(52, 73, 94, 0.07);
        }
        form[method="POST"] button:hover {
            background: linear-gradient(90deg, #2980b9 0%, #8e44ad 100%);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(52, 73, 94, 0.06);
        }
        th, td {
            border: none;
            padding: 14px 10px;
            text-align: center;
            font-size: 1em;
        }
        th {
            background: linear-gradient(90deg, #8e44ad 60%, #2980b9 100%);
            color: #fff;
            font-weight: 600;
            letter-spacing: 1px;
        }
        tr:nth-child(even) {
            background: #f8fafd;
        }
        tr:hover {
            background: #f1eafd;
        }
        .btn {
            background: linear-gradient(90deg, #2980b9 60%, #8e44ad 100%);
            color: #fff;
            border: none;
            padding: 7px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.98em;
            transition: background 0.2s;
        }
        .btn-danger {
            background: linear-gradient(90deg, #c0392b 60%, #e17055 100%);
        }
        .btn-approve {
            background: linear-gradient(90deg, #27ae60 60%, #6dd5ed 100%);
        }
        .btn-reject {
            background: linear-gradient(90deg, #e67e22 60%, #fbc2eb 100%);
        }
        .btn:disabled {
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
            form[method="POST"] { padding: 8px 2px; }
        }
    </style>
</head>
<body>
<div class="container">
    <a class="logout" href="logout.php">Logout</a>
    <h2>Dashboard Admin</h2>
    <div class="rekap">
        <div class="rekap-box">
            <h3>Total Pengajuan</h3>
            <div style="font-size:2em;"><?= $total_pengajuan ?></div>
        </div>
        <div class="rekap-box">
            <h3>Closed</h3>
            <div style="font-size:2em;"><?= $total_closed ?></div>
        </div>
        <div class="rekap-box">
            <h3>Approved</h3>
            <div style="font-size:2em;"><?= $total_approved ?></div>
        </div>
        <div class="rekap-box">
            <h3>Rejected</h3>
            <div style="font-size:2em;"><?= $total_rejected ?></div>
        </div>
    </div>

    <div class="section">
        <h3>Tambah User</h3>
        <form method="POST" style="display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
            <input type="text" name="eskul" placeholder="Eskul" required>
            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button class="btn" type="submit" name="tambah_user">Tambah</button>
        </form>
    </div>

    <div class="section">
        <h3>Data User</h3>
        <table>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Eskul</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            <?php $no=1; while($row = $data_user->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['Eskul']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td>
                    <?php if($row['role']!='admin'): ?>
                        <a href="?hapus_user=<?= $row['user_id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus user ini?')">Hapus</a>
                    <?php else: ?>
                        <button class="btn" disabled>Admin</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="section">
        <h3>Approval & Report Pengajuan Event</h3>
        <table>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>Judul Event</th>
                <th>Jenis Kegiatan</th>
                <th>Total Pembiayaan</th>
                <th>Proposal</th>
                <th>Deskripsi</th>
                <th>Tanggal Pengajuan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php $no=1; while($row = $data_pengajuan->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']) ?> (<?= htmlspecialchars($row['username']) ?>)</td>
                <td><?= htmlspecialchars($row['judul_event']) ?></td>
                <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['Total_pembiayaan']) ?></td>
                <td><?= htmlspecialchars($row['Proposal']) ?></td>
                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                <td>
                    <?php if($row['status']=='open'): ?>
                        <a href="?approve=<?= $row['event_id'] ?>" class="btn btn-approve" onclick="return confirm('Approve event ini?')">Approve</a>
                        <a href="?reject=<?= $row['event_id'] ?>" class="btn btn-reject" onclick="return confirm('Reject event ini?')">Reject</a>
                    <?php elseif($row['status']=='approved'): ?>
                        <button class="btn btn-approve" disabled>Approved</button>
                    <?php elseif($row['status']=='rejected'): ?>
                        <button class="btn btn-reject" disabled>Rejected</button>
                    <?php elseif($row['status']=='closed'): ?>
                        <button class="btn" disabled>Closed</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>