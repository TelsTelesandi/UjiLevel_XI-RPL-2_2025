<?php
session_start();
include '../config/db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Anda harus login sebagai admin untuk mengakses halaman ini.";
    header("Location: ../auth/login.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $event_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = "DELETE FROM event_pengajuan WHERE event_id = '$event_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['success'] = "Event berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus event: " . mysqli_error($conn);
    }
    header("Location: manage_events.php");
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
    $nama_event = mysqli_real_escape_string($conn, $_POST['nama_event']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_query = "UPDATE event_pengajuan 
                    SET nama_event = '$nama_event',
                        deskripsi = '$deskripsi',
                        tanggal = '$tanggal',
                        status = '$status'
                    WHERE event_id = '$event_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success'] = "Event berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui event: " . mysqli_error($conn);
    }
    header("Location: manage_events.php");
    exit();
}

// Get all events with user information
$query = "SELECT ep.*, u.nama_lengkap, u.email 
          FROM event_pengajuan ep 
          JOIN users u ON ep.user_id = u.user_id 
          ORDER BY ep.event_id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Event - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --text-light: #ffffff;
            --text-dark: #2c3e50;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-light);
        }

        .page-wrapper {
            min-height: 100vh;
            padding: 2rem;
        }

        .nav-modern {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .nav-list {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            justify-content: center;
        }

        .nav-link {
            color: var(--text-light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .events-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-dark);
        }

        .events-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .events-header h1 {
            color: var(--primary-color);
            margin: 0;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-warning {
            background: var(--warning-color);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .modern-table th {
            background: var(--primary-color);
            color: var(--text-light);
            padding: 1rem;
            text-align: left;
        }

        .modern-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .modern-table tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            color: white;
            text-align: center;
        }

        .alert-success {
            background: var(--success-color);
        }

        .alert-error {
            background: var(--danger-color);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-dark);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-pending {
            background: var(--warning-color);
            color: white;
        }

        .status-approved {
            background: var(--success-color);
            color: white;
        }

        .status-rejected {
            background: var(--danger-color);
            color: white;
        }

        .status-completed {
            background: var(--accent-color);
            color: white;
        }

        .status-closed {
            background: var(--dark-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Navigation -->
        <nav class="nav-modern">
            <div class="container">
                <ul class="nav-list">
                    <li><a href="dashboard_admin.php" class="nav-link">Dashboard</a></li>
                    <li><a href="manage_events.php" class="nav-link">Kelola Event</a></li>
                    <li><a href="../auth/logout.php" class="nav-link">Keluar</a></li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="events-container">
                <div class="events-header">
                    <h1>Kelola Event</h1>
                    <a href="../pengajuan_event.php" class="btn btn-primary">Tambah Event Baru</a>
                </div>

                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Nama Event</th>
                            <th>Pengaju</th>
                            <th>Email</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['nama_event']); ?></td>
                                <td><?php echo htmlspecialchars($event['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($event['email']); ?></td>
                                <td><?php echo htmlspecialchars($event['tanggal']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($event['status']); ?>">
                                        <?php echo ucfirst($event['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button onclick="editEvent(<?php echo htmlspecialchars(json_encode($event)); ?>)" class="btn btn-warning">Edit</button>
                                    <a href="?delete=<?php echo $event['event_id']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus event ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Event</h2>
            <form method="POST" action="">
                <input type="hidden" name="event_id" id="edit_event_id">
                <div class="form-group">
                    <label for="edit_nama_event">Nama Event</label>
                    <input type="text" class="form-control" id="edit_nama_event" name="nama_event" required>
                </div>
                <div class="form-group">
                    <label for="edit_deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="edit_deskripsi" name="deskripsi" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_tanggal">Tanggal</label>
                    <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                </div>
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select class="form-control" id="edit_status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="completed">Completed</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <button type="submit" name="update" class="btn btn-primary">Update Event</button>
            </form>
        </div>
    </div>

    <script>
        function editEvent(event) {
            document.getElementById('edit_event_id').value = event.event_id;
            document.getElementById('edit_nama_event').value = event.nama_event;
            document.getElementById('edit_deskripsi').value = event.deskripsi;
            document.getElementById('edit_tanggal').value = event.tanggal;
            document.getElementById('edit_status').value = event.status;
            
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html> 