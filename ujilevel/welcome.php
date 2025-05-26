<?php
include 'database/config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Pengajuan addEventListener</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.1;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            animation: drift 20s linear infinite;
        }

        @keyframes drift {
            0% { transform: rotate(0deg) translate(-50px, -50px); }
            100% { transform: rotate(360deg) translate(-50px, -50px); }
        }

        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 3rem;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .header-title {
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .header-actions {
            display: flex;
            gap: 0.8rem;
            align-items: center;
        }

        .header-btn {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 8px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .header-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .header-btn:hover::before {
            left: 100%;
        }

        .header-btn:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.15);
        }

        .header-btn.primary {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border: 1px solid rgba(255, 107, 107, 0.3);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.2);
        }

        .header-btn.primary:hover {
            background: linear-gradient(135deg, #ff5252 0%, #d84315 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
            margin-top: 60px;
        }

        .hero-container {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .welcome-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 2rem;
            display: inline-block;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .welcome-title {
            font-size: 4.5rem;
            color: white;
            font-weight: 800;
            letter-spacing: -2px;
            margin-bottom: 1rem;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease-out;
        }

        .welcome-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
            margin-bottom: 1rem;
            line-height: 1.6;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .welcome-description {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
            margin-bottom: 3rem;
            line-height: 1.8;
            max-width: 600px;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            width: 100%;
            animation: fadeInUp 1s ease-out 0.6s both;
        }

        .btn-main {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 15px 35px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
            position: relative;
            overflow: hidden;
            display: inline-block;
            transform: translateY(0);
            width: auto;
        }

        .btn-main::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-main:hover::before {
            left: 100%;
        }

        .btn-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
        }

        /* Features Section */
        .features {
            display: flex;
            gap: 3rem;
            justify-content: center;
            padding: 2rem 1rem;
            opacity: 0.8;
            flex-wrap: wrap;
        }

        .feature-item {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            max-width: 100px;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .feature-text {
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                padding: 1rem 1.5rem;
            }

            .header-brand {
                gap: 8px;
            }

            .header-logo {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .header-title {
                font-size: 1.1rem;
            }

            .header-actions {
                gap: 0.5rem;
            }

            .header-btn {
                padding: 6px 16px;
                font-size: 0.85rem;
            }

            .welcome-title {
                font-size: 3rem;
                letter-spacing: -1px;
            }

            .welcome-subtitle {
                font-size: 1.1rem;
            }

            .welcome-description {
                font-size: 1rem;
                margin-bottom: 2rem;
            }

            .cta-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-main {
                max-width: 300px;
                width: 90%; /* Mengurangi lebar tombol agar tidak menempel di pinggir */
                margin: 0 auto; /* Memastikan tombol berada di tengah dengan margin di sisi */
                transform: translateY(0);
                animation: none;
            }

            .features {
                gap: 1.5rem;
                padding: 1rem;
            }

            .feature-item {
                max-width: 80px;
            }

            .feature-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .feature-text {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 1rem;
                margin-top: 50px;
            }

            .header {
                padding: 0.8rem 1rem;
            }

            .header-logo {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }

            .header-title {
                font-size: 1rem;
            }

            .header-actions {
                gap: 0.3rem;
            }

            .header-btn {
                padding: 5px 12px;
                font-size: 0.75rem;
            }

            .welcome-title {
                font-size: 2.5rem;
            }

            .welcome-subtitle {
                font-size: 1rem;
            }

            .welcome-description {
                font-size: 0.9rem;
            }

            .btn-main {
                padding: 12px 25px;
                font-size: 1rem;
                max-width: 300px;
                width: 90%; /* Mengurangi lebar tombol agar tidak menempel di pinggir */
                margin: 0 auto; /* Memastikan tombol berada di tengah dengan margin di sisi */
                transform: translateY(0);
                animation: none;
            }

            .features {
                gap: 1rem;
            }

            .feature-item {
                max-width: 70px;
            }

            .feature-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .feature-text {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>

    <!-- Header/Navbar -->
    <div class="header">
        <div class="header-brand">
            <div class="header-logo">🎓</div>
            <div class="header-title">Pengajuan Event</div>
        </div>
        <div class="header-actions">
            <a href="login.php" class="header-btn primary">LOGIN</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="hero-container">
            <div class="welcome-badge">✨ Selamat Datang</div>
            <h1 class="welcome-title">WELCOME</h1>
            <p class="welcome-subtitle">Pengajuan Event Sekolah</p>
            <p class="welcome-description">
               Wujudkan sekolah impian dengan sistem pengajuan event yang modern dan efisien!
            </p>
            <div class="cta-buttons">
                <a href="login.php" class="btn-main">GET STARTED</a>
            </div>
        </div>

        <!-- Features -->
        <div class="features">
            <div class="feature-item">
                <div class="feature-icon">🎯</div>
                <div class="feature-text">Mudah Digunakan</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">⚡</div>
                <div class="feature-text">Cepat & Efisien</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">🔒</div>
                <div class="feature-text">Aman & Terpercaya</div>
            </div>
        </div>
    </div>
</body>
</html>