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

// Define variables and initialize with empty values
$judul_event = $jenis_kegiatan = $total_pembiayaan = $deskripsi = $tanggal_pengajuan = $proposal_file = "";
$judul_event_err = $jenis_kegiatan_err = $total_pembiayaan_err = $deskripsi_err = $tanggal_pengajuan_err = $proposal_file_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate judul_event
    if(empty(trim($_POST["judul_event"]))){
        $judul_event_err = "Mohon masukkan judul event.";
    } else{
        $judul_event = trim($_POST["judul_event"]);
    }

    // Validate jenis_kegiatan
    if(empty(trim($_POST["jenis_kegiatan"]))){
        $jenis_kegiatan_err = "Mohon masukkan jenis kegiatan.";
    } else{
        $jenis_kegiatan = trim($_POST["jenis_kegiatan"]);
        
        // Validasi khusus untuk lomba 17 Agustus
        if($jenis_kegiatan === "Lomba 17 Agustus") {
            // Cek apakah sudah ada pengajuan lomba 17 Agustus yang aktif
            $check_sql = "SELECT COUNT(*) as total FROM event_pengajuan 
                         WHERE jenis_kegiatan = 'Lomba 17 Agustus' 
                         AND status NOT IN ('rejected', 'closed')";
            
            if($check_result = mysqli_query($link, $check_sql)){
                $row = mysqli_fetch_assoc($check_result);
                if($row['total'] > 0){
                    $jenis_kegiatan_err = "Maaf, pengajuan lomba 17 Agustus sudah ada yang aktif.";
                }
            }
        }
    }
    
    // Validate total_pembiayaan
    if(empty(trim($_POST["total_pembiayaan"]))){
        $total_pembiayaan_err = "Mohon masukkan total pembiayaan.";
    } else{
        $total_pembiayaan = trim($_POST["total_pembiayaan"]);
    }

    // Validate deskripsi
    if(empty(trim($_POST["deskripsi"]))){
        $deskripsi_err = "Mohon masukkan deskripsi.";
    } else{
        $deskripsi = trim($_POST["deskripsi"]);
    }

    // Validate tanggal_pengajuan
    if(empty(trim($_POST["tanggal_pengajuan"]))){
        $tanggal_pengajuan_err = "Mohon masukkan tanggal pengajuan.";
    } else{
        $tanggal_pengajuan = trim($_POST["tanggal_pengajuan"]);
    }

    // Validate proposal file upload
    if(isset($_FILES["proposal"]) && $_FILES["proposal"]["error"] == 0){
        $allowed_ext = array("pdf", "doc", "docx");
        $file_name = $_FILES["proposal"]["name"];
        $file_type = $_FILES["proposal"]["type"];
        $file_size = $_FILES["proposal"]["size"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $upload_dir = "uploads/"; // Direktori untuk menyimpan file proposal
        $new_file_name = uniqid() . "." . $file_ext;
        $upload_path = $upload_dir . $new_file_name;

        // Buat direktori uploads jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if(!in_array($file_ext, $allowed_ext)){
            $proposal_file_err = "Hanya file PDF, DOC, dan DOCX yang diizinkan.";
        } elseif($file_size > 5 * 1024 * 1024) { // Batas ukuran file 5MB
            $proposal_file_err = "Ukuran file terlalu besar (maks 5MB).";
        } else{
            // Pindahkan file terunggah ke direktori tujuan
            if(move_uploaded_file($_FILES["proposal"]["tmp_name"], $upload_path)){
                $proposal_file = $upload_path;
            } else{
                $proposal_file_err = "Gagal mengunggah file proposal.";
            }
        }
    } else{
        $proposal_file_err = "Mohon unggah file proposal.";
    }


    // Check input errors before inserting in database
    if(empty($judul_event_err) && empty($jenis_kegiatan_err) && empty($total_pembiayaan_err) && empty($deskripsi_err) && empty($tanggal_pengajuan_err) && empty($proposal_file_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, Total_pembiyaan, Proposal, deskripsi, tanggal_pengajuan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isssssss", $param_user_id, $param_judul_event, $param_jenis_kegiatan, $param_total_pembiayaan, $param_proposal_file, $param_deskripsi, $param_tanggal_pengajuan, $param_status);
            
            // Set parameters
            $param_user_id = $_SESSION["user_id"]; // Ambil user_id dari session
            $param_judul_event = $judul_event;
            $param_jenis_kegiatan = $jenis_kegiatan;
            $param_total_pembiayaan = $total_pembiayaan;
            $param_proposal_file = $proposal_file;
            $param_deskripsi = $deskripsi;
            $param_tanggal_pengajuan = $tanggal_pengajuan;
            $param_status = "menunggu"; // Set status awal "menunggu"
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to user dashboard
                header("location: user_dashboard.php");
            } else{
                echo "Ada yang salah. Mohon coba lagi nanti.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Event Baru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Ajukan Event Baru</h2>
        <p>Silakan isi formulir di bawah ini untuk mengajukan event baru.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Judul Event</label>
                <input type="text" name="judul_event" value="<?php echo $judul_event; ?>">
                <span class="help-block"><?php echo $judul_event_err; ?></span>
            </div>
            
            <div class="form-group">
                <label for="jenis_kegiatan">Jenis Kegiatan:</label>
                <input type="text" name="jenis_kegiatan" id="jenis_kegiatan" class="form-control <?php echo (!empty($jenis_kegiatan_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($jenis_kegiatan); ?>" placeholder="Masukkan jenis kegiatan...">
                <span class="help-block"><?php echo $jenis_kegiatan_err; ?></span>
            </div>

            <div class="form-group">
                <label>Total Pembiayaan (Rp)</label>
                <input type="text" name="total_pembiayaan" value="<?php echo $total_pembiayaan; ?>">
                <span class="help-block"><?php echo $total_pembiayaan_err; ?></span>
            </div>

             <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="5"><?php echo $deskripsi; ?></textarea>
                <span class="help-block"><?php echo $deskripsi_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Tanggal Pengajuan</label>
                <input type="date" name="tanggal_pengajuan" value="<?php echo $tanggal_pengajuan; ?>">
                <span class="help-block"><?php echo $tanggal_pengajuan_err; ?></span>
            </div>

            <div class="form-group">
                <label>File Proposal (PDF, DOC, DOCX)</label>
                <input type="file" name="proposal">
                <span class="help-block"><?php echo $proposal_file_err; ?></span>
            </div>
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Ajukan">
                <a href="user_dashboard.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html> 