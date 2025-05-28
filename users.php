<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Handle user operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nama_lengkap = $_POST['nama_lengkap'];
        $ekskul = $_POST['ekskul'];
        $role = $_POST['role'];
        
        $query = "INSERT INTO users (username, password, role, nama_lengkap, Ekskul) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$username, $password, $role, $nama_lengkap, $ekskul])) {
            $success = 'User berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan user!';
        }
    } elseif (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $ekskul = $_POST['ekskul'];
        $role = $_POST['role'];
        
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = "UPDATE users SET username = ?, password = ?, role = ?, nama_lengkap = ?, Ekskul = ? WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([$username, $password, $role, $nama_lengkap, $ekskul, $user_id]);
        } else {
            $query = "UPDATE users SET username = ?, role = ?, nama_lengkap = ?, Ekskul = ? WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([$username, $role, $nama_lengkap, $ekskul, $user_id]);
        }
        
        if ($result) {
            $success = 'User berhasil diupdate!';
        } else {
            $error = 'Gagal mengupdate user!';
        }
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        
        $query = "DELETE FROM users WHERE user_id = ? AND role != 'admin'";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$user_id])) {
            $success = 'User berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus user!';
        }
    }
}

// Get all users
$query = "SELECT * FROM users ORDER BY user_id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Event Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
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
                            <a class="nav-link text-white active" href="users.php">
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
                            <a class="nav-link text-white" href="laporan.php">
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
                    <h1 class="h2">Kelola User</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-users"></i> Daftar User</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Nama Lengkap</th>
                                        <th>Ekstrakurikuler</th>
                                        <th>Role</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['user_id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                            <td><?php echo isset($user['Ekskul']) ? htmlspecialchars($user['Ekskul']) : ''; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['user_id']; ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <?php if ($user['role'] != 'admin'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                        <button type="submit" name="delete_user" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <!-- Edit User Modal -->
                                        <div class="modal fade" id="editUserModal<?php echo $user['user_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                            <div class="mb-3">
                                                                <label for="username" class="form-label">Username</label>
                                                                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="password" class="form-label">Password (kosongkan jika tidak ingin mengubah)</label>
                                                                <input type="password" class="form-control" name="password">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                                                <input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="ekskul" class="form-label">Ekstrakurikuler</label>
                                                                <input type="text" class="form-control" name="ekskul" value="<?php echo htmlspecialchars($user['Ekskul']); ?>">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="role" class="form-label">Role</label>
                                                                <select class="form-select" name="role" required>
                                                                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="edit_user" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label for="ekskul" class="form-label">Ekstrakurikuler</label>
                            <input type="text" class="form-control" name="ekskul">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_user" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>