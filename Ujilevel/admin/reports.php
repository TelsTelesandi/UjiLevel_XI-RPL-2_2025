<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Get filter parameters
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$status = $_GET['status'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if ($start_date) {
    $where_conditions[] = "ep.tanggal_pengajuan >= :start_date";
    $params[':start_date'] = $start_date;
}

if ($end_date) {
    $where_conditions[] = "ep.tanggal_pengajuan <= :end_date";
    $params[':end_date'] = $end_date;
}

if ($status) {
    $where_conditions[] = "ep.status = :status";
    $params[':status'] = $status;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

$query = "SELECT ep.*, u.nama_lengkap, u.ekskul 
          FROM event_pengajuan ep 
          JOIN users u ON ep.user_id = u.user_id 
          $where_clause 
          ORDER BY ep.tanggal_pengajuan DESC";

$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate PDF if requested
if (isset($_GET['generate_pdf'])) {
    require_once '../vendor/tcpdf/tcpdf.php';
    
    // Konfigurasi TCPDF
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Sistem Pengajuan Event');
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Laporan Pengajuan Event');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    
    // Add page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Title
    $pdf->Cell(0, 10, 'LAPORAN PENGAJUAN EVENT', 0, 1, 'C');
    if ($start_date && $end_date) {
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Periode: ' . date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date)), 0, 1, 'C');
    }
    $pdf->Ln(5);
    
    // Table
    $pdf->SetFont('helvetica', '', 10);
    $html = '<table border="1" cellpadding="4">
                <tr style="background-color:#f0f0f0; font-weight:bold;">
                    <th width="5%">No</th>
                    <th width="25%">Judul Event</th>
                    <th width="15%">Pengaju</th>
                    <th width="15%">Jenis Kegiatan</th>
                    <th width="15%">Total Biaya</th>
                    <th width="15%">Tanggal</th>
                    <th width="10%">Status</th>
                </tr>';
    
    $no = 1;
    foreach ($events as $event) {
        $status_color = '';
        switch($event['status']) {
            case 'disetujui': $status_color = 'color:green;'; break;
            case 'ditolak': $status_color = 'color:red;'; break;
            case 'selesai': $status_color = 'color:blue;'; break;
            default: $status_color = 'color:orange;';
        }
        
        $html .= '<tr>
                    <td align="center">' . $no++ . '</td>
                    <td>' . htmlspecialchars($event['judul_event']) . '</td>
                    <td>' . htmlspecialchars($event['nama_lengkap']) . '</td>
                    <td>' . htmlspecialchars($event['jenis_kegiatan']) . '</td>
                    <td align="right">Rp ' . number_format($event['total_pembiayaan'], 0, ',', '.') . '</td>
                    <td align="center">' . date('d/m/Y', strtotime($event['tanggal_pengajuan'])) . '</td>
                    <td align="center" style="' . $status_color . '">' . ucfirst($event['status']) . '</td>
                  </tr>';
    }
    
    $html .= '</table>';
    
    // Print table
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Output PDF
    $pdf->Output('laporan_event_' . date('Y-m-d') . '.pdf', 'D');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Pengajuan Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .main-content {
            min-height: 100vh;
            background-color: #f5f6fa;
        }
        .nav-link {
            color: white;
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="p-3">
                    <h4 class="text-white mb-4">Admin Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_events.php">
                                <i class="fas fa-calendar me-2"></i>Kelola Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_users.php">
                                <i class="fas fa-users me-2"></i>Kelola User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="reports.php">
                                <i class="fas fa-file-alt me-2"></i>Laporan
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-danger" href="../includes/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-auto main-content">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Laporan Pengajuan Event</h2>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['generate_pdf' => '1'])); ?>" 
                           class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i>Download PDF
                        </a>
                    </div>
                    
                    <!-- Filter Form -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?php echo htmlspecialchars($start_date); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?php echo htmlspecialchars($end_date); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="menunggu" <?php echo $status === 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                        <option value="disetujui" <?php echo $status === 'disetujui' ? 'selected' : ''; ?>>Disetujui</option>
                                        <option value="ditolak" <?php echo $status === 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter me-2"></i>Filter
                                    </button>
                                    <a href="reports.php" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Results -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Data Pengajuan Event (<?php echo count($events); ?> data)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Event</th>
                                            <th>Pengaju</th>
                                            <th>Ekstrakurikuler</th>
                                            <th>Jenis Kegiatan</th>
                                            <th>Total Biaya</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        foreach ($events as $event): 
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                            <td><?php echo htmlspecialchars($event['nama_lengkap']); ?></td>
                                            <td><?php echo htmlspecialchars($event['ekskul']); ?></td>
                                            <td><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                            <td>Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                switch($event['status']) {
                                                    case 'menunggu': $badge_class = 'bg-warning'; break;
                                                    case 'disetujui': $badge_class = 'bg-success'; break;
                                                    case 'ditolak': $badge_class = 'bg-danger'; break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($event['status']); ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (empty($events)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data yang ditemukan</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
