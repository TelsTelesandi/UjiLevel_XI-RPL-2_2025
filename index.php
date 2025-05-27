<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Aplikasi Ekskul</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero {
            text-align: center;
            color: white;
            padding: 2rem;
            max-width: 800px;
        }

        .hero-content {
            background: rgba(255, 255, 255, 0.1);
            padding: 3rem;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .cta-btn {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(90deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid white;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .cta-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="hero-content">
            <h1>Selamat Datang di Aplikasi Ekskul</h1>
            <p>Platform manajemen kegiatan ekstrakurikuler yang memudahkan pengajuan dan pengelolaan event dengan efisien.</p>
            <div class="cta-buttons">
                <a href="auth/login.php" class="cta-btn btn-primary">Login</a>
                <a href="auth/register.php" class="cta-btn btn-secondary">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</body>
</html>