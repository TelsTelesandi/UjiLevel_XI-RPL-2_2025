<?php
session_start();

require 'database/config.php';

$error_message = '';
$success_message = '';
$input_username = '';

// Tampilkan pesan sukses logout jika ada
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success_message = 'Anda berhasil logout.';
}

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_username = trim($_POST['username'] ?? '');
    $input_password = $_POST['password'] ?? '';
    
    if (empty($input_username) || empty($input_password)) {
        $error_message = 'Username dan password harus diisi!';
    } else {
        // Bersihkan input
        $username = mysqli_real_escape_string($conn, $input_username);
        
        // Query yang sudah diperbaiki (tanpa pengecekan status)
        $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // Verifikasi password
            if (password_verify($input_password, $user['password']) || 
                $user['password'] === $input_password) {
                
                // Set session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['ekskul'] = $user['ekskul'];
                
                // Redirect
                header('Location: ' . ($user['role'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
                exit();
            } else {
                $error_message = 'Username atau password salah!';
            }
        } else {
            $error_message = 'Username atau password salah!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Event Ekstrakurikuler</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        .bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .floating-shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-shape:nth-child(1) {
            width: 5rem;
            height: 5rem;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-shape:nth-child(2) {
            width: 7.5rem;
            height: 7.5rem;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .floating-shape:nth-child(3) {
            width: 3.75rem;
            height: 3.75rem;
            bottom: 30%;
            left: 20%;
            animation-delay: 4s;
        }

        .floating-shape:nth-child(4) {
            width: 6.25rem;
            height: 6.25rem;
            top: 10%;
            right: 30%;
            animation-delay: 1s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
            50% { transform: translateY(-1.25rem) rotate(5deg); opacity: 0.3; }
        }

        .login-container {
            display: flex;
            width: 90%;
            max-width: 1000px;
            min-height: 31.25rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 1.25rem;
            box-shadow: 0 0.9375rem 2.1875rem rgba(0, 0, 0, 0.2);
            overflow: hidden;
            z-index: 1;
            position: relative;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .login-logo {
            background: rgba(255, 255, 255, 0.2);
            width: 5rem;
            height: 5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(5px);
        }

        .login-logo svg {
            width: 2.5rem;
            height: 2.5rem;
            fill: white;
        }

        .login-left h2 {
            font-size: clamp(1.5rem, 4vw, 1.8rem);
            margin-bottom: 0.8rem;
            font-weight: 700;
        }

        .login-left p {
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            max-width: 18rem;
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        }

        .login-illustration {
            width: 100%;
            max-width: 15rem;
            opacity: 0.9;
        }

        .login-right {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-form {
            width: 100%;
            max-width: 22rem;
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-header h1 {
            font-size: clamp(1.8rem, 5vw, 2rem);
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #6c757d;
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #495057;
            font-weight: 600;
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 0.1875rem rgba(102, 126, 234, 0.1);
        }

        .password-container {
            position: relative;
            width: 100%;
        }

        .toggle-password {
            position: absolute;
            right: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.2rem;
        }

        .eye-icon {
            width: 1.2rem;
            height: 1.2rem;
            fill: #6c757d;
            transition: all 0.3s ease;
        }

        .eye-icon.hidden {
            fill: #667eea;
        }

        .eye-icon path.slash {
            display: none;
        }

        .eye-icon.hidden path.slash {
            display: block;
        }

        .btn-login {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            min-height: 2.75rem;
        }

        .btn-login:hover {
            transform: translateY(-0.125rem);
            box-shadow: 0 0.3125rem 0.9375rem rgba(102, 126, 234, 0.4);
        }

        .btn-back {
            position: absolute;
            top: 1rem;
            left: 1rem;
            width: 2.5rem;
            height: 2.5rem;
            background: #ffffff;
            border: none;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .btn-back:hover {
            transform: translateY(-0.125rem);
            box-shadow: 0 0.3125rem 0.9375rem rgba(102, 126, 234, 0.4);
        }

        .btn-back svg {
            width: 1.5rem;
            height: 1.5rem;
            stroke: #764ba2;
        }

        .error-message {
            background: #ff6b6b;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.2rem;
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
            word-wrap: break-word;
        }

        .success-message {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            padding: 0.8rem 1rem;
            border-radius: 0.6rem;
            margin-bottom: 1.5rem;
            font-size: clamp(0.8rem, 2.5vw, 0.95rem);
            box-shadow: 0 0.3125rem 0.9375rem rgba(67, 233, 123, 0.18);
            text-align: center;
            animation: fadeIn 0.7s;
            word-wrap: break-word;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-0.625rem); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                margin: 1rem;
                min-height: auto;
            }

            .login-left, .login-right {
                padding: 1.5rem;
            }

            .login-left {
                order: 2;
            }

            .login-right {
                order: 1;
            }

            .login-logo {
                width: 4rem;
                height: 4rem;
            }

            .login-logo svg {
                width: 2rem;
                height: 2rem;
            }

            .login-illustration {
                max-width: 12rem;
            }

            .btn-back {
                top: 0.75rem;
                left: 0.75rem;
                width: 2rem;
                height: 2rem;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .btn-back svg {
                width: 1.2rem;
                height: 1.2rem;
                stroke: white;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
            }

            .login-container {
                margin: 0.5rem;
                border-radius: 0.75rem;
            }

            .login-left, .login-right {
                padding: 1rem;
            }

            .login-form {
                max-width: 100%;
            }

            .login-header h1 {
                font-size: clamp(1.5rem, 5vw, 1.8rem);
            }

            .form-control, .btn-login {
                padding: 0.6rem;
                font-size: clamp(0.8rem, 2.5vw, 0.9rem);
                min-height: 2.5rem;
            }

            .toggle-password {
                right: 0.6rem;
            }

            .eye-icon {
                width: 1rem;
                height: 1rem;
            }

            .floating-shape {
                display: none;
            }

            .login-logo {
                width: 3.5rem;
                height: 3.5rem;
            }

            .login-logo svg {
                width: 1.75rem;
                height: 1.75rem;
            }

            .login-illustration {
                max-width: 10rem;
            }

            .error-message, .success-message {
                padding: 0.5rem;
                font-size: clamp(0.7rem, 2.5vw, 0.8rem);
            }

            .btn-back {
                top: 0.5rem;
                left: 0.5rem;
                width: 1.8rem;
                height: 1.8rem;
            }

            .btn-back svg {
                width: 1rem;
                height: 1rem;
            }
        }

        @media (max-width: 320px) {
            .login-container {
                margin: 0.25rem;
                border-radius: 0.5rem;
            }

            .login-left, .login-right {
                padding: 0.75rem;
            }

            .login-form {
                padding: 0 0.5rem;
            }

            .login-header h1 {
                font-size: clamp(1.2rem, 5vw, 1.4rem);
            }

            .form-control, .btn-login {
                padding: 0.5rem;
                font-size: clamp(0.7rem, 2.5vw, 0.8rem);
                min-height: 2.25rem;
            }

            .login-logo {
                width: 3rem;
                height: 3rem;
            }

            .login-logo svg {
                width: 1.5rem;
                height: 1.5rem;
            }

            .login-illustration {
                max-width: 8rem;
            }

            .error-message, .success-message {
                padding: 0.4rem;
                font-size: clamp(0.65rem, 2.5vw, 0.75rem);
            }

            .btn-back {
                top: 0.4rem;
                left: 0.4rem;
                width: 1.5rem;
                height: 1.5rem;
            }

            .btn-back svg {
                width: 0.9rem;
                height: 0.9rem;
            }
        }

        @media (min-width: 1200px) {
            .login-container {
                max-width: 1200px;
            }

            .login-left h2 {
                font-size: 2.2rem;
            }

            .login-left p {
                font-size: 1rem;
                max-width: 24rem;
            }

            .login-illustration {
                max-width: 18rem;
            }

            .login-form {
                max-width: 28rem;
            }

            .btn-back {
                top: 1.25rem;
                left: 1.25rem;
                width: 3rem;
                height: 3rem;
            }

            .btn-back svg {
                width: 1.8rem;
                height: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>

    <div class="login-container">
        <a href="welcome.php" class="btn-back">
            <svg viewBox="0 0 24 24">
                <path d="M15 18l-6-6 6-6" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <div class="login-left">
            <div class="login-logo">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2L2 7V10C2 16 6 21.5 12 22C18 21.5 22 16 22 10V7L12 2Z"/>
                </svg>
            </div>
            <h2>Selamat Datang!</h2>
            <p>Bergabunglah dengan sistem manajemen event ekstrakurikuler yang modern dan efisien</p>
            <div class="login-illustration">
                <svg viewBox="0 0 400 300">
                    <rect x="50" y="50" width="300" height="200" rx="20" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
                    <circle cx="200" cy="120" r="30" fill="rgba(255,255,255,0.3)"/>
                    <rect x="120" y="170" width="160" height="8" rx="4" fill="rgba(255,255,255,0.3)"/>
                    <rect x="140" y="190" width="120" height="6" rx="3" fill="rgba(255,255,255,0.2)"/>
                    <rect x="160" y="210" width="80" height="6" rx="3" fill="rgba(255,255,255,0.2)"/>
                </svg>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-form">
                <div class="login-header">
                    <h1>LOGIN</h1>
                    <p>Masuk ke akun Anda</p>
                </div>

                <?php if ($success_message): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" 
                               placeholder="Masukkan username" required
                               value="<?= htmlspecialchars($input_username) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" class="form-control" 
                                   placeholder="Masukkan password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <svg viewBox="0 0 24 24" class="eye-icon">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">Masuk Sekarang</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.querySelector('.eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeIcon.innerHTML = `
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    <path class="slash" d="M3 3l18 18"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeIcon.innerHTML = `
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                `;
            }
        }
    </script>
</body>
</html> 