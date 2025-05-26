<?php
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/navbar.php';

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">ID event tidak ditemukan.</div>';
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT e.*, u.nama_lengkap, u.email FROM event_pengajuan e JOIN users u ON e.user_id = u.id WHERE e.id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    echo '<div class="alert alert-danger">Data event tidak ditemukan.</div>';
    exit();
}
?>

<main>
    <section class="dashboard-section" style="max-width:600px; margin:40px auto;">
        <h2 style="margin-bottom:18px;"><i class="fas fa-calendar-alt" style="color:#2563eb"></i> Detail Event</h2>
        <table style="width:100%; font-size:1.08rem;">
            <tr><th style="width:160px; text-align:left;">Nama Event</th><td>: <?php echo htmlspecialchars($event['nama_event']); ?></td></tr>
            <tr><th>Ekskul</th><td>: <?php echo htmlspecialchars($event['ekskul']); ?></td></tr>
            <tr><th>Pengaju</th><td>: <?php echo htmlspecialchars($event['nama_lengkap']); ?> (<?php echo htmlspecialchars($event['email']); ?>)</td></tr>
            <tr><th>Tanggal</th><td>: <?php echo htmlspecialchars($event['tanggal']); ?></td></tr>
            <tr><th>Status</th><td>: <span class="<?php 
                if ($event['status'] == 'approved') echo 'status-approved';
                elseif ($event['status'] == 'rejected') echo 'status-rejected';
                else echo 'status-pending';
            ?>"><?php echo htmlspecialchars($event['status']); ?></span></td></tr>
            <tr><th>Deskripsi</th><td>: <?php echo (array_key_exists('deskripsi', $event) && $event['deskripsi'] !== null && $event['deskripsi'] !== '') ? nl2br(htmlspecialchars($event['deskripsi'])) : '-'; ?></td></tr>
            <tr><th>Kebutuhan</th><td>: <?php echo (array_key_exists('kebutuhan', $event) && $event['kebutuhan'] !== null && $event['kebutuhan'] !== '') ? nl2br(htmlspecialchars($event['kebutuhan'])) : '-'; ?></td></tr>
        </table>
        <div style="margin-top:28px; text-align:right;">
            <a href="events.php" class="btn btn-secondary">&larr; Kembali ke Kelola Event</a>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?> 