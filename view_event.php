<?php
// Mulai session
session_start();

// Cek apakah user sudah login, jika tidak, redirect ke halaman login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Include file config
require_once "config.php";

$event_details = null;
$user_who_submitted = null;
$verification_details = null;
$event_id = null;

// Proses parameter event_id dari URL
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get URL parameter
    $event_id = trim($_GET["id"]);
    
    // Siapkan query untuk mengambil detail event
    $sql_event = "SELECT * FROM event_pengajuan WHERE event_id = ?";
    
    if($stmt_event = mysqli_prepare($link, $sql_event)){
        mysqli_stmt_bind_param($stmt_event, "i", $param_event_id);
        $param_event_id = $event_id;
        
        if(mysqli_stmt_execute($stmt_event)){
            $result_event = mysqli_stmt_get_result($stmt_event);
            
            if(mysqli_num_rows($result_event) == 1){
                $event_details = mysqli_fetch_assoc($result_event);
                
                // Otorisasi: Cek jika user adalah Admin atau pemilik pengajuan
                if($_SESSION["role"] !== "Admin" && $_SESSION["user_id"] !== $event_details["user_id"]){
                    // Jika bukan Admin dan bukan pemilik, redirect atau tampilkan pesan error
                    header("location: user_dashboard.php"); // Redirect ke dashboard user
                    exit();
                }

                // Ambil data user yang mengajukan
                $sql_user = "SELECT nama_lengkap FROM users WHERE user_id = ?";
                 if($stmt_user = mysqli_prepare($link, $sql_user)){
                    mysqli_stmt_bind_param($stmt_user, "i", $param_user_id);
                    $param_user_id = $event_details["user_id"];
                    if(mysqli_stmt_execute($stmt_user)){
                         $result_user = mysqli_stmt_get_result($stmt_user);
                         if(mysqli_num_rows($result_user) == 1){
                            $user_who_submitted = mysqli_fetch_assoc($result_user);
                         }
                    }
                    mysqli_stmt_close($stmt_user);
                 }

                 // Ambil data verifikasi event (jika ada)
                 $sql_verif = "SELECT v.*, a.username as admin_username FROM verifikasi_event v LEFT JOIN users a ON v.admin_id = a.user_id WHERE v.event_id = ? LIMIT 1";
                  if($stmt_verif = mysqli_prepare($link, $sql_verif)){
                     mysqli_stmt_bind_param($stmt_verif, "i", $param_event_id_verif);
                     $param_event_id_verif = $event_id;
                     if(mysqli_stmt_execute($stmt_verif)){
                          $result_verif = mysqli_stmt_get_result($stmt_verif);
                          if(mysqli_num_rows($result_verif) == 1){
                             $verification_details = mysqli_fetch_assoc($result_verif);
                          }
                     }
                     mysqli_stmt_close($stmt_verif);
                  }


            } else{
                // Event tidak ditemukan
                echo "Event tidak ditemukan.";
                exit();
            }
            
        } else{
            echo "Oops! Ada yang salah saat mengambil detail event. Silakan coba lagi nanti.";
            exit();
        }

        // Close statement
        mysqli_stmt_close($stmt_event);
    }
    
    // Close connection (akan ditutup di bagian akhir setelah semua query)
    // mysqli_close($link);

} else{
    // Jika parameter id tidak ada atau kosong
     header("location: user_dashboard.php"); // Redirect kembali ke dashboard user jika tidak ada ID
     exit();
}

// Tutup koneksi database di akhir skrip
mysqli_close($link);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php if ($event_details): ?>
            <h2>Detail Event: <?php echo htmlspecialchars($event_details['judul_event']); ?></h2>

            <div class="detail-item">
                <label>Diajukan Oleh:</label>
                <p><?php echo htmlspecialchars($user_who_submitted['nama_lengkap'] ?? 'N/A'); ?></p>
            </div>

            <div class="detail-item">
                <label>Judul Event:</label>
                <p><?php echo htmlspecialchars($event_details['judul_event']); ?></p>
            </div>

            <div class="detail-item">
                <label>Jenis Kegiatan:</label>
                <p><?php echo htmlspecialchars($event_details['jenis_kegiatan']); ?></p>
            </div>

            <div class="detail-item">
                <label>Total Pembiayaan:</label>
                <p>Rp <?php echo htmlspecialchars($event_details['Total_pembiayaan'] ?? 'N/A'); ?></p>
            </div>

             <div class="detail-item">
                <label>Deskripsi:</label>
                <p><?php echo nl2br(htmlspecialchars($event_details['deskripsi'])); ?></p>
            </div>
            
            <div class="detail-item">
                <label>Tanggal Pengajuan:</label>
                <p><?php echo htmlspecialchars($event_details['tanggal_pengajuan']); ?></p>
            </div>

            <div class="detail-item">
                <label>Status Pengajuan:</label>
                <p><?php echo htmlspecialchars($event_details['status']); ?></p>
            </div>

            <div class="detail-item">
                <label>File Proposal:</label>
                <?php if (!empty($event_details['Proposal'])): ?>
                    <p><a href="<?php echo htmlspecialchars($event_details['Proposal']); ?>" target="_blank">Unduh Proposal</a></p>
                <?php else: ?>
                    <p>Tidak ada file proposal diunggah.</p>
                <?php endif; ?>
            </div>

            <?php if ($verification_details): ?>
                 <div class="verification-section">
                    <h3>Status Verifikasi</h3>
                     <div class="detail-item">
                        <label>Status Verifikasi:</label>
                        <p><?php echo htmlspecialchars($verification_details['Status']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Tanggal Verifikasi:</label>
                        <p><?php echo htmlspecialchars($verification_details['tanggal_verifikasi']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Diverifikasi Oleh (Admin):</label>
                        <p><?php echo htmlspecialchars($verification_details['admin_username'] ?? 'N/A'); ?></p>
                    </div>
                     <div class="detail-item">
                        <label>Catatan Admin:</label>
                        <p><?php echo nl2br(htmlspecialchars($verification_details['catatan_admin'])); ?></p>
                    </div>
                 </div>
            <?php endif; ?>

            <p>
                <?php if($_SESSION["role"] === "Admin"): ?>
                     <a href="admin_dashboard.php" class="btn btn-secondary">Kembali ke Dashboard Admin</a>
                <?php else: ?>
                    <a href="user_dashboard.php" class="btn btn-secondary">Kembali ke Dashboard Pengguna</a>
                <?php endif; ?>
                 <?php if($_SESSION["role"] === "Admin" && $event_details['status'] === 'menunggu'): ?>
                     <!-- Link untuk Approval/Reject akan ditambahkan di sini -->
                      | <a href="admin_approve_request.php?id=<?php echo $event_id; ?>" class="btn btn-primary">Setujui</a>
                     <a href="admin_reject_request.php?id=<?php echo $event_id; ?>" class="btn btn-danger">Tolak</a>
                 <?php endif; ?>

            </p>

        <?php else: ?>
            <p>Event tidak ditemukan atau Anda tidak memiliki izin untuk melihatnya.</p>
             <p>
                <?php if($_SESSION["role"] === "Admin"): ?>
                     <a href="admin_dashboard.php" class="btn btn-secondary">Kembali ke Dashboard Admin</a>
                <?php else: ?>
                    <a href="user_dashboard.php" class="btn btn-secondary">Kembali ke Dashboard Pengguna</a>
                <?php endif; ?>
            </p>
        <?php endif; ?>

    </div>
</body>
</html> 