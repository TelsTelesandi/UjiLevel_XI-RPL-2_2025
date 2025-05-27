<?php
session_start();

require_once '../db_connect.php';

// Cek login dan role admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: login.php");
    exit;
}

// Dummy data user (ganti dengan query ke database jika sudah ada tabel users)
$users = [
    [
        'user_id' => 1,
        'username' => 'user',
        'role' => 'user',
        'nama' => 'taqiyudin'
    ],
    [
        'user_id' => 2,
        'username' => 'admin',
        'role' => 'admin',
        'nama' => 'rafli adi'
    ],
    [
        'user_id' => 3,
        'username' => 'dayat',
        'role' => 'user',
        'nama' => 'dayatajaudah'
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Admin Panel</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background-color: #f8fafd;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3b41;
            color: #b8c7ce;
            padding-top: 20px;
            height: 100vh;
            position: fixed;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 30px;
            font-size: 1.8em;
            padding: 0 15px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            flex-grow: 1;
        }
        .sidebar ul li {
            padding: 12px 20px;
            border-bottom: 1px solid #3a4b54;
        }
        .sidebar ul li:first-child {
             border-top: 1px solid #3a4b54;
        }
        .sidebar ul li a {
            color: #b8c7ce;
            text-decoration: none;
            display: block;
            font-size: 1.1em;
            transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
        }
        .sidebar ul li a:hover {
            background-color: #3a4b54;
            color: #ffffff;
        }
        .sidebar ul li.active a {
            background-color: #007bff;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            flex-grow: 1;
        }
        .main-content h1 {
            margin-top: 0;
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
        }
        .user-table-section {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }
        .user-table-section h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.6em;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .user-table-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 1px solid #ddd;
        }
        .user-table-section th,
        .user-table-section td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
        }
        .user-table-section th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #555;
        }
        .user-table-section tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .user-table-section tbody tr:hover {
            background-color: #e9e9e9;
            transition: background-color 0.2s ease-in-out;
        }
        .logout-link {
            display: block;
            padding: 15px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            text-align: center;
            font-size: 1.1em;
            margin-top: auto;
            transition: background-color 0.2s ease-in-out;
        }
        .logout-link:hover {
            background-color: #c82333;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.2s ease-in-out;
        }
        .btn-approve {
            background-color: #28a745;
            color: white;
        }
        .btn-approve:hover {
            background-color: #218838;
        }
        .btn-reject {
            background-color: #dc3545;
            color: white;
        }
        .btn-reject:hover {
            background-color: #c82333;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>"><a href="dashboard.php">Dashboard</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'active' : ''; ?>"><a href="manage_users.php">Manajemen User</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'approval.php') ? 'active' : ''; ?>"><a href="approval.php">Approval Kegiatan</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : ''; ?>"><a href="laporan.php">Laporan</a></li>
        </ul>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div class="main-content">
        <h1>Manajemen User</h1>
        <div class="user-table-section">
            <h3>Daftar User</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['nama']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <?php 
                                $status = isset($user['status']) ? $user['status'] : 'pending';
                                $statusClass = 'status-' . $status;
                                $statusText = ucfirst($status);
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-approve" onclick="approveUser(<?php echo $user['user_id']; ?>)">Approve</button>
                                    <button class="btn btn-reject" onclick="rejectUser(<?php echo $user['user_id']; ?>)">Reject</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function approveUser(userId) {
            if(confirm('Apakah Anda yakin ingin menyetujui user ini?')) {
                // Tambahkan AJAX call atau form submission untuk approve user
                console.log('Approving user:', userId);
            }
        }

        function rejectUser(userId) {
            if(confirm('Apakah Anda yakin ingin menolak user ini?')) {
                // Tambahkan AJAX call atau form submission untuk reject user
                console.log('Rejecting user:', userId);
            }
        }
    </script>
</body>
</html>
