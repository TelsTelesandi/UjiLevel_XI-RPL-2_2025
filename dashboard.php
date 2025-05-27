<?php
// Mock data - replace with actual database queries in production
$applicationStats = [
    'total' => 5,
    'pending' => 5,
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
            cursor: pointer;
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
            cursor: pointer;
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
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: background-color 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .action-button:hover {
            background-color: #e2e8f0;
            transform: translateY(-1px);
        }
        .action-button:active {
            transform: translateY(0);
        }
        .action-button.edit {
            background-color: #f59e0b;
            color: white;
        }
        .action-button.edit:hover {
            background-color: #d97706;
        }
        .action-button.delete {
            background-color: #ef4444;
            color: white;
        }
        .action-button.delete:hover {
            background-color: #dc2626;
        }
        .action-button.setuju-button {
            background-color: #10b981;
            color: white;
        }
        .action-button.setuju-button:hover {
            background-color: #059669;
        }
        .action-button.tolak-button {
            background-color: #ef4444;
            color: white;
        }
        .action-button.tolak-button:hover {
            background-color: #dc2626;
        }
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .add-button, .add-event-button {
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
            transition: background-color 0.2s, transform 0.1s;
        }
        .add-button:hover, .add-event-button:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }
        .add-button:active, .add-event-button:active {
            transform: translateY(0);
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow: auto;
            padding: 20px;
        }
        .modal-content {
            background-color: white;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .modal-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
        }
        .close-button {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #64748b;
            transition: color 0.2s;
        }
        .close-button:hover {
            color: #1e40af;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        .form-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.1);
        }
        .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background-color: white;
            transition: border-color 0.2s;
        }
        .form-select:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.1);
        }
        .form-textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            min-height: 80px;
            resize: vertical;
            transition: border-color 0.2s;
        }
        .form-textarea:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.1);
        }
        .form-file {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background-color: white;
            transition: border-color 0.2s;
        }
        .form-file:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.1);
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        .cancel-button {
            background-color: #f1f5f9;
            color: #64748b;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }
        .cancel-button:hover {
            background-color: #e2e8f0;
            transform: translateY(-1px);
        }
        .cancel-button:active {
            transform: translateY(0);
        }
        .submit-button {
            background-color: #1e40af;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }
        .submit-button:hover {
            background-color: #1e3a8a;
            transform: translateY(-1px);
        }
        .submit-button:active {
            transform: translateY(0);
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
        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 2000;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
        }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .toast i {
            font-size: 18px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .file-info {
            font-size: 12px;
            color: #64748b;
            margin-top: 5px;
        }
        .dashboard-detail-section {
            margin-top: 30px;
        }
        /* Add new styles for status display */
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

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage">User berhasil ditambahkan!</span>
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
                                            <button class="action-button edit" onclick="editUser(<?php echo $data['user_id']; ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="action-button delete" onclick="deleteUser(<?php echo $data['user_id']; ?>)">
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
            <div id="addEventModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Tambah Event Baru</h3>
                        <button class="close-button" id="closeEventModalBtn">×</button>
                    </div>
                    <form id="addEventForm">
                        <div class="form-group">
                            <label for="judul_event" class="form-label">Judul Event</label>
                            <input type="text" id="judul_event" name="judul_event" class="form-input" required placeholder="Masukkan judul event">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="jenis_kegiatan" class="form-label">Jenis Kegiatan</label>
                                <select id="jenis_kegiatan" name="jenis_kegiatan" class="form-select" required>
                                    <option value="">Pilih Jenis Kegiatan</option>
                                    <?php foreach ($jenisKegiatanOptions as $jenis): ?>
                                        <option value="<?php echo $jenis; ?>"><?php echo $jenis; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="total_pembiayaan" class="form-label">Total Pembiayaan</label>
                                <input type="number" id="total_pembiayaan" name="total_pembiayaan" class="form-input" required placeholder="0" min="0">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="proposal_file" class="form-label">Upload Proposal</label>
                            <input type="file" id="proposal_file" name="proposal_file" class="form-file" accept=".pdf,.doc,.docx" required>
                            <div class="file-info">Format yang diizinkan: PDF, DOC, DOCX (Maksimal 5MB)</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-textarea" required placeholder="Jelaskan detail kegiatan yang akan dilaksanakan"></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="cancel-button" id="cancelEventBtn">Batal</button>
                            <button type="submit" class="submit-button" id="saveEventBtn">Ajukan Event</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Add/Edit User Modal -->
            <div id="addUserModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalTitle">Tambah User Baru</h3>
                        <button class="close-button" id="closeModalBtn">×</button>
                    </div>
                    <form id="addUserForm">
                        <input type="hidden" id="editUserId" name="editUserId">
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role" class="form-label">Role</label>
                            <select id="role" name="role" class="form-select" required>
                                <option value="">Pilih Role</option>
                                <option value="user">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="ekskul" class="form-label">Ekstrakurikuler</label>
                            <select id="ekskul" name="ekskul" class="form-select">
                                <option value="">Pilih Ekstrakurikuler</option>
                                <?php foreach ($ekstrakurikulerOptions as $ekskul): ?>
                                    <option value="<?php echo $ekskul; ?>"><?php echo $ekskul; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="cancel-button" id="cancelBtn">Batal</button>
                            <button type="submit" class="submit-button" id="saveUserBtn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
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
                                        }
                                        echo $statusText;
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($data['status'] == 'menunggu'): ?>
                                        <button class="action-button setuju-button" onclick="approveEvent(<?php echo $data['event_id']; ?>)">
                                            <i class="fas fa-check"></i> Setuju
                                        </button>
                                        <button class="action-button tolak-button" onclick="rejectEvent(<?php echo $data['event_id']; ?>)">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
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
                                        <button class="action-button edit" onclick="editUser(<?php echo $data['user_id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="action-button delete" onclick="deleteUser(<?php echo $data['user_id']; ?>)">
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
            <!-- Reports content would go here -->
        <?php endif; ?>
    </div>

    <div class="help-button">
        <i class="fas fa-question"></i>
    </div>

    <script>
        // Store data in JavaScript
        let userData = <?php echo json_encode($penggunaData ?? []); ?>;
        let userStats = <?php echo json_encode($userStats ?? []); ?>;
        let eventData = <?php echo json_encode($pengajuanData ?? []); ?>;
        let applicationStats = <?php echo json_encode($applicationStats ?? []); ?>;
        
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // User modal elements
            const userModal = document.getElementById('addUserModal');
            const openUserModalBtns = document.querySelectorAll('#openModalBtn');
            const closeUserModalBtn = document.getElementById('closeModalBtn');
            const cancelUserBtn = document.getElementById('cancelBtn');
            const addUserForm = document.getElementById('addUserForm');
            const modalTitle = document.getElementById('modalTitle');
            
            // Event modal elements
            const eventModal = document.getElementById('addEventModal');
            const openEventModalBtns = document.querySelectorAll('#openEventModalBtn');
            const closeEventModalBtn = document.getElementById('closeEventModalBtn');
            const cancelEventBtn = document.getElementById('cancelEventBtn');
            const addEventForm = document.getElementById('addEventForm');
            
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');

            // Debugging: Check if elements are found
            console.log('User Modal:', userModal);
            console.log('Open User Modal Buttons:', openUserModalBtns);
            console.log('Event Modal:', eventModal);
            console.log('Open Event Modal Buttons:', openEventModalBtns);

            // User modal functionality
            openUserModalBtns.forEach(btn => {
                if (btn && userModal) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Open User Modal Button Clicked');
                        modalTitle.textContent = 'Tambah User Baru';
                        addUserForm.reset();
                        document.getElementById('editUserId').value = '';
                        userModal.style.display = 'block';
                    });
                }
            });
            
            if (closeUserModalBtn && userModal) {
                closeUserModalBtn.addEventListener('click', function() {
                    console.log('Close User Modal Button Clicked');
                    userModal.style.display = 'none';
                });
            }
            
            if (cancelUserBtn && userModal) {
                cancelUserBtn.addEventListener('click', function() {
                    console.log('Cancel User Modal Button Clicked');
                    userModal.style.display = 'none';
                });
            }
            
            // Event modal functionality
            openEventModalBtns.forEach(btn => {
                if (btn && eventModal) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Open Event Modal Button Clicked');
                        eventModal.style.display = 'block';
                    });
                }
            });
            
            if (closeEventModalBtn && eventModal) {
                closeEventModalBtn.addEventListener('click', function() {
                    console.log('Close Event Modal Button Clicked');
                    eventModal.style.display = 'none';
                });
            }
            
            if (cancelEventBtn && eventModal) {
                cancelEventBtn.addEventListener('click', function() {
                    console.log('Cancel Event Modal Button Clicked');
                    eventModal.style.display = 'none';
                });
            }
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === userModal) {
                    console.log('Clicked Outside User Modal');
                    userModal.style.display = 'none';
                }
                if (event.target === eventModal) {
                    console.log('Clicked Outside Event Modal');
                    eventModal.style.display = 'none';
                }
            });
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
            
            // Handle user form submission (for both add and edit)
            if (addUserForm) {
                addUserForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('User Form Submitted');
                    
                    const userId = document.getElementById('editUserId').value;
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    const namaLengkap = document.getElementById('nama_lengkap').value;
                    const role = document.getElementById('role').value;
                    const ekskul = document.getElementById('ekskul').value;
                    
                    if (!username || !password || !namaLengkap || !role) {
                        alert('Semua field harus diisi kecuali Ekstrakurikuler');
                        return;
                    }
                    
                    if (userId) {
                        // Edit existing user
                        const userIndex = userData.findIndex(user => user.user_id == userId);
                        if (userIndex !== -1) {
                            const oldRole = userData[userIndex].role;
                            userData[userIndex] = {
                                user_id: parseInt(userId),
                                username: username,
                                password: password,
                                nama_lengkap: namaLengkap,
                                role: role,
                                ekskul: ekskul
                            };
                            
                            if (oldRole !== role) {
                                if (oldRole === 'Admin') {
                                    userStats.admin--;
                                    userStats.regularUsers++;
                                } else {
                                    userStats.admin++;
                                    userStats.regularUsers--;
                                }
                            }
                            showToast('User berhasil diupdate!');
                        }
                    } else {
                        // Add new user
                        const newUser = {
                            user_id: userData.length + 1,
                            username: username,
                            password: password,
                            nama_lengkap: namaLengkap,
                            role: role,
                            ekskul: ekskul
                        };
                        userData.push(newUser);
                        if (role === 'Admin') {
                            userStats.admin++;
                        } else {
                            userStats.regularUsers++;
                        }
                        userStats.total++;
                        showToast('User berhasil ditambahkan!');
                    }
                    
                    updateUserTable();
                    updateDashboardStats();
                    addUserForm.reset();
                    userModal.style.display = 'none';
                });
            }
            
            // Handle event form submission
            if (addEventForm) {
                addEventForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Event Form Submitted');
                    
                    const judulEvent = document.getElementById('judul_event').value;
                    const jenisKegiatan = document.getElementById('jenis_kegiatan').value;
                    const totalPembiayaan = document.getElementById('total_pembiayaan').value;
                    const deskripsi = document.getElementById('deskripsi').value;
                    const proposalFile = document.getElementById('proposal_file').files[0];
                    
                    if (!judulEvent || !jenisKegiatan || !totalPembiayaan || !deskripsi || !proposalFile) {
                        alert('Semua field harus diisi');
                        return;
                    }
                    
                    if (proposalFile.size > 5 * 1024 * 1024) {
                        alert('Ukuran file tidak boleh lebih dari 5MB');
                        return;
                    }
                    
                    const newEvent = {
                        event_id: eventData.length + 1,
                        user_id: 1,
                        judul_event: judulEvent,
                        jenis_kegiatan: jenisKegiatan,
                        total_pembiayaan: parseInt(totalPembiayaan),
                        proposal: proposalFile.name,
                        deskripsi: deskripsi,
                        tanggal_pengajuan: new Date().toISOString().split('T')[0],
                        status: 'menunggu'
                    };
                    eventData.push(newEvent);
                    applicationStats.total++;
                    applicationStats.pending++;
                    updateEventTable();
                    updateApplicationStats();
                    showToast('Event berhasil diajukan! Menunggu persetujuan admin.');
                    addEventForm.reset();
                    eventModal.style.display = 'none';
                });
            }
            
            // Function to update user table
            function updateUserTable() {
                const userTables = document.querySelectorAll('#userTable');
                userTables.forEach(userTable => {
                    if (!userTable) return;
                    const tbody = userTable.querySelector('tbody');
                    tbody.innerHTML = '';
                    userData.forEach(user => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${user.user_id}</td>
                            <td>${user.username}</td>
                            <td>${user.password}</td>
                            <td style="font-weight: 500;">${user.nama_lengkap}</td>
                            <td>
                                <span class="role-badge ${user.role === 'Admin' ? 'role-admin' : 'role-user'}">
                                    ${user.role}
                                </span>
                            </td>
                            <td>${user.ekskul}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-button edit" onclick="editUser(${user.user_id})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="action-button delete" onclick="deleteUser(${user.user_id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                });
                const totalUserCounts = document.querySelectorAll('#totalUserCount');
                totalUserCounts.forEach(totalUserCount => {
                    if (totalUserCount) totalUserCount.textContent = userData.length;
                });
            }
            
            // Function to update event table
            function updateEventTable() {
                const eventTables = document.querySelectorAll('#eventTable');
                eventTables.forEach(eventTable => {
                    if (!eventTable) return;
                    const tbody = eventTable.querySelector('tbody');
                    tbody.innerHTML = '';
                    eventData.forEach(event => {
                        let statusClass = '';
                        let statusText = '';
                        switch(event.status) {
                            case 'menunggu': statusClass = 'status-menunggu'; statusText = 'Menunggu'; break;
                            case 'disetujui': statusClass = 'status-disetujui'; statusText = 'Disetujui'; break;
                            case 'ditolak': statusClass = 'status-ditolak'; statusText = 'Ditolak'; break;
                        }
                        const tr = document.createElement('tr');
                        tr.setAttribute('data-event-id', event.event_id);
                        tr.innerHTML = `
                            <td>${event.event_id}</td>
                            <td>${event.user_id}</td>
                            <td style="font-weight: 500;">${event.judul_event}</td>
                            <td>${event.jenis_kegiatan}</td>
                            <td>Rp ${event.total_pembiayaan.toLocaleString('id-ID')}</td>
                            <td style="color: #1e40af;">${event.proposal}</td>
                            <td style="max-width: 200px;">${event.deskripsi}</td>
                            <td>${event.tanggal_pengajuan}</td>
                            <td>
                                <span class="status-span ${statusClass}">
                                    ${statusText}
                                </span>
                            </td>
                            <td>
                                ${event.status === 'menunggu' ? `
                                    <button class="action-button setuju-button" onclick="approveEvent(${event.event_id})">
                                        <i class="fas fa-check"></i> Setuju
                                    </button>
                                    <button class="action-button tolak-button" onclick="rejectEvent(${event.event_id})">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                ` : ''}
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                });
                const totalEventCounts = document.querySelectorAll('#totalEventCount');
                totalEventCounts.forEach(totalEventCount => {
                    if (totalEventCount) totalEventCount.textContent = eventData.length;
                });
            }
            
            // Function to update dashboard stats
            function updateDashboardStats() {
                const totalUsersElements = document.querySelectorAll('#totalUsers');
                const adminUsersElements = document.querySelectorAll('#adminUsers');
                const regularUsersElements = document.querySelectorAll('#regularUsers');
                totalUsersElements.forEach(element => { if (element) element.textContent = userStats.total; });
                adminUsersElements.forEach(element => { if (element) element.textContent = userStats.admin; });
                regularUsersElements.forEach(element => { if (element) element.textContent = userStats.regularUsers; });
            }
            
            // Function to update application stats
            function updateApplicationStats() {
                const totalApplicationsElements = document.querySelectorAll('#totalApplications');
                const pendingApplicationsElements = document.querySelectorAll('#pendingApplications');
                const approvedApplicationsElements = document.querySelectorAll('#approvedApplications');
                const rejectedApplicationsElements = document.querySelectorAll('#rejectedApplications');
                totalApplicationsElements.forEach(element => { if (element) element.textContent = applicationStats.total; });
                pendingApplicationsElements.forEach(element => { if (element) element.textContent = applicationStats.pending; });
                approvedApplicationsElements.forEach(element => { if (element) element.textContent = applicationStats.approved; });
                rejectedApplicationsElements.forEach(element => { if (element) element.textContent = applicationStats.rejected; });
            }
            
            // Function to edit user
            window.editUser = function(userId) {
                console.log('Edit User:', userId);
                const user = userData.find(u => u.user_id == userId);
                if (user) {
                    modalTitle.textContent = 'Edit User';
                    document.getElementById('editUserId').value = user.user_id;
                    document.getElementById('username').value = user.username;
                    document.getElementById('password').value = user.password;
                    document.getElementById('nama_lengkap').value = user.nama_lengkap;
                    document.getElementById('role').value = user.role;
                    document.getElementById('ekskul').value = user.ekskul;
                    userModal.style.display = 'block';
                }
            }

            // Function to delete user
            window.deleteUser = function(userId) {
                console.log('Delete User:', userId);
                if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                    const userIndex = userData.findIndex(user => user.user_id == userId);
                    if (userIndex !== -1) {
                        const userRole = userData[userIndex].role;
                        userData.splice(userIndex, 1);
                        if (userRole === 'Admin') {
                            userStats.admin--;
                        } else {
                            userStats.regularUsers--;
                        }
                        userStats.total--;
                        updateUserTable();
                        updateDashboardStats();
                        showToast('User berhasil dihapus!');
                    }
                }
            }
            
            // Function to approve an event
            window.approveEvent = function(eventId) {
                console.log('Approve Event:', eventId);
                const eventIndex = eventData.findIndex(event => event.event_id === eventId);
                if (eventIndex !== -1 && eventData[eventIndex].status === 'menunggu') {
                    eventData[eventIndex].status = 'disetujui';
                    applicationStats.pending--;
                    applicationStats.approved++;
                    updateEventTable();
                    updateApplicationStats();
                    showToast('Pengajuan berhasil disetujui!');
                }
            }

            // Function to reject an event
            window.rejectEvent = function(eventId) {
                console.log('Reject Event:', eventId);
                const eventIndex = eventData.findIndex(event => event.event_id === eventId);
                if (eventIndex !== -1 && eventData[eventIndex].status === 'menunggu') {
                    eventData[eventIndex].status = 'ditolak';
                    applicationStats.pending--;
                    applicationStats.rejected++;
                    updateEventTable();
                    updateApplicationStats();
                    showToast('Pengajuan berhasil ditolak!');
                }
            }
            
            // Function to show toast notification
            function showToast(message) {
                console.log('Showing Toast:', message);
                toastMessage.textContent = message;
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }
        });
    </script>
</body>
</html>