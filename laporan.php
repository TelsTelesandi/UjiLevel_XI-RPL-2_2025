<?php
ob_start();
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'vendor/autoload.php'; // For TCPDF

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Generate PDF Report
if (isset($_GET['generate_pdf'])) {
    // Get data for PDF
    $query = "SELECT ep.*, u.nama_lengkap FROM event_pengajuan ep
              JOIN users u ON ep.user_id = u.user_id
              ORDER BY ep.tanggal_pengajuan DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // PDF generation code starts here...
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Event Management System');
    $pdf->SetTitle('Laporan Pengajuan Event');
    $pdf->SetSubject('Laporan Event');

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 15, 'LAPORAN PENGAJUAN EVENT', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 10, 'Tanggal: ' . date('d/m/Y'), 0, 1, 'R');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(40, 7, 'Pengaju', 1, 0, 'C');
    $pdf->Cell(50, 7, 'Judul Event', 1, 0, 'C');
    $pdf->Cell(30, 7, 'Jenis Kegiatan', 1, 0, 'C');
    $pdf->Cell(25, 7, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(20, 7, 'Status', 1, 1, 'C');

    // Table content
    $pdf->SetFont('helvetica', '', 7);
    foreach ($submissions as $submission) {
        $pdf->Cell(40, 6, $submission['nama_lengkap'], 1, 0, 'L');
        $pdf->Cell(50, 6, substr($submission['judul_event'], 0, 30), 1, 0, 'L');
        $pdf->Cell(30, 6, $submission['jenis_kegiatan'], 1, 0, 'L');
        $pdf->Cell(25, 6, date('d/m/Y', strtotime($submission['tanggal_pengajuan'])), 1, 0, 'C');
        $pdf->Cell(20, 6, ucfirst($submission['status']), 1, 1, 'C');
    }

    ob_end_clean();
    $pdf->Output('laporan_event_' . date('Y-m-d') . '.pdf', 'D');
    exit();
}

// Get statistics for report
$stats_query = "SELECT 
    COUNT(*) as total_pengajuan,
    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak,
    SUM(CASE WHEN status = 'disetujui' THEN CAST(Total_pembiayaan AS UNSIGNED) ELSE 0 END) as total_biaya_disetujui
    FROM event_pengajuan";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get monthly statistics
$monthly_query = "SELECT 
    MONTH(tanggal_pengajuan) as bulan,
    YEAR(tanggal_pengajuan) as tahun,
    COUNT(*) as jumlah
    FROM event_pengajuan 
    WHERE YEAR(tanggal_pengajuan) = YEAR(CURDATE())
    GROUP BY YEAR(tanggal_pengajuan), MONTH(tanggal_pengajuan)
    ORDER BY tahun, bulan";
$monthly_stmt = $db->prepare($monthly_query);
$monthly_stmt->execute();
$monthly_stats = $monthly_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Event Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center text-white mb-4">
                        <i class="fas fa-user-shield fa-3x"></i>
                        <h5 class="mt-2"><?php echo $_SESSION['nama_lengkap']; ?></h5>
                        <small>Administrator</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="admin_dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="users.php">
                                <i class="fas fa-users"></i> Kelola User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="admin_pengajuan.php">
                                <i class="fas fa-file-alt"></i> Kelola Pengajuan 
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="verifikasi.php">
                                <i class="fas fa-check-circle"></i> Verifikasi Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="laporan.php">
                                <i class="fas fa-file-pdf"></i> Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="./logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Laporan Event</h1>
                    <a href="?generate_pdf=1" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Generate PDF
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                                <h3 class="text-primary"><?php echo $stats['total_pengajuan']; ?></h3>
                                <p class="text-muted">Total Pengajuan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h3 class="text-success"><?php echo $stats['disetujui']; ?></h3>
                                <p class="text-muted">Disetujui</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                <h3 class="text-danger"><?php echo $stats['ditolak']; ?></h3>
                                <p class="text-muted">Ditolak</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-money-bill fa-2x text-warning mb-2"></i>
                                <h6 class="text-warning">Rp <?php echo number_format($stats['total_biaya_disetujui'] ?? 0, 0, ',', '.'); ?></h6>
                                <p class="text-muted">Total Biaya Disetujui</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Status Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Status Pengajuan</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Pengajuan per Bulan (<?php echo date('Y'); ?>)</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Disetujui', 'Ditolak'],
                datasets: [{
                    data: [<?php echo $stats['menunggu']; ?>, <?php echo $stats['disetujui']; ?>, <?php echo $stats['ditolak']; ?>],
                    backgroundColor: ['#ffc107', '#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    for ($i = 1; $i <= 12; $i++) {
                        echo "'" . $months[$i-1] . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: [
                        <?php 
                        for ($i = 1; $i <= 12; $i++) {
                            $found = false;
                            foreach ($monthly_stats as $stat) {
                                if ($stat['bulan'] == $i) {
                                    echo $stat['jumlah'] . ',';
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) echo '0,';
                        }
                        ?>
                    ],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>