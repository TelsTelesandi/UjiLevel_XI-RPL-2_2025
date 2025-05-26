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
$username = $role = $nama_lengkap = $ekskul = "";
$username_err = $role_err = $nama_lengkap_err = $ekskul_err = "";

// Process $_GET["id"] parameter
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get URL parameter
    $user_id = trim($_GET["id"]);
    
    // Prepare a select statement
    $sql = "SELECT username, role, nama_lengkap, Ekskul FROM users WHERE user_id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_user_id);
        
        // Set parameters
        $param_user_id = $user_id;
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_assoc($result);
                
                // Retrieve individual field value
                $username = $row["username"];
                $role = $row["role"];
                $nama_lengkap = $row["nama_lengkap"];
                $ekskul = $row["Ekskul"];
            } else{
                // URL doesn't contain valid id. Redirect to error page or manage users.
                header("location: admin_manage_users.php");
                exit();
            }
            
        } else{
            echo "Oops! Ada yang salah. Silakan coba lagi nanti.";
        }
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
    
} elseif($_SERVER["REQUEST_METHOD"] == "POST"){
    // Process form data when form is submitted

    // Get user_id from hidden input
    $user_id = $_POST["user_id"];

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Mohon masukkan username.";
    } else{
         // Prepare a select statement to check for duplicate username (excluding current user)
        $sql = "SELECT user_id FROM users WHERE username = ? AND user_id != ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "si", $param_username, $param_user_id);
            
            $param_username = trim($_POST["username"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Username ini sudah ada.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Ada yang salah. Silakan coba lagi nanti.";
            }

            mysqli_stmt_close($stmt);
        }
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

    // Check input errors before updating in database
    if(empty($username_err) && empty($role_err)){
        
        // Prepare an update statement
        $sql = "UPDATE users SET username = ?, role = ?, nama_lengkap = ?, Ekskul = ? WHERE user_id = ?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssi", $param_username, $param_role, $param_nama_lengkap, $param_ekskul, $param_user_id);
            
            // Set parameters
            $param_username = $username;
            $param_role = $role;
            $param_nama_lengkap = $nama_lengkap;
            $param_ekskul = $ekskul;
            $param_user_id = $user_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to manage users page
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
} else{
    // URL doesn't contain id parameter. Redirect to error page or manage users.
     header("location: admin_manage_users.php");
     exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Pengguna</h2>
        <p>Edit detail pengguna di bawah ini.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="User" <?php echo ($role == 'User') ? 'selected' : ''; ?>>User</option>
                     <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
                 <span class="help-block"><?php echo $role_err; ?></span>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($nama_lengkap); ?>">
                 <span class="help-block"><?php echo $nama_lengkap_err; ?></span>
            </div>

             <div class="form-group">
                <label>Ekskul</label>
                <input type="text" name="ekskul" value="<?php echo htmlspecialchars($ekskul); ?>">
                 <span class="help-block"><?php echo $ekskul_err; ?></span>
            </div>
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Simpan Perubahan">
                <a href="admin_manage_users.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html> 