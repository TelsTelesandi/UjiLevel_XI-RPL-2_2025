<?php
// Start a session
session_start();

// Include database connection file
require_once 'db_connect.php';

$username = $password = "";
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if (empty(trim($_POST["username"]))) {
        $error = "Silakan masukkan username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $error = "Silakan masukkan password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // If there are no errors, attempt to login
    if (empty($error)) {
        // Prepare a select statement, checking only username
        $sql = "SELECT user_id, username, password, role FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username); // 's' for string, only one parameter

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password and role
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $user_id, $db_username, $hashed_password, $role);
                    if (mysqli_stmt_fetch($stmt)) {
                        // --- !! SECURITY RISK !! ---
                        // In a real application, use password_verify() with hashed passwords.
                        // Example: if (password_verify($password, $hashed_password)) {
                        // For now, simulating plain text comparison (CHANGE THIS!)
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, now check role
                            if ($role === 'user') {
                                // Password and role are correct, start a new session
                                session_regenerate_id();

                                $_SESSION['loggedin'] = true;
                                $_SESSION['user_id'] = $user_id;
                                $_SESSION['username'] = $db_username; // Use username from DB
                                $_SESSION['role'] = $role;

                                // Redirect to user dashboard page
                                header("location: dashboard.php");
                                exit;
                            } else {
                                // User found, but role is not 'user'
                                $error = "You do not have user access. Please use the correct login page.";
                            }
                        } else {
                            // Password is not valid
                            $error = "Username atau password salah.";
                        }
                    }
                } else {
                    // Username doesn't exist
                    $error = "Username atau password salah.";
                }
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
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
    <title>User Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-white to-blue-500">
    <div class="bg-white p-10 rounded-lg shadow-xl text-center max-w-sm w-full">
        <img src="telk-removebg-preview.png" alt="Logo Telesandi" class="mx-auto h-20 mb-6">
        <h2 class="text-2xl font-bold mb-6 text-blue-700">User Login</h2>
        <?php if (!empty($error)): ?>
            <p class="text-red-500 mb-4 text-sm"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="mb-5 relative">
                <label for="username" class="sr-only">Username</label>
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"><i class="fas fa-user"></i></span>
                <input type="text" id="username" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-700">
            </div>
            <div class="mb-6 relative">
                <label for="password" class="sr-only">Password</label>
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"><i class="fas fa-lock"></i></span>
                <input type="password" id="password" name="password" placeholder="Password" required class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-700">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 mb-4">Login</button>
        </form>
        <a href="admin/login.php" class="text-blue-600 hover:underline text-sm">Admin Login</a>
    </div>
</body>
</html> 