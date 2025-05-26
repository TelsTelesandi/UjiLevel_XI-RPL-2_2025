<?php
// Mulai session
session_start();

// Cek apakah user sudah login dan memiliki peran Admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin"){
    // Jika tidak, redirect ke halaman login
    header("location: index.php");
    exit;
}

// Include file config
require_once "config.php";

$all_events = [];

// Query untuk mengambil semua data event pengajuan dan data user yang mengajukan
$sql = "SELECT ep.*, u.nama_lengkap FROM event_pengajuan ep JOIN users u ON ep.user_id = u.user_id ORDER BY ep.tanggal_pengajuan DESC";

if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $all_events[] = $row;
        }
        // Free result set
        mysqli_free_result($result);
    } else{
        // Tidak ada pengajuan ditemukan
    }
} else{
    echo "Oops! Ada yang salah. Silakan coba lagi nanti." . mysqli_error($link);
}

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Pengajuan Event</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <div class="user-info">
                <i class="fas fa-user-shield fa-2x" style="color: var(--primary-color);"></i>
                <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
                <p>Panel Kontrol Administrator</p>
            </div>
            
            <div class="dashboard-links">
                <a href="admin_manage_users.php" class="btn btn-secondary"><i class="fas fa-users"></i> Manajemen Pengguna</a>
                <a href="admin_report.php" class="btn btn-secondary"><i class="fas fa-chart-bar"></i> Laporan</a>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="content-header">
                <h3><i class="fas fa-list"></i> Daftar Pengajuan Event</h3>
            </div>

            <?php if(empty($all_events)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Belum ada pengajuan event yang masuk.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul Event</th>
                                <th>Pengaju</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                    <td><?php echo htmlspecialchars($event['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($event['tanggal_pengajuan']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($event['status']); ?>">
                                            <?php echo htmlspecialchars($event['status']); ?>
                                        </span>
                                    </td>
                                    <td class="action-links">
                                        <a href="view_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <?php if ($event['status'] === 'menunggu'): ?>
                                            <a href="admin_approve_request.php?id=<?php echo $event['event_id']; ?>" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Setujui
                                            </a>
                                            <button onclick="showRejectModal(<?php echo $event['event_id']; ?>)" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-footer">
            <a href="logout.php" class="logout-link btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</body>
<script>
function showRejectModal(eventId) {
    document.getElementById('event_id').value = eventId;
    document.getElementById('rejectModal').style.display = 'block';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

// Menutup modal jika user klik di luar modal
window.onclick = function(event) {
    var modal = document.getElementById('rejectModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<!-- Modal untuk penolakan -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-times-circle"></i> Tolak Pengajuan Event</h3>
            <span class="close" onclick="closeRejectModal()">&times;</span>
        </div>
        <form action="admin_reject_with_reason.php" method="post">
            <input type="hidden" name="event_id" id="event_id">
            <div class="form-group">
                <label for="alasan_penolakan">Alasan Penolakan:</label>
                <textarea name="alasan_penolakan" id="alasan_penolakan" rows="5" required 
                          class="form-control" placeholder="Masukkan alasan penolakan..."></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-ban"></i> Tolak Pengajuan
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 5px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>
</html> 