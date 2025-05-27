<?php
session_start();

// Include database connection file
require_once '../db_connect.php'; // Note the '../' to go up one directory

// Check if the admin is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: login.php"); // Redirect to admin login page
    exit;
}

$username = $password = $role = $nama_lengkap = '';
$username_err = $password_err = $role_err = $nama_lengkap_err = '';
$success_msg = '';
$error_msg = ''; // Add error message variable

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Mohon masukkan username.";
    } else {
        // Prepare a select statement to check if username already exists
        $sql = "SELECT user_id FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Username ini sudah terdaftar.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                $error_msg = "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Mohon masukkan password.";     
    } else {
        $password = trim($_POST["password"]);
        // TODO: Add password hashing here in a real application
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    // Validate role
    if (empty(trim($_POST["role"]))) {
        $role_err = "Mohon pilih role.";
    } else {
        $role = trim($_POST["role"]);
        // Optional: Add validation to check if the role is a valid option (e.g., 'admin', 'user')
    }
    
    // Validate nama_lengkap
    if (empty(trim($_POST["nama_lengkap"]))) {
        $nama_lengkap_err = "Mohon masukkan nama lengkap.";
    } else {
        $nama_lengkap = trim($_POST["nama_lengkap"]);
    }
    
    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($role_err) && empty($nama_lengkap_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, role, nama_lengkap) VALUES (?, ?, ?, ?)";
         // Using plain text password for now (CHANGE THIS!), should use hashed_password
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_password, $param_role, $param_nama_lengkap);
            
            $param_username = $username;
            $param_password = $password; // CHANGE THIS to $hashed_password
            $param_role = $role;
            $param_nama_lengkap = $nama_lengkap;
            
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "User baru berhasil ditambahkan.";
                // Clear the form after successful submission
                $username = $password = $role = $nama_lengkap = '';
            } else {
                $error_msg = "Terjadi kesalahan saat menambahkan user: " . mysqli_error($link);
            }

            mysqli_stmt_close($stmt);
        }
         else {
             $error_msg = "Terjadi kesalahan saat menyiapkan statement: " . mysqli_error($link);
         }
    }
     else {
         $error_msg = "Mohon perbaiki kesalahan pada form.";
     }
    
    // Close connection - only close if it was successfully opened
    if (isset($link)) {
        mysqli_close($link);
    }

}
// Close connection if it was opened but form was not submitted
else if (isset($link)) {
    mysqli_close($link);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tambah User</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">

    <!-- Sidebar -->
    <aside class="sidebar w-64 bg-gray-800 text-gray-300 flex flex-col h-screen fixed top-0 left-0">
        <h2 class="text-center text-white text-xl font-semibold py-4 mb-6 border-b border-gray-700">Admin Panel</h2>
        <ul class="flex-grow">
            <li class="px-6 py-3 border-b border-gray-700 hover:bg-gray-700"><a href="dashboard.php" class="block text-gray-300 hover:text-white">Dashboard</a></li>
            <li class="px-6 py-3 border-b border-gray-700 bg-blue-600 text-white"><a href="manage_users.php" class="block text-white">Manajemen User</a></li>
            <li class="px-6 py-3 border-b border-gray-700 hover:bg-gray-700"><a href="approval.php" class="block text-gray-300 hover:text-white">Approval Kegiatan</a></li>
            <li class="px-6 py-3 border-b border-gray-700 hover:bg-gray-700"><a href="#">Laporan</a></li>
        </ul>
        <a href="logout.php" class="block px-6 py-4 bg-red-600 text-white text-center hover:bg-red-700 transition duration-200 mt-auto">Logout</a>
    </aside>

    <!-- Main Content -->
    <main class="main-content flex-grow ml-64 p-8 bg-gray-100 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah User Baru</h1>

        <?php if (!empty($success_msg)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                 <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-8 rounded-lg shadow-xl max-w-xl mx-auto">
            <h3 class="text-2xl font-bold text-gray-800 pb-4 mb-6 border-b border-gray-200">Masukkan Detail User</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                
                <div class="mb-6">
                    <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" class="form-input w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <?php if (!empty($username_err)): ?>
                        <p class="text-red-500 text-xs italic mt-1"><?php echo $username_err; ?></p>
                    <?php endif; ?>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" class="form-input w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <?php if (!empty($password_err)): ?>
                        <p class="text-red-500 text-xs italic mt-1"><?php echo $password_err; ?></p>
                    <?php endif; ?>
                </div>

                <div class="mb-6">
                    <label for="role" class="block text-gray-700 text-sm font-semibold mb-2">Role</label>
                    <select id="role" name="role" class="form-select w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" <?php echo ($role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?php echo ($role == 'user') ? 'selected' : ''; ?>>User</option>
                        <!-- Add other roles as needed -->
                    </select>
                    <?php if (!empty($role_err)): ?>
                        <p class="text-red-500 text-xs italic mt-1"><?php echo $role_err; ?></p>
                    <?php endif; ?>
                </div>

                 <div class="mb-6">
                    <label for="nama_lengkap" class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($nama_lengkap); ?>" class="form-input w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <?php if (!empty($nama_lengkap_err)): ?>
                        <p class="text-red-500 text-xs italic mt-1"><?php echo $nama_lengkap_err; ?></p>
                    <?php endif; ?>
                 </div>

                <div class="flex items-center justify-start">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-md focus:outline-none focus:shadow-outline transition duration-200">
                        Tambah User
                    </button>
                </div>
            </form>
        </div>

    </main>

</body>
</html> 