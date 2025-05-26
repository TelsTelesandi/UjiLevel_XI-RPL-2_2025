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

// Proses form ketika di-submit
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["event_id"]) && isset($_POST["alasan_penolakan"])){
        $event_id = trim($_POST["event_id"]);
        $alasan_penolakan = trim($_POST["alasan_penolakan"]);
        $admin_id = $_SESSION["user_id"];
        
        // Update status event menjadi rejected
        $sql = "UPDATE event_pengajuan SET status = 'rejected' WHERE event_id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $event_id);
            
            if(mysqli_stmt_execute($stmt)){
                // Insert ke tabel verifikasi_event
                $sql = "INSERT INTO verifikasi_event (event_id, admin_id, Status, catatan_admin, tanggal_verifikasi) 
                        VALUES (?, ?, 'rejected', ?, NOW())";
                
                if($stmt2 = mysqli_prepare($link, $sql)){
                    mysqli_stmt_bind_param($stmt2, "iis", $event_id, $admin_id, $alasan_penolakan);
                    
                    if(mysqli_stmt_execute($stmt2)){
                        // Redirect kembali ke dashboard
                        header("location: admin_dashboard.php");
                        exit();
                    } else {
                        echo "Oops! Ada yang salah saat menyimpan alasan penolakan.";
                    }
                    mysqli_stmt_close($stmt2);
                }
            } else {
                echo "Oops! Ada yang salah saat memperbarui status event.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);
} else {
    // Jika bukan POST request, redirect ke dashboard
    header("location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tolak Pengajuan Event - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <div class="user-info">
                <i class="fas fa-times-circle fa-2x" style="color: var(--danger-color);"></i>
                <h2>Tolak Pengajuan Event</h2>
                <p>Berikan alasan penolakan untuk pengajuan event ini.</p>
            </div>
        </div>

        <div class="dashboard-content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                
                <div class="form-group">
                    <label for="alasan_penolakan">Alasan Penolakan:</label>
                    <textarea name="alasan_penolakan" id="alasan_penolakan" rows="5" required 
                              class="form-control" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Tolak Pengajuan
                    </button>
                    <a href="admin_report.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 