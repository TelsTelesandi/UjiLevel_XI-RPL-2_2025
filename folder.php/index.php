<?php
session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan Event Ekstrakurikuler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">EkstraEvent</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Ajukan Event</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Riwayat</a></li>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item"><a class="nav-link" href="#">Admin Panel</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="bg-light py-5 text-center">
        <div class="container">
            <h1 class="display-5">Ajukan Kegiatan Ekstrakurikulermu</h1>
            <p class="lead">Mudah. Cepat. Transparan.</p>
        </div>
    </header>

    <main class="container my-5">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <a href="#" class="btn btn-outline-primary w-100 p-4">
                    <h4>📝 Ajukan Event Baru</h4>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="btn btn-outline-success w-100 p-4">
                    <h4>📊 Status Pengajuan</h4>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="btn btn-outline-secondary w-100 p-4">
                    <h4>🗂️ Riwayat Event</h4>
                </a>
            </div>
            <?php if ($isAdmin): ?>
                <div class="col-md-4 offset-md-4 mt-4">
                    <a href="#" class="btn btn-outline-danger w-100 p-4">
                        <h4>🛠️ Panel Admin</h4>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <hr class="my-5">

        <div class="alert alert-info text-center">
            <strong>Info:</strong> Pengajuan event hanya dibuka hingga tanggal 30 setiap bulan. Hubungi pembina OSIS jika mengalami kendala.
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3">
        <small>&copy; <?= date('Y'); ?> SMAN Contoh | OSIS & Ekstrakurikuler</small>
    </footer>

</body>
</html>
