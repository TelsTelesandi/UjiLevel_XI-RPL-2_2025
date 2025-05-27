<?php
// Start a session
session_start();

// Include database connection file
require_once '../db_connect.php'; // Note the '../' to go up one directory

$username = $password = "";
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if (empty(trim($_POST["username"]))) {
        $error = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $error = "Please enter your password.";
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
                         if ($password === $hashed_password) { // <-- **GANTI INI DENGAN password_verify()**
                             // Password is correct, now check role
                             if ($role === 'admin') {
                                // Password and role are correct, start a new session
                                session_regenerate_id();

                                $_SESSION['loggedin'] = true;
                                $_SESSION['user_id'] = $user_id;
                                $_SESSION['username'] = $db_username; // Use username from DB
                                $_SESSION['role'] = $role;

                                // Redirect to admin dashboard page
                                header("location: dashboard.php");
                                exit;
                            } else {
                                // User found, but role is not 'admin'
                                $error = "You do not have admin access. Please use the correct login page.";
                            }
                        } else {
                            // Password is not valid
                            $error = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Oops! Something went wrong. Please try again later.";
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
    <title>Admin Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-white to-red-600">
    <div class="bg-white p-10 rounded-lg shadow-xl text-center max-w-sm w-full">
        <img src="../telk-removebg-preview.png" alt="Logo Telesandi" class="mx-auto h-20 mb-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Admin Login</h2>
         <?php if (!empty($error)): ?>
            <p class="text-red-500 mb-4 text-sm"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="post">
             <div class="mb-5 relative">
                <label for="username" class="sr-only">Username</label>
                 <!-- Using Font Awesome classes for actual icons -->
                 <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"><i class="fas fa-user"></i></span>
                <input type="text" id="username" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-gray-700">
            </div>
            <div class="mb-6 relative">
                <label for="password" class="sr-only">Password</label>
                 <!-- Using Font Awesome classes for actual icons -->
                 <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"><i class="fas fa-lock"></i></span>
                <input type="password" id="password" name="password" placeholder="Password" required class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-gray-700">
            </div>
            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 transition duration-200 mb-4">Login</button>
        </form>
        <a href="../login.php" class="text-blue-600 hover:underline text-sm">User Login</a>
    </div>
</body>
</html> 