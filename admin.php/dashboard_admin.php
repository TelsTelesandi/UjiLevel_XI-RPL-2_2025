<?php
session_start();
require_once 'koneksi_admin.php';

// Cek apakah sudah login sebagai admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

// Proses CRUD User
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_user':
                $username = $_POST['username'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $nama_lengkap = $_POST['nama_lengkap'];
                $eskul = $_POST['eskul'];
                $role = $_POST['role'];

                $query = "INSERT INTO users (username, password, nama_lengkap, eskul, role) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssss", $username, $password, $nama_lengkap, $eskul, $role);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "User berhasil ditambahkan!";
                } else {
                    $_SESSION['error'] = "Gagal menambahkan user: " . $conn->error;
                }
                break;

            case 'edit_user':
                $user_id = $_POST['user_id'];
                $nama_lengkap = $_POST['nama_lengkap'];
                $eskul = $_POST['eskul'];
                $role = $_POST['role'];

                $query = "UPDATE users SET nama_lengkap = ?, eskul = ?, role = ? WHERE username = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssss", $nama_lengkap, $eskul, $role, $user_id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "User berhasil diupdate!";
                } else {
                    $_SESSION['error'] = "Gagal mengupdate user: " . $conn->error;
                }
                break;

            case 'delete_user':
                $user_id = $_POST['user_id'];
                
                $query = "DELETE FROM users WHERE username = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $user_id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "User berhasil dihapus!";
                } else {
                    $_SESSION['error'] = "Gagal menghapus user: " . $conn->error;
                }
                break;

            case 'verify_event':
                $event_id = $_POST['event_id'];
                $status = $_POST['status'];
                $admin_id = $_SESSION['user_id'];
                $verifikasi_date = date('Y-m-d H:i:s');
                $catatan = ($status === 'approved') ? 'Disetujui oleh admin' : 'Ditolak oleh admin';
                $query = "UPDATE events SET status=?, admin_id=?, tanggal_verifikasi=?, catatan_admin=? WHERE event_id=?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sisss", $status, $admin_id, $verifikasi_date, $catatan, $event_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Status event berhasil diubah menjadi $status!";
                } else {
                    $_SESSION['error'] = "Gagal mengubah status event.";
                }
                header("Location: dashboard_admin.php");
                exit();
                break;
        }
    }
}

// Ambil data untuk dashboard
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$row = $result->fetch_assoc();
$total_users = $row['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM events");
$row = $result->fetch_assoc();
$total_events = $row['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM events WHERE status = 'pending'");
$row = $result->fetch_assoc();
$pending_events = $row['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM events WHERE status = 'approved'");
$row = $result->fetch_assoc();
$approved_events = $row['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM events WHERE status = 'rejected'");
$row = $result->fetch_assoc();
$rejected_events = $row['total'];

// Ambil data users untuk tabel
$users = $conn->query("SELECT * FROM users ORDER BY username DESC");

// Ambil data events untuk tabel
$events = $conn->query("SELECT e.*, u.nama_lengkap, u.eskul 
                       FROM events e 
                       JOIN users u ON e.username = u.username 
                       ORDER BY e.tanggal_pengajuan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f6f9;
        }

        .navbar {
            background: linear-gradient(135deg, #43a047, #388e3c);
            padding: 1rem 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card h3 {
            color: #1a237e;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card p {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
        }

        .section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .btn {
            background: #4caf50;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #45a049;
        }

        .btn-danger {
            background: #f44336;
        }

        .btn-danger:hover {
            background: #d32f2f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fa;
            color: #1a237e;
            font-weight: 600;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 500px;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 10px;
            position: relative;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.875rem;
        }

        .status-pending {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-approved {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-rejected {
            background: #ffebee;
            color: #c62828;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Dashboard Admin</h1>
        <div>
            <a href="../logout.php" class="btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <h3><i class="fas fa-users"></i> Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-calendar-alt"></i> Total Events</h3>
                <p><?php echo $total_events; ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-clock"></i> Pending Events</h3>
                <p><?php echo $pending_events; ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-check-circle"></i> Approved Events</h3>
                <p><?php echo $approved_events; ?></p>
            </div>
        </div>

        <!-- User Management Section -->
        <div class="section">
            <div class="section-header">
                <h2>Manajemen User</h2>
                <button class="btn" onclick="openModal('addUserModal')">Tambah User</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Ekskul</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($user['eskul']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <button class="btn" onclick="openEditUserModal('<?php echo htmlspecialchars(json_encode($user)); ?>')">Edit</button>
                                <button class="btn btn-danger" onclick="deleteUser('<?php echo $user['username']; ?>')">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Events Section -->
        <div class="section">
            <div class="section-header">
                <h2>Daftar Event</h2>
                <a href="login_admin.php" class="btn">Verifikasi Event</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Event ID</th>
                        <th>Judul Event</th>
                        <th>Ekskul</th>
                        <th>Pemohon</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($event = $events->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $event['event_id']; ?></td>
                            <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                            <td><?php echo htmlspecialchars($event['eskul']); ?></td>
                            <td><?php echo htmlspecialchars($event['nama_lengkap']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($event['status']); ?>">
                                    <?php echo htmlspecialchars($event['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_event.php?id=<?php echo $event['event_id']; ?>" class="btn">Detail</a>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="action" value="verify_event">
                                    <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn" style="background:#4caf50;">Approved</button>
                                </form>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="action" value="verify_event">
                                    <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-danger" style="background:#f44336;">Rejected</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addUserModal')">&times;</span>
            <h2>Tambah User</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_user">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required>
                </div>
                <div class="form-group">
                    <label>Ekskul</label>
                    <input type="text" name="eskul" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editUserModal')">&times;</span>
            <h2>Edit User</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit_nama_lengkap" required>
                </div>
                <div class="form-group">
                    <label>Ekskul</label>
                    <input type="text" name="eskul" id="edit_eskul" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="edit_role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn">Update</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function openEditUserModal(user) {
            document.getElementById('edit_user_id').value = user.username;
            document.getElementById('edit_nama_lengkap').value = user.nama_lengkap;
            document.getElementById('edit_eskul').value = user.eskul;
            document.getElementById('edit_role').value = user.role;
            openModal('editUserModal');
        }

        function deleteUser(userId) {
            if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
