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

// Periksa dan tambahkan kolom created_at jika belum ada
try {
    $check_column = "SHOW COLUMNS FROM users LIKE 'created_at'";
    $stmt = $conn->prepare($check_column);
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $add_column = "ALTER TABLE users ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $conn->exec($add_column);
    }
} catch(PDOException $e) {
    // Lanjutkan eksekusi meskipun ada error
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $username = $_POST['username'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $nama_lengkap = $_POST['nama_lengkap'];
                $ekskul = $_POST['ekskul'];
                $role = $_POST['role'];
                
                $query = "INSERT INTO users (username, password, nama_lengkap, ekskul, role) 
                         VALUES (:username, :password, :nama_lengkap, :ekskul, :role)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':nama_lengkap', $nama_lengkap);
                $stmt->bindParam(':ekskul', $ekskul);
                $stmt->bindParam(':role', $role);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = 'User berhasil ditambahkan!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Gagal menambahkan user!';
                    $_SESSION['message_type'] = 'danger';
                }
                break;

            case 'edit':
                $user_id = $_POST['user_id'];
                $username = $_POST['username'];
                $nama_lengkap = $_POST['nama_lengkap'];
                $ekskul = $_POST['ekskul'];
                $role = $_POST['role'];
                
                $query = "UPDATE users SET username = :username, nama_lengkap = :nama_lengkap, 
                         ekskul = :ekskul, role = :role WHERE user_id = :user_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':nama_lengkap', $nama_lengkap);
                $stmt->bindParam(':ekskul', $ekskul);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':user_id', $user_id);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = 'User berhasil diupdate!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Gagal mengupdate user!';
                    $_SESSION['message_type'] = 'danger';
                }
                break;

            case 'delete':
                $user_id = $_POST['user_id'];
                if ($user_id != $_SESSION['user_id']) {
                    $query = "DELETE FROM users WHERE user_id = :user_id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':user_id', $user_id);
                    
                    if ($stmt->execute()) {
                        $_SESSION['message'] = 'User berhasil dihapus!';
                        $_SESSION['message_type'] = 'success';
                    } else {
                        $_SESSION['message'] = 'Gagal menghapus user!';
                        $_SESSION['message_type'] = 'danger';
                    }
                } else {
                    $_SESSION['message'] = 'Tidak dapat menghapus akun sendiri!';
                    $_SESSION['message_type'] = 'danger';
                }
                break;
        }
    }
    header("Location: manage_users.php");
    exit();
}

// Get all users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Sistem Pengajuan Event</title>
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
                            <a class="nav-link active" href="manage_users.php">
                                <i class="fas fa-users me-2"></i>Kelola User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
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
                        <h2>Kelola User</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-2"></i>Tambah User
                        </button>
                    </div>

                    <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Username</th>
                                            <th>Nama Lengkap</th>
                                            <th>Ekstrakurikuler</th>
                                            <th>Role</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        foreach ($users as $user): 
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                            <td><?php echo htmlspecialchars($user['ekskul']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                        data-bs-target="#editUserModal<?php echo $user['user_id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['user_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit User -->
                                        <div class="modal fade" id="editUserModal<?php echo $user['user_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="edit">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Username</label>
                                                                <input type="text" class="form-control" name="username" 
                                                                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Nama Lengkap</label>
                                                                <input type="text" class="form-control" name="nama_lengkap" 
                                                                       value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Ekstrakurikuler</label>
                                                                <input type="text" class="form-control" name="ekskul" 
                                                                       value="<?php echo htmlspecialchars($user['ekskul']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Role</label>
                                                                <select class="form-select" name="role" required>
                                                                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (empty($users)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada user terdaftar</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
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
                            <label class="form-label">Ekstrakurikuler</label>
                            <input type="text" class="form-control" name="ekskul" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Delete User (Hidden) -->
    <form id="deleteUserForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="user_id" id="deleteUserId">
    </form>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteUser(userId) {
            if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                document.getElementById('deleteUserId').value = userId;
                document.getElementById('deleteUserForm').submit();
            }
        }
    </script>
</body>
</html>
