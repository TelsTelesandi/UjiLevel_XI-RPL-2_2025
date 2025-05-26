<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil informasi user
$username = $_SESSION['username'];
$nama_lengkap = $_SESSION['nama_lengkap'];
$eskul = $_SESSION['eskul'];

// Ambil data event dari database
$query = "SELECT * FROM events WHERE username = ? ORDER BY tanggal_pengajuan DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Event Ekstrakurikuler</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f6f9;
        }

        .navbar {
            background: linear-gradient(135deg, #4caf50, #45a049);
            padding: 1rem 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            font-size: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background-color: #e53935;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c62828;
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .welcome-card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            border-left: 5px solid #4caf50;
        }

        .event-section {
            background-color: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .add-event-btn {
            background-color: #4caf50;
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .add-event-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .event-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .event-table th,
        .event-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .event-table th {
            background-color: #f8f9fa;
            color: #2e7d32;
            font-weight: 600;
        }

        .event-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .status-approved {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .view-pdf {
            color: #4caf50;
            text-decoration: none;
            font-weight: 500;
        }

        .view-pdf:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Pengajuan Event Ekstrakurikuler</h1>
        <div class="user-info">
            <span>Selamat datang, <?php echo htmlspecialchars($nama_lengkap); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat Datang di Dashboard <?php echo htmlspecialchars($eskul); ?></h2>
            <p>Anda login sebagai: <?php echo htmlspecialchars($username); ?></p>
        </div>

        <div class="event-section">
            <div class="event-header">
                <h2>Daftar Pengajuan Event</h2>
                <a href="tambah_event.php" class="add-event-btn">+ Ajukan Event Baru</a>
            </div>

            <?php if($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="event-table">
                    <thead>
                        <tr>
                            <th>Event ID</th>
                            <th>Judul Event</th>
                            <th>Jenis Kegiatan</th>
                            <th>Total Pembiayaan</th>
                            <th>Proposal</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['event_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['judul_event']); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis_kegiatan']); ?></td>
                            <td>Rp <?php echo number_format($row['total_pembiayaan'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if($row['proposal_path']): ?>
                                    <a href="<?php echo htmlspecialchars($row['proposal_path']); ?>" class="view-pdf" target="_blank">Lihat PDF</a>
                                <?php else: ?>
                                    Belum ada proposal
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars(substr($row['deskripsi'], 0, 50)) . '...'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_pengajuan'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo $row['status'] == 'Approved' ? 'status-approved' : 'status-pending'; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <p>Belum ada event yang ditambahkan. Klik tombol "Ajukan Event Baru" untuk membuat event pertama Anda.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
