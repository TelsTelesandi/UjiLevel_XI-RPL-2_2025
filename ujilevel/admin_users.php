<?php
session_start();
require 'database/config.php';

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Pesan notifikasi
$message = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        $ekskul = isset($_POST['ekskul']) ? mysqli_real_escape_string($conn, $_POST['ekskul']) : '';
        
        $check_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check_query) > 0) {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Username sudah digunakan!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } else {
            $query = "INSERT INTO users (username, password, nama_lengkap, role, ekskul) 
                      VALUES ('$username', '$password', '$nama_lengkap', '$role', '$ekskul')";
            if (mysqli_query($conn, $query)) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">User berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: '.mysqli_error($conn).'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }
    } elseif (isset($_POST['edit_user'])) {
        $user_id = intval($_POST['user_id']);
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        $ekskul = isset($_POST['ekskul']) ? mysqli_real_escape_string($conn, $_POST['ekskul']) : '';
        
        $query = "UPDATE users SET nama_lengkap = '$nama_lengkap', role = '$role', ekskul = '$ekskul' WHERE user_id = $user_id";
        if (mysqli_query($conn, $query)) {
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE users SET password = '$password' WHERE user_id = $user_id");
            }
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">User berhasil diupdate!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: '.mysqli_error($conn).'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }
}

// Handle delete user
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT role FROM users WHERE user_id = $user_id"));
    $role = $user['role'];
    $confirm_msg = "Apakah kamu yakin ingin menghapus " . ($role == 'admin' ? 'admin' : 'user') . "?";
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        $check_events = mysqli_query($conn, "SELECT * FROM event_pengajuan WHERE user_id = $user_id");
        if (mysqli_num_rows($check_events) > 0) {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">User tidak dapat dihapus karena memiliki pengajuan event!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } else {
            $query = "DELETE FROM users WHERE user_id = $user_id";
            if (mysqli_query($conn, $query)) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">User berhasil dihapus!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: '.mysqli_error($conn).'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }
    } else {
        echo "<script>
            if (confirm('$confirm_msg')) {
                window.location.href = '?delete=$user_id&confirm=yes';
            } else {
                window.location.href = 'admin_users.php';
            }
        </script>";
        exit();
    }
}

// Get user to edit
$edit_user = null;
if (isset($_GET['edit'])) {
    $user_id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
    $edit_user = mysqli_fetch_assoc($result);
}

// Ambil data user dan statistik
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY user_id DESC");
$total_users = mysqli_num_rows($users);
mysqli_data_seek($users, 0); // Reset pointer
$admins = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role = 'admin'"));
$regular_users = $total_users - $admins;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4b5aaf;
            --secondary-color: #6c757d;
            --card-bg: #ffffff;
            --text-color: #2d3748;
            --border-color: #e2e8f0;
            --gradient-bg: linear-gradient(135deg, #6B48FF, #42A5F5);
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            --hover-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --backdrop-bg: rgba(0, 0, 0, 0.5);
            --danger-color: #dc3545;
        }

        body {
            font-family: 'Inter', 'Roboto', system-ui, sans-serif;
            background: #f4f6f9;
            margin: 0;
            color: var(--text-color);
            font-size: 0.95rem;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            width: 36px;
            height: 32px;
            background: #ffffff;
            border: none;
            border-radius: 4px;
            padding: 6px;
            cursor: pointer;
            z-index: 1100;
            box-shadow: 0 0 8px rgba(107, 72, 255, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.3s ease;
        }

        .hamburger.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .hamburger:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 0 12px rgba(107, 72, 255, 0.6);
        }

        .hamburger span {
            position: absolute;
            left: 6px;
            width: calc(100% - 12px);
            height: 4px;
            background: linear-gradient(90deg, #6B48FF, #42A5F5);
            border-radius: 2px;
            transition: transform 0.3s ease;
            box-shadow: 0 0 5px rgba(107, 72, 255, 0.3);
        }

        .hamburger span:nth-child(1) { top: 6px; }
        .hamburger span:nth-child(2) { top: 14px; }
        .hamburger span:nth-child(3) { top: 22px; }

        .hamburger:hover span {
            transform: scale(1.1) rotate(5deg);
        }

        /* Layout */
        .container-fluid {
            padding: 1.5rem;
        }

        .main-content {
            margin-left: 220px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100vh;
            background: var(--card-bg);
            border-right: 1px solid var(--border-color);
            transform: translateX(0);
            transition: transform 0.3s ease;
            z-index: 900;
        }

        .sidebar-header {
            padding: 1.25rem;
            text-align: center;
            background: var(--gradient-bg);
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-header img {
            max-width: 100%;
            height: auto;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            padding: 0.75rem 1.25rem;
            transition: background 0.2s ease;
        }

        .sidebar-menu li:hover {
            background: #f1f5f9;
            border-left: 3px solid var(--primary-color);
        }

        .sidebar-menu li.active {
            background: #e0e7ff;
            border-left: 3px solid var(--primary-color);
        }

        .sidebar-menu li a {
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .sidebar-menu li i {
            margin-right: 0.75rem;
            width: 1.25rem;
            color: var(--primary-color);
            text-align: center;
        }

        /* Backdrop */
        .backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--backdrop-bg);
            z-index: 899;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .backdrop.active {
            display: block;
            opacity: 1;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--hover-shadow);
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card .card-title {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .stat-card .card-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-color);
        }

        /* Table */
        .recent-table {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .recent-table .table {
            margin-bottom: 0;
        }

        .recent-table th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.75rem;
            border-bottom: 2px solid var(--border-color);
        }

        .recent-table td {
            padding: 0.75rem;
            font-size: 0.9rem;
            vertical-align: middle;
            border-top: 1px solid var(--border-color);
        }

        .recent-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .recent-table tr:hover {
            background: #f1f5f9;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-admin { background: #c3cee9; color: var(--text-color); }
        .status-user { background: #d1fae5; color: #065f46; }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: white;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: #3b4a99;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 0.375rem;
            border: none;
            text-decoration: none;
            margin-right: 0.5rem;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .action-btn i {
            margin-right: 0.25rem;
        }

        .action-btn.edit {
            background: var(--primary-color);
            color: white;
        }

        .action-btn.edit:hover {
            background: #3b4a99;
            transform: translateY(-1px);
        }

        .action-btn.delete {
            background: var(--danger-color);
            color: white;
        }

        .action-btn.delete:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        /* Alerts */
        .alert {
            border-radius: 0.375rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            box-shadow: var(--shadow);
            animation: fadeIn 0.5s ease-in-out;
            margin-bottom: 1rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modal */
        .modal-content {
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-bottom: none;
            padding: 1rem;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .modal-body {
            padding: 1.25rem;
        }

        .modal .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #4b5563;
        }

        .modal .form-control,
        .modal .form-select {
            font-size: 0.9rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            border: 1px solid var(--border-color);
        }

        /* Typography */
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        h4 {
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        /* Responsive Styles */
        @media (max-width: 991px) {
            .main-content {
                margin-left: 0;
                padding-top: 3.5rem;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 260px;
                z-index: 901;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .hamburger {
                display: block;
            }
        }

        @media (max-width: 767px) {
            .container-fluid {
                padding: 1rem;
            }

            h2 {
                font-size: 1.3rem;
            }

            h4 {
                font-size: 1rem;
            }

            .stat-card {
                padding: 0.75rem;
            }

            .stat-card .card-value {
                font-size: 1.5rem;
            }

            .recent-table th,
            .recent-table td {
                font-size: 0.85rem;
                padding: 0.5rem;
            }

            .action-btn {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
                margin-right: 0.25rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .btn-primary,
            .btn-secondary {
                font-size: 0.85rem;
                padding: 0.375rem 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .stat-card {
                margin-bottom: 0.75rem;
            }

            .hamburger {
                width: 32px;
                height: 28px;
                padding: 5px;
            }

            .hamburger span {
                height: 3px;
                left: 5px;
                width: calc(100% - 10px);
            }

            .hamburger span:nth-child(1) { top: 5px; }
            .hamburger span:nth-child(2) { top: 12px; }
            .hamburger span:nth-child(3) { top: 19px; }

            .action-btn {
                display: block;
                margin: 0.25rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- Backdrop -->
    <div class="backdrop"></div>

    <!-- Sidebar -->
    <div class="sidebar" aria-label="Navigasi utama">
        <div class="sidebar-header">
            <img src="resource/logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li>
                <a href="admin_event.php"><i class="fas fa-check-circle"></i> Approval Kegiatan</a>
            </li>
            <li class="active">
                <a href="admin_users.php"><i class="fas fa-users"></i> Manajemen User</a>
            </li>
            <li>
                <a href="admin_reports.php"><i class="fas fa-file-alt"></i> Laporan</a>
            </li>
            <li>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Hamburger Menu -->
        <button class="hamburger" aria-label="Buka menu navigasi">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="container-fluid">
            <h2>Manajemen User</h2>
            
            <?php echo $message; ?>
            
            <!-- Statistik -->
            <div class="card">
                <div class="card-body">
                    <h4>Statistik Pengguna</h4>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="stat-card">
                                <h5 class="card-title">Total User</h5>
                                <h2 class="card-value"><?php echo $total_users; ?></h2>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="stat-card">
                                <h5 class="card-title">Admin</h5>
                                <h2 class="card-value"><?php echo $admins; ?></h2>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="stat-card">
                                <h5 class="card-title">User</h5>
                                <h2 class="card-value"><?php echo $regular_users; ?></h2>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Tambah User</button>
                </div>
            </div>
            
            <!-- Tabel daftar user -->
            <div class="card recent-table">
                <div class="card-header">
                    <h4 class="mb-0">Daftar User</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Role</th>
                                    <th>Ekstrakurikuler</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($users)): ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $user['role']; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['ekskul'] ? $user['ekskul'] : '-'); ?></td>
                                    <td>
                                        <a href="#" class="action-btn edit" data-bs-toggle="modal" data-bs-target="#editUserModal" data-id="<?php echo $user['user_id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>" data-nama="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" data-role="<?php echo $user['role']; ?>" data-ekskul="<?php echo htmlspecialchars($user['ekskul'] ? $user['ekskul'] : ''); ?>" data-bs-toggle="tooltip" title="Edit User">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $user['user_id']; ?>" class="action-btn delete" data-bs-toggle="tooltip" title="Hapus User">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <?php if (mysqli_num_rows($users) == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data user</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah User -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Tambah User Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama_lengkap" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-select" name="role" required>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ekstrakurikuler (untuk user)</label>
                                    <input type="text" class="form-control" name="ekskul">
                                </div>
                                <button type="submit" name="add_user" class="btn btn-primary">Tambah User</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Edit User -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <input type="hidden" id="edit_user_id" name="user_id">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" id="edit_username" name="username" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="edit_nama_lengkap" name="nama_lengkap" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-select" id="edit_role" name="role" required>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ekstrakurikuler (untuk user)</label>
                                    <input type="text" class="form-control" id="edit_ekskul" name="ekskul">
                                </div>
                                <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Sidebar and backdrop toggle
            function toggleSidebar() {
                $('.sidebar').toggleClass('active');
                $('.backdrop').toggleClass('active');
                $('.hamburger').animate({ scale: '1.05' }, 100).animate({ scale: '1' }, 100);
            }

            $('.hamburger').click(toggleSidebar);
            $('.backdrop').click(toggleSidebar);

            // Close sidebar on menu item click (mobile)
            $('.sidebar-menu li a').click(function() {
                if ($(window).width() < 992) {
                    toggleSidebar();
                }
            });

            // Sidebar active state
            $('.sidebar-menu li').click(function() {
                $('.sidebar-menu li').removeClass('active');
                $(this).addClass('active');
            });

            // Handle edit modal
            $('#editUserModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var username = button.data('username');
                var nama = button.data('nama');
                var role = button.data('role');
                var ekskul = button.data('ekskul');

                var modal = $(this);
                modal.find('#edit_user_id').val(id);
                modal.find('#edit_username').val(username);
                modal.find('#edit_nama_lengkap').val(nama);
                modal.find('#edit_role').val(role);
                modal.find('#edit_ekskul').val(ekskul);

                $('.hamburger').addClass('hidden');
            });

            $('#editUserModal').on('hidden.bs.modal', function () {
                $('.hamburger').removeClass('hidden');
            });

            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Optional: Auto-dismiss alerts after 5 seconds (uncomment to enable)
            /*
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            */
        });
    </script>
</body>
</html>
