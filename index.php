<?php
// Mulai session
session_start();

// Cek apakah user sudah login, jika ya, redirect ke dashboard
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    // Cek peran user dan redirect sesuai peran
    if(isset($_SESSION["role"])) {
        if($_SESSION["role"] === "Admin") {
            header("location: admin_dashboard.php");
        } else {
            header("location: user_dashboard.php");
        }
    } else {
        // Jika peran tidak ditemukan, redirect ke user dashboard secara default atau halaman error
         header("location: user_dashboard.php");
    }
    exit;
}

// Include file config
require_once "config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

// Proses data saat form di submit
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Cek jika username kosong
    if(empty(trim($_POST["username"]))){
        $username_err = "Mohon masukkan username.";
    } else{
        $username = trim($_POST["username"]);
    }

    // Cek jika password kosong
    if(empty(trim($_POST["password"]))){
        $password_err = "Mohon masukkan password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validasi kredensial
    if(empty($username_err) && empty($password_err)){
        // Siapkan query select
        $sql = "SELECT user_id, username, password, role, nama_lengkap FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables ke prepared statement sebagai parameter
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameter
            $param_username = $username;

            // Jalankan prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Simpan hasil
                mysqli_stmt_store_result($stmt);

                // Cek jika username ada, kalau ya, verifikasi password
                if(mysqli_stmt_num_rows($stmt) == 1){
                    // Bind hasil variable
                    mysqli_stmt_bind_result($stmt, $user_id, $username, $hashed_password, $role, $nama_lengkap);
                    if(mysqli_stmt_fetch($stmt)){
                        if($password === $hashed_password){
                            // Password benar, mulai session
                            session_start();

                            // Simpan data di session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user_id"] = $user_id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;
                            $_SESSION["nama_lengkap"] = $nama_lengkap;

                            // Redirect user ke halaman dashboard sesuai peran
                            if($role === "Admin") {
                                header("location: admin_dashboard.php");
                            } else {
                                header("location: user_dashboard.php");
                            }
                        } else{
                            // Password tidak valid
                            $login_err = "Username atau password salah.";
                        }
                    }
                } else{
                    // Username tidak ada
                    $login_err = "Username atau password salah.";
                }
            } else{
                echo "Oops! Ada yang salah. Silakan coba lagi nanti.";
            }

            // Tutup statement
            mysqli_stmt_close($stmt);
        }
    }

    // Tutup koneksi
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengajuan Event</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-calendar-check fa-3x" style="color: var(--primary-color); margin-bottom: 20px;"></i>
            <h2>Sistem Pengajuan Event</h2>
            <p>Silakan login untuk melanjutkan</p>
        </div>
        
        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" placeholder="Masukkan username">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="Masukkan password">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </div>
        </form>
    </div>
</body>
</html> 