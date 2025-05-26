<?php
// Mulai session
session_start();

// Cek apakah user sudah login dan memiliki peran Admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin"){
    // Jika tidak, redirect ke halaman login
    header("location: index.php");
    exit;
}

// Include file config
require_once "config.php";

$users = [];

// Query untuk mengambil semua data user
$sql = "SELECT user_id, username, role, nama_lengkap, Ekskul FROM users ORDER BY user_id ASC";

if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $users[] = $row;
        }
        // Free result set
        mysqli_free_result($result);
    } else{
        // Tidak ada user ditemukan
    }
} else{
    echo "Oops! Ada yang salah. Silakan coba lagi nanti.";
}

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <div class="user-info">
                <i class="fas fa-users-cog fa-2x" style="color: var(--primary-color);"></i>
                <h2>Selamat Datang, Admin <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
                <p>Ini adalah halaman manajemen pengguna.</p>
            </div>

            <div class="dashboard-links">
                <a href="admin_add_user.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Tambah Pengguna Baru</a>
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-circle-left"></i> Kembali ke Dashboard</a>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="content-header">
                <h3><i class="fas fa-list"></i> Daftar Pengguna</h3>
            </div>

            <?php if(empty($users)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Belum ada pengguna dalam sistem.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Nama Lengkap</th>
                                <th>Ekskul</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($user['Ekskul'] ?? ''); ?></td>
                                    <td class="action-links">
                                        <a href="admin_edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="admin_delete_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus pengguna ini?');"><i class="fas fa-trash-alt"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-footer">
            <a href="logout.php" class="logout-link btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</body>
</html> 