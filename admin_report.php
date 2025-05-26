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

$report_data = [];

// Query untuk mengambil semua data pengajuan event, detail user, dan detail verifikasi
// Menggunakan LEFT JOIN untuk memastikan semua pengajuan ditampilkan, meskipun belum diverifikasi
$sql = "SELECT ep.*, u.nama_lengkap, ve.tanggal_verifikasi, ve.catatan_admin, ve.Status AS verifikasi_status, a.username AS admin_verifikator ";
$sql .= "FROM event_pengajuan ep ";
$sql .= "JOIN users u ON ep.user_id = u.user_id "; // Gunakan JOIN karena pengajuan selalu memiliki user_id
$sql .= "LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id ";
$sql .= "LEFT JOIN users a ON ve.admin_id = a.user_id "; // LEFT JOIN untuk admin verifikator
$sql .= "ORDER BY ep.tanggal_pengajuan DESC";

if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $report_data[] = $row;
        }
        // Free result set
        mysqli_free_result($result);
    } else{
        // Tidak ada pengajuan ditemukan
    }
} else{
    echo "Oops! Ada yang salah saat mengambil data laporan. Silakan coba lagi nanti." . mysqli_error($link);
}

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajuan Event - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container report-container">
        <div class="dashboard-header">
            <div class="user-info">
                 <i class="fas fa-chart-bar fa-2x" style="color: var(--primary-color);"></i>
                 <h2>Laporan Pengajuan Event</h2>
                 <p>Ringkasan seluruh pengajuan event dan status verifikasinya.</p>
            </div>

            <div class="dashboard-links">
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-circle-left"></i> Kembali ke Dashboard</a>
                <a href="admin_manage_users.php" class="btn btn-secondary"><i class="fas fa-users"></i> Manajemen Pengguna</a>
            </div>
        </div>

        <div class="dashboard-content">
            <?php if(empty($report_data)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Belum ada data pengajuan event untuk dilaporkan.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Event</th>
                                <th>Diajukan Oleh</th>
                                <th>Judul Event</th>
                                <th>Jenis Kegiatan</th>
                                <th>Total Pembiayaan</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status Pengajuan</th>
                                <th>Status Verifikasi</th>
                                <th>Tanggal Verifikasi</th>
                                <th>Diverifikasi Oleh</th>
                                <th>Alasan</th>
                                <th>Proposal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['event_id']); ?></td>
                                    <td><?php echo htmlspecialchars($event['nama_lengkap'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                    <td><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                    <td>Rp <?php echo htmlspecialchars($event['Total_pembiyaan']); ?></td>
                                    <td><?php echo htmlspecialchars($event['tanggal_pengajuan']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($event['status']); ?>">
                                            <?php 
                                                // Tampilkan teks status dalam Bahasa Indonesia
                                                switch ($event['status']) {
                                                    case 'rejected':
                                                        echo 'Ditolak';
                                                        break;
                                                    case 'approved':
                                                        echo 'Disetujui';
                                                        break;
                                                    case 'pending':
                                                        echo 'Menunggu';
                                                        break;
                                                    case 'closed':
                                                        echo 'Ditutup';
                                                        break;
                                                    default:
                                                        echo htmlspecialchars($event['status']); // Tampilkan status lain jika ada
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                         <?php if ($event['verifikasi_status']): ?>
                                            <span class="status-badge status-<?php echo strtolower($event['verifikasi_status']); ?>">
                                                 <?php 
                                                    // Tampilkan teks status verifikasi dalam Bahasa Indonesia
                                                     switch ($event['verifikasi_status']) {
                                                        case 'rejected':
                                                            echo 'Ditolak';
                                                            break;
                                                        case 'approved':
                                                            echo 'Disetujui';
                                                            break;
                                                        case 'pending':
                                                            echo 'Menunggu';
                                                            break;
                                                        // Status verifikasi seharusnya tidak 'closed' jika status utama 'closed'
                                                        // Tapi jika ada, tampilkan teks default atau strip jika kosong
                                                        default:
                                                             echo htmlspecialchars($event['verifikasi_status']);
                                                             break;
                                                    }
                                                ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending">Belum diverifikasi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($event['tanggal_verifikasi'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($event['admin_verifikator'] ?? '-'); ?></td>
                                    <td>
                                        <?php 
                                            $catatan_admin = $event['catatan_admin'] ?? '';
                                            if ($event['verifikasi_status'] === 'rejected'): 
                                        ?>
                                            <span class="alasan-penolakan">
                                                <i class="fas fa-exclamation-circle"></i>
                                                <?php echo htmlspecialchars(!empty($catatan_admin) ? $catatan_admin : 'Ditolak oleh Admin'); ?>
                                            </span>
                                        <?php elseif ($event['verifikasi_status'] === 'approved'): ?>
                                            <span class="alasan-persetujuan">
                                                <i class="fas fa-check-circle"></i>
                                                <?php echo htmlspecialchars(!empty($catatan_admin) ? $catatan_admin : 'Disetujui oleh Admin'); ?>
                                            </span>
                                        <?php elseif (!empty($catatan_admin)): ?>
                                            <?php echo htmlspecialchars($catatan_admin); // Tampilkan catatan admin jika ada tapi status bukan rejected/approved ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($event['Proposal'])): ?>
                                            <a href="<?php echo htmlspecialchars($event['Proposal']); ?>" target="_blank" class="btn btn-info btn-sm">
                                                <i class="fas fa-download"></i> Lihat/Unduh
                                            </a>
                                        <?php else: ?>
                                            Tidak ada
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($event['status'] !== 'closed' && $event['status'] !== 'rejected'): ?>
                                            <div class="action-buttons">
                                                <a href="admin_close_request.php?id=<?php echo $event['event_id']; ?>" 
                                                   class="btn btn-warning btn-sm"
                                                   onclick="return confirm('Apakah Anda yakin ingin menutup request ini?')">
                                                    <i class="fas fa-times-circle"></i> Tutup Request
                                                </a>
                                                <a href="admin_reject_with_reason.php?id=<?php echo $event['event_id']; ?>" 
                                                   class="btn btn-danger btn-sm">
                                                    <i class="fas fa-ban"></i> Tolak Request
                                                </a>
                                            </div>
                                        <?php elseif ($event['status'] === 'closed'): ?>
                                            <span class="status-badge status-closed">Ditutup</span>
                                        <?php elseif ($event['status'] === 'rejected'): ?>
                                            <span class="status-badge status-rejected">Ditolak</span>
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
</html>