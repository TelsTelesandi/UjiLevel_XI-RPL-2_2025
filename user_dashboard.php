<?php
// Mulai session
session_start();

// Cek apakah user sudah login, jika tidak, redirect ke halaman login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Cek apakah user adalah Admin, jika ya, redirect ke halaman admin dashboard
if(isset($_SESSION["role"]) && $_SESSION["role"] === "Admin") {
    header("location: admin_dashboard.php");
    exit;
}

// Include file config
require_once "config.php";

$user_id = $_SESSION["user_id"];
$user_events = [];

// Query untuk mengambil data event yang diajukan oleh user ini
// LEFT JOIN dengan verifikasi_event dan users (untuk nama admin verifikator) untuk menampilkan status verifikasi
$sql = "SELECT ep.event_id, ep.judul_event, ep.tanggal_pengajuan, ep.status, ve.Status AS verifikasi_status, a.username AS admin_verifikator ";
$sql .= "FROM event_pengajuan ep ";
$sql .= "LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id ";
$sql .= "LEFT JOIN users a ON ve.admin_id = a.user_id ";
$sql .= "WHERE ep.user_id = ? ORDER BY ep.tanggal_pengajuan DESC";

if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $param_user_id);
    
    // Set parameters
    $param_user_id = $user_id;
    
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        
        // Fetch all results into an array
        while($row = mysqli_fetch_assoc($result)){
            $user_events[] = $row;
        }
        
    } else{
        echo "Oops! Ada yang salah. Silakan coba lagi nanti." . mysqli_error($link);
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna - Sistem Pengajuan Event</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <div class="user-info">
                <i class="fas fa-user fa-2x" style="color: var(--primary-color);"></i>
                <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION["nama_lengkap"] ?? $_SESSION["username"]); ?>!</h2>
                <p>Panel Kontrol Pengguna</p>
            </div>
            
            <div class="dashboard-links">
                <a href="user_request_event.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajukan Event Baru
                </a>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="content-header">
                <h3><i class="fas fa-list"></i> Daftar Pengajuan Event Anda</h3>
            </div>

            <?php if(empty($user_events)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Anda belum mengajukan event apapun.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul Event</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status Pengajuan</th>
                                <th>Status Verifikasi</th>
                                <th>Diverifikasi Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                    <td><?php echo htmlspecialchars($event['tanggal_pengajuan']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($event['status']); ?>">
                                            <?php echo htmlspecialchars($event['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($event['verifikasi_status']): ?>
                                            <span class="status-badge status-<?php echo strtolower($event['verifikasi_status']); ?>">
                                                <?php echo htmlspecialchars($event['verifikasi_status']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending">Belum diverifikasi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($event['admin_verifikator'] ?? '-'); ?></td>
                                    <td class="action-links">
                                        <a href="view_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <?php if ($event['status'] !== 'closed'): ?>
                                            <a href="close_request.php?id=<?php echo $event['event_id']; ?>" 
                                               class="btn btn-warning btn-sm"
                                               onclick="return confirm('Anda yakin ingin menandai pengajuan ini selesai?');">
                                                <i class="fas fa-check-circle"></i> Tandai Selesai
                                            </a>
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
            <a href="logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</body>
</html> 