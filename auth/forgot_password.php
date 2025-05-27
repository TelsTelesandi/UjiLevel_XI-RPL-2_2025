<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Email harus diisi!";
    } else {
        // Check if email exists in database
        $query = "SELECT user_id, username FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token in database
            $update_query = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE user_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "ssi", $reset_token, $expiry, $user['user_id']);
            
            if (mysqli_stmt_execute($update_stmt)) {
                // Send reset email
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/auth/reset_password.php?token=" . $reset_token;
                $to = $email;
                $subject = "Reset Password";
                $message = "Halo " . $user['username'] . ",\n\n";
                $message .= "Klik link berikut untuk reset password Anda:\n";
                $message .= $reset_link . "\n\n";
                $message .= "Link ini akan kadaluarsa dalam 1 jam.\n";
                $message .= "Jika Anda tidak meminta reset password, abaikan email ini.\n";
                
                $headers = "From: noreply@example.com";
                
                if (mail($to, $subject, $message, $headers)) {
                    $success = "Instruksi reset password telah dikirim ke email Anda.";
                } else {
                    $error = "Gagal mengirim email reset password.";
                }
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
            mysqli_stmt_close($update_stmt);
        } else {
            $error = "Email tidak ditemukan.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password - Aplikasi Ekskul</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button:hover {
            background: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        .back-link a {
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lupa Password</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php else: ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit">Kirim Link Reset Password</button>
        </form>
        <?php endif; ?>
        <div class="back-link">
            <a href="login.php">Kembali ke Login</a>
        </div>
    </div>

    <script>
    document.querySelector('form')?.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        
        if (!email) {
            e.preventDefault();
            alert('Email harus diisi!');
            return false;
        }
    });
    </script>
</body>
</html> 