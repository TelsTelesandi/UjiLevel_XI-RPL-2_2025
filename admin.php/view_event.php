<?php
require_once 'koneksi_admin.php';

if (!isset($_GET['id'])) {
    echo "ID event tidak ditemukan!";
    exit;
}

$event_id = $_GET['id'];
$query = "SELECT e.*, u.nama_lengkap, u.eskul 
          FROM events e 
          JOIN users u ON e.username = u.username 
          WHERE e.event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Event tidak ditemukan!";
    exit;
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Event</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; }
        .container { max-width: 600px; margin: 2rem auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 2rem; }
        h2 { color: #1a237e; }
        .label { font-weight: bold; }
        .btn { background: #1a237e; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #283593; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Event</h2>
        <p><span class="label">Event ID:</span> <?php echo htmlspecialchars($event['event_id']); ?></p>
        <p><span class="label">Judul Event:</span> <?php echo htmlspecialchars($event['judul_event']); ?></p>
        <p><span class="label">Ekskul:</span> <?php echo htmlspecialchars($event['eskul']); ?></p>
        <p><span class="label">Pemohon:</span> <?php echo htmlspecialchars($event['nama_lengkap']); ?></p>
        <p><span class="label">Tanggal Pengajuan:</span> <?php echo htmlspecialchars($event['tanggal_pengajuan']); ?></p>
        <p><span class="label">Status:</span> <?php echo htmlspecialchars($event['status']); ?></p>
        <p><span class="label">Catatan Admin:</span> <?php echo isset($event['catatan_admin']) && $event['catatan_admin'] !== null && $event['catatan_admin'] !== '' ? htmlspecialchars($event['catatan_admin']) : '-'; ?></p>
        <a href="dashboard_admin.php" class="btn">Kembali ke Dashboard</a>
    </div>
</body>
</html>