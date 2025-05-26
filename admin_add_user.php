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

// Define variables and initialize with empty values
$username = $password = $role = $nama_lengkap = $ekskul = "";
$username_err = $password_err = $role_err = $nama_lengkap_err = $ekskul_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Mohon masukkan username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT user_id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Username ini sudah ada.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Ada yang salah. Silakan coba lagi nanti.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Mohon masukkan password.";     
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate role
     if(empty(trim($_POST["role"]))){
        $role_err = "Mohon pilih role.";     
    } else{
        $role = trim($_POST["role"]);
    }

    // Validate nama_lengkap (optional, allow empty)
    $nama_lengkap = trim($_POST["nama_lengkap"]);

    // Validate Ekskul (optional, allow empty)
    $ekskul = trim($_POST["ekskul"]);


    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($role_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, role, nama_lengkap, Ekskul) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_role, $param_nama_lengkap, $param_ekskul);
            
            // Set parameters
            $param_username = $username;
            $param_password = $password;
            $param_role = $role;
            $param_nama_lengkap = $nama_lengkap;
            $param_ekskul = $ekskul;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: admin_manage_users.php");
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
    <title>Tambah Pengguna Baru</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Tambah Pengguna Baru</h2>
        <p>Silakan isi formulir di bawah ini untuk menambahkan pengguna baru.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="">Pilih Role</option>
                    <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="User" <?php echo ($role == 'User') ? 'selected' : ''; ?>>User</option>
                </select>
                 <span class="help-block"><?php echo $role_err; ?></span>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="<?php echo $nama_lengkap; ?>">
                 <span class="help-block"><?php echo $nama_lengkap_err; ?></span>
            </div>

             <div class="form-group">
                <label>Ekskul</label>
                <input type="text" name="ekskul" value="<?php echo $ekskul; ?>">
                 <span class="help-block"><?php echo $ekskul_err; ?></span>
            </div>
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Tambah Pengguna">
                <a href="admin_manage_users.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html> 