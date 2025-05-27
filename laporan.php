<?php
// Mock data - replace with actual database queries in production
$applicationStats = [
    'total' => 5,
    'pending' => 5, // Semua diubah menjadi pending
    'approved' => 0,
    'rejected' => 0
];

$userStats = [
    'total' => 5,
    'admin' => 1,
    'regularUsers' => 4
];

// Current page - this would be set based on which menu item is clicked
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Handle form submission for closing request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'close_request' && isset($_POST['event_id'])) {
    $eventId = (int)$_POST['event_id'];
    foreach ($pengajuanData as &$data) {
        if ($data['event_id'] === $eventId && $data['status'] === 'disetujui') {
            $data['status'] = 'selesai';
            $applicationStats['approved']--;
            break;
        }
    }
    unset($data); // Unset reference to avoid issues
}

// Available extracurricular options
$ekstrakurikulerOptions = [
    'Futsal',
    'Sepak Bola',
    'Basket',
    'Voli',
    'Paduan Suara',
    'Pramuka',
    'PMR',
    'Seni Tari',
    'Teater'
];

// Available activity types
$jenisKegiatanOptions = [
    'Olahraga',
    'Seni',
    'Akademik',
    'Sosial',
    'Keagamaan'
];

// Sample data with all status set to 'menunggu'
$pengajuanData = [
    [
        'event_id' => 1,
        'user_id' => 1,
        'judul_event' => 'Turnamen Basket Antar Sekolah',
        'jenis_kegiatan' => 'Olahraga',
        'total_pembiayaan' => 1500000,
        'proposal' => 'turnamen_basket.pdf',
        'deskripsi' => 'Mengikuti turnamen basket tingkat kota',
        'tanggal_pengajuan' => '2025-05-01',
        'status' => 'menunggu'
    ],
    [
        'event_id' => 2,
        'user_id' => 2,
        'judul_event' => 'Festival Paduan Suara',
        'jenis_kegiatan' => 'Seni',
        'total_pembiayaan' => 2000000,
        'proposal' => 'festival_paduan_suara.pdf',
        'deskripsi' => 'Mengikuti lomba paduan suara di provinsi',
        'tanggal_pengajuan' => '2025-05-03',
        'status' => 'menunggu'
    ],
    [
        'event_id' => 3,
        'user_id' => 3,
        'judul_event' => 'Liga Futsal Sekolah',
        'jenis_kegiatan' => 'Olahraga',
        'total_pembiayaan' => 1800000,
        'proposal' => 'liga_futsal.pdf',
        'deskripsi' => 'Pertandingan liga futsal antar sekolah',
        'tanggal_pengajuan' => '2025-05-05',
        'status' => 'menunggu'
    ],
    [
        'event_id' => 4,
        'user_id' => 1,
        'judul_event' => 'Latihan Intensif Basket',
        'jenis_kegiatan' => 'Olahraga',
        'total_pembiayaan' => 1000000,
        'proposal' => 'latihan_basket.pdf',
        'deskripsi' => 'Pelatihan khusus menjelang turnamen',
        'tanggal_pengajuan' => '2025-05-08',
        'status' => 'menunggu'
    ],
    [
        'event_id' => 5,
        'user_id' => 2,
        'judul_event' => 'Pentas Seni Akhir Tahun',
        'jenis_kegiatan' => 'Seni',
        'total_pembiayaan' => 2500000,
        'proposal' => 'pentas_seni.pdf',
        'deskripsi' => 'Pertunjukan seni dari ekskul paduan suara',
        'tanggal_pengajuan' => '2025-05-10',
        'status' => 'menunggu'
    ]
];

// Sample data from the screenshot
$penggunaData = [
    [
        'user_id' => 1,
        'username' => 'dzaki',
        'password' => '1',
        'nama_lengkap' => 'Muhammad dzaki',
        'role' => 'Admin',
        'ekskul' => ''
    ],
    [
        'user_id' => 2,
        'username' => 'Jamal',
        'password' => '1',
        'nama_lengkap' => 'Jamal Ikhsan',
        'role' => 'user',
        'ekskul' => 'Futsal'
    ],
    [
        'user_id' => 3,
        'username' => 'Andi',
        'password' => '1',
        'nama_lengkap' => 'Andi Saputra',
        'role' => 'user',
        'ekskul' => 'Futsal'
    ],
    [
        'user_id' => 4,
        'username' => 'Salman',
        'password' => '1',
        'nama_lengkap' => 'Salman AL',
        'role' => 'user',
        'ekskul' => 'Futsal'
    ],
    [
        'user_id' => 5,
        'username' => 'Raflyadi',
        'password' => '1',
        'nama_lengkap' => 'Rafly Adi',
        'role' => 'user',
        'ekskul' => 'Sepak Bola'
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 250px;
            background-color: #1e3a8a;
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 100;
        }
        .sidebar-title {
            font-size: 24px;
            font-weight: bold;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-menu {
            list-style: none;
        }
        .sidebar-menu li {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sidebar-menu a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 15px 20px;
            transition: background-color 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            flex: 1;
            background-color: #f0f4f8;
            padding: 20px;
            margin-left: 250px;
            height: 100vh;
            overflow-y: auto;
        }
        .page-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #1e293b;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .stats-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stats-section {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 10px;
        }
        .section-detail-button {
            background-color: #1e40af;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .section-detail-button:hover {
            background-color: #1e3a8a;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 15px;
        }
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .stat-title {
            font-size: 14px;
            font-weight: 500;
            color: #475569;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
            color: #1e293b;
        }
        .stat-description {
            font-size: 12px;
            color: #64748b;
        }
        .help-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            z-index: 100;
        }
        .pending-icon {
            color: #f59e0b;
        }
        .approved-icon {
            color: #10b981;
        }
        .rejected-icon {
            color: #ef4444;
        }
        .admin-icon {
            color: #8b5cf6;
        }
        .user-icon {
            color: #0ea5e9;
        }
        .detail-content {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .back-button {
            color: #1e40af;
            text-decoration: none;
            font-weight: 500;
        }
        .back-button:hover {
            text-decoration: underline;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .role-admin {
            background-color: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
        }
        .role-user {
            background-color: rgba(14, 165, 233, 0.2);
            color: #0ea5e9;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .action-button {
            background-color: #f1f5f9;
            color: #64748b;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.2s;
        }
        .action-button:hover {
            background-color: #e2e8f0;
        }
        .action-button.edit {
            color: #f59e0b;
        }
        .action-button.delete {
            color: #ef4444;
        }
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .add-button {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }
        .add-button:hover {
            background-color: #059669;
        }
        .add-event-button {
            background-color: #7c3aed;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }
        .add-event-button:hover {
            background-color: #6d28d9;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .detail-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .detail-table td {
            padding: 12px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-table tr:last-child td {
            border-bottom: none;
        }
        .detail-footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-span {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-menunggu {
            background-color: #f59e0b20;
            color: #f59e0b;
        }
        .status-disetujui {
            background-color: #10b98120;
            color: #10b981;
        }
        .status-ditolak {
            background-color: #ef444420;
            color: #ef4444;
        }
        .status-selesai {
            background-color: #6b728020;
            color: #6b7280;
        }
        .close-button {
            background-color: #6b7280;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.2s;
        }
        .close-button:hover {
            background-color: #4b5563;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">Admin Panel</div>
        <ul class="sidebar-menu">
            <li><a href="?page=dashboard" class="<?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="?page=users" class="<?php echo $currentPage == 'users' ? 'active' : ''; ?>">Managemen Users</a></li>
            <li><a href="?page=approval" class="<?php echo $currentPage == 'approval' ? 'active' : ''; ?>">Approval Kegiatan</a></li>
            <li><a href="?page=reports" class="<?php echo $currentPage == 'reports' ? 'active' : ''; ?>">Laporan</a></li>
            <li><a href="login.php?logout=1" class="<?php echo $currentPage == 'login' ? 'active' : ''; ?>">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php if ($currentPage == 'dashboard'): ?>
            <h1 class="page-title">Dashboard</h1>
            
            <div class="stats-container">
                <!-- Application Statistics -->
                <div class="stats-section">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-file-alt"></i> Statistik Pengajuan
                        </div>
                        <a href="#pengajuanDetailSection" class="section-detail-button">Lihat Detail</a>
                    </div>
                    <div class="stats-grid">
                        <!-- Total Pengajuan -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Total Pengajuan</div>
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-value" id="totalApplications"><?php echo $applicationStats['total']; ?></div>
                            <div class="stat-description">Semua pengajuan dalam sistem</div>
                        </div>

                        <!-- Pending -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Pending</div>
                                <i class="fas fa-clock pending-icon"></i>
                            </div>
                            <div class="stat-value" id="pendingApplications"><?php echo $applicationStats['pending']; ?></div>
                            <div class="stat-description">Menunggu persetujuan</div>
                        </div>

                        <!-- Approved -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Approved</div>
                                <i class="fas fa-check-circle approved-icon"></i>
                            </div>
                            <div class="stat-value" id="approvedApplications"><?php echo $applicationStats['approved']; ?></div>
                            <div class="stat-description">Pengajuan disetujui</div>
                        </div>

                        <!-- Rejected -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Rejected</div>
                                <i class="fas fa-times-circle rejected-icon"></i>
                            </div>
                            <div class="stat-value" id="rejectedApplications"><?php echo $applicationStats['rejected']; ?></div>
                            <div class="stat-description">Pengajuan ditolak</div>
                        </div>
                    </div>
                </div>

                <!-- User Statistics -->
                <div class="stats-section">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-users"></i> Statistik Pengguna
                        </div>
                        <a href="#penggunaDetailSection" class="section-detail-button">Lihat Detail</a>
                    </div>
                    <div class="stats-grid">
                        <!-- Total Pengguna -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Total Pengguna</div>
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-value" id="totalUsers"><?php echo $userStats['total']; ?></div>
                            <div class="stat-description">Semua pengguna terdaftar</div>
                        </div>

                        <!-- Admin -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Admin</div>
                                <i class="fas fa-user-cog admin-icon"></i>
                            </div>
                            <div class="stat-value" id="adminUsers"><?php echo $userStats['admin']; ?></div>
                            <div class="stat-description">Pengguna dengan hak admin</div>
                        </div>

                        <!-- Regular Users -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Regular Users</div>
                                <i class="fas fa-user user-icon"></i>
                            </div>
                            <div class="stat-value" id="regularUsers"><?php echo $userStats['regularUsers']; ?></div>
                            <div class="stat-description">Pengguna standar</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pengajuan Detail Section -->
            <div id="pengajuanDetailSection" class="dashboard-detail-section">
                <div class="detail-content">
                    <div class="detail-header">
                        <div>
                            <h3 style="color: #1e40af; margin-bottom: 10px;">Data Pengajuan Kegiatan</h3>
                            <p style="color: #64748b; font-size: 14px;">Berikut adalah detail semua pengajuan kegiatan yang ada dalam sistem</p>
                        </div>
                        <button class="add-event-button" id="openEventModalBtn">
                            <i class="fas fa-plus"></i> Tambah Event
                        </button>
                    </div>
                    
                    <div style="overflow-x: auto;">
                        <table class="detail-table" id="eventTable">
                            <thead>
                                <tr>
                                    <th>Event ID</th>
                                    <th>User ID</th>
                                    <th>Judul Event</th>
                                    <th>Jenis Kegiatan</th>
                                    <th>Total Pembiayaan</th>
                                    <th>Proposal</th>
                                    <th>Deskripsi</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengajuanData as $data): ?>
                                <tr data-event-id="<?php echo $data['event_id']; ?>">
                                    <td><?php echo $data['event_id']; ?></td>
                                    <td><?php echo $data['user_id']; ?></td>
                                    <td style="font-weight: 500;"><?php echo $data['judul_event']; ?></td>
                                    <td><?php echo $data['jenis_kegiatan']; ?></td>
                                    <td>Rp <?php echo number_format($data['total_pembiayaan'], 0, ',', '.'); ?></td>
                                    <td style="color: #1e40af;"><?php echo $data['proposal']; ?></td>
                                    <td style="max-width: 200px;"><?php echo $data['deskripsi']; ?></td>
                                    <td><?php echo $data['tanggal_pengajuan']; ?></td>
                                    <td>
                                        <span class="status-span status-<?php echo $data['status']; ?>">
                                            <?php 
                                            $statusText = '';
                                            switch($data['status']) {
                                                case 'menunggu':
                                                    $statusText = 'Menunggu';
                                                    break;
                                                case 'disetujui':
                                                    $statusText = 'Disetujui';
                                                    break;
                                                case 'ditolak':
                                                    $statusText = 'Ditolak';
                                                    break;
                                                case 'selesai':
                                                    $statusText = 'Selesai';
                                                    break;
                                            }
                                            echo $statusText;
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="detail-footer">
                        <a href="#" class="back-button" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                            <i class="fas fa-arrow-up"></i> Kembali ke Atas
                        </a>
                        <div style="color: #64748b; font-size: 14px;">
                            Total: <span id="totalEventCount"><?php echo count($pengajuanData); ?></span> pengajuan
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pengguna Detail Section -->
            <div id="penggunaDetailSection" class="dashboard-detail-section">
                <div class="detail-content">
                    <div class="detail-header">
                        <div>
                            <h3 style="color: #1e40af; margin-bottom: 10px;">Data Pengguna</h3>
                            <p style="color: #64748b; font-size: 14px;">Berikut adalah detail semua pengguna yang terdaftar dalam sistem</p>
                        </div>
                        <button class="add-button" id="openModalBtn">
                            <i class="fas fa-plus"></i> Tambah User Baru
                        </button>
                    </div>
                    
                    <div style="overflow-x: auto;">
                        <table class="detail-table" id="userTable">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Nama Lengkap</th>
                                    <th>Role</th>
                                    <th>Ekskul</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($penggunaData as $data): ?>
                                <tr>
                                    <td><?php echo $data['user_id']; ?></td>
                                    <td><?php echo $data['username']; ?></td>
                                    <td><?php echo $data['password']; ?></td>
                                    <td style="font-weight: 500;"><?php echo $data['nama_lengkap']; ?></td>
                                    <td>
                                        <span class="role-badge <?php echo $data['role'] == 'Admin' ? 'role-admin' : 'role-user'; ?>">
                                            <?php echo $data['role']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $data['ekskul']; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-button edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="action-button">
                                                <i class="fas fa-copy"></i> Copy
                                            </button>
                                            <button class="action-button delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="detail-footer">
                        <a href="#" class="back-button" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                            <i class="fas fa-arrow-up"></i> Kembali ke Atas
                        </a>
                        <div style="color: #64748b; font-size: 14px;">
                            Total: <span id="totalUserCount"><?php echo count($penggunaData); ?></span> pengguna
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add Event Modal -->
            <!-- [Modal content remains the same, but not functional without JavaScript] -->
            
            <!-- Add User Modal -->
            <!-- [Modal content remains the same, but not functional without JavaScript] -->
            
        <?php elseif ($currentPage == 'approval'): ?>
            <h1 class="page-title">Approval Kegiatan</h1>
            <div class="detail-content">
                <div class="detail-header">
                    <div>
                        <h3 style="color: #1e40af; margin-bottom: 10px;">Data Pengajuan Kegiatan</h3>
                        <p style="color: #64748b; font-size: 14px;">Berikut adalah detail semua pengajuan kegiatan yang ada dalam sistem</p>
                    </div>
                    <button class="add-event-button" id="openEventModalBtn">
                        <i class="fas fa-plus"></i> Tambah Event
                    </button>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="detail-table" id="eventTable">
                        <thead>
                            <tr>
                                <th>Event ID</th>
                                <th>User ID</th>
                                <th>Judul Event</th>
                                <th>Jenis Kegiatan</th>
                                <th>Total Pembiayaan</th>
                                <th>Proposal</th>
                                <th>Deskripsi</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pengajuanData as $data): ?>
                            <tr data-event-id="<?php echo $data['event_id']; ?>">
                                <td><?php echo $data['event_id']; ?></td>
                                <td><?php echo $data['user_id']; ?></td>
                                <td style="font-weight: 500;"><?php echo $data['judul_event']; ?></td>
                                <td><?php echo $data['jenis_kegiatan']; ?></td>
                                <td>Rp <?php echo number_format($data['total_pembiayaan'], 0, ',', '.'); ?></td>
                                <td style="color: #1e40af;"><?php echo $data['proposal']; ?></td>
                                <td style="max-width: 200px;"><?php echo $data['deskripsi']; ?></td>
                                <td><?php echo $data['tanggal_pengajuan']; ?></td>
                                <td>
                                    <span class="status-span status-<?php echo $data['status']; ?>">
                                        <?php 
                                        $statusText = '';
                                        switch($data['status']) {
                                            case 'menunggu':
                                                $statusText = 'Menunggu';
                                                break;
                                            case 'disetujui':
                                                $statusText = 'Disetujui';
                                                break;
                                            case 'ditolak':
                                                $statusText = 'Ditolak';
                                                break;
                                            case 'selesai':
                                                $statusText = 'Selesai';
                                                break;
                                        }
                                        echo $statusText;
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($data['status'] == 'menunggu'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?php echo $data['event_id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="action-button" style="background-color: #10b981; color: white;">Setuju</button>
                                        </form>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?php echo $data['event_id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="action-button" style="background-color: #ef4444; color: white;">Tolak</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="detail-footer">
                    <a href="?page=dashboard" class="back-button">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <div style="color: #64748b; font-size: 14px;">
                        Total: <span id="totalEventCount"><?php echo count($pengajuanData); ?></span> pengajuan
                    </div>
                </div>
            </div>
        <?php elseif ($currentPage == 'users'): ?>
            <h1 class="page-title">Managemen Users</h1>
            <div class="detail-content">
                <div class="detail-header">
                    <div>
                        <h3 style="color: #1e40af; margin-bottom: 10px;">Data Pengguna</h3>
                        <p style="color: #64748b; font-size: 14px;">Berikut adalah detail semua pengguna yang terdaftar dalam sistem</p>
                    </div>
                    <button class="add-button" id="openModalBtn">
                        <i class="fas fa-plus"></i> Tambah User Baru
                    </button>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="detail-table" id="userTable">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Ekskul</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($penggunaData as $data): ?>
                            <tr>
                                <td><?php echo $data['user_id']; ?></td>
                                <td><?php echo $data['username']; ?></td>
                                <td><?php echo $data['password']; ?></td>
                                <td style="font-weight: 500;"><?php echo $data['nama_lengkap']; ?></td>
                                <td>
                                    <span class="role-badge <?php echo $data['role'] == 'Admin' ? 'role-admin' : 'role-user'; ?>">
                                        <?php echo $data['role']; ?>
                                    </span>
                                </td>
                                <td><?php echo $data['ekskul']; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-button edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="action-button delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="detail-footer">
                    <a href="?page=dashboard" class="back-button">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <div style="color: #64748b; font-size: 14px;">
                        Total: <span id="totalUserCount"><?php echo count($penggunaData); ?></span> pengguna
                    </div>
                </div>
            </div>
        <?php elseif ($currentPage == 'reports'): ?>
            <h1 class="page-title">Laporan</h1>
            <div class="detail-content">
                <div class="detail-header">
                    <div>
                        <h3 style="color: #1e40af; margin-bottom: 10px;">Laporan Pengajuan Kegiatan</h3>
                        <p style="color: #64748b; font-size: 14px;">Berikut adalah laporan semua pengajuan kegiatan beserta statusnya</p>
                    </div>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="detail-table" id="reportTable">
                        <thead>
                            <tr>
                                <th>Event ID</th>
                                <th>User ID</th>
                                <th>Judul Event</th>
                                <th>Jenis Kegiatan</th>
                                <th>Total Pembiayaan</th>
                                <th>Proposal</th>
                                <th>Deskripsi</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pengajuanData as $data): ?>
                            <tr data-event-id="<?php echo $data['event_id']; ?>">
                                <td><?php echo $data['event_id']; ?></td>
                                <td><?php echo $data['user_id']; ?></td>
                                <td style="font-weight: 500;"><?php echo $data['judul_event']; ?></td>
                                <td><?php echo $data['jenis_kegiatan']; ?></td>
                                <td>Rp <?php echo number_format($data['total_pembiayaan'], 0, ',', '.'); ?></td>
                                <td style="color: #1e40af;"><?php echo $data['proposal']; ?></td>
                                <td style="max-width: 200px;"><?php echo $data['deskripsi']; ?></td>
                                <td><?php echo $data['tanggal_pengajuan']; ?></td>
                                <td>
                                    <span class="status-span status-<?php echo $data['status']; ?>">
                                        <?php 
                                        $statusText = '';
                                        switch($data['status']) {
                                            case 'menunggu':
                                                $statusText = 'Menunggu';
                                                break;
                                            case 'disetujui':
                                                $statusText = 'Disetujui';
                                                break;
                                            case 'ditolak':
                                                $statusText = 'Ditolak';
                                                break;
                                            case 'selesai':
                                                $statusText = 'Selesai';
                                                break;
                                        }
                                        echo $statusText;
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($data['status'] == 'disetujui'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?php echo $data['event_id']; ?>">
                                            <input type="hidden" name="action" value="close_request">
                                            <button type="submit" class="close-button">Close Request</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="detail-footer">
                    <a href="?page=dashboard" class="back-button">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <div style="color: #64748b; font-size: 14px;">
                        Total: <span id="totalReportCount"><?php echo count($pengajuanData); ?></span> pengajuan
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="help-button">
        <i class="fas fa-question"></i>
    </div>
</body>
</html>