<?php
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/navbar.php';

// Ambil data user (kecuali admin utama dengan id=1, jika ada)
$stmt = $conn->query("SELECT id, nama_lengkap, username, email, role FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<main>
    <section class="dashboard-section">
        <h2><i class="fas fa-users" style="color:#2563eb"></i> Daftar User</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): $no = 1; foreach ($users as $user): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($user['nama_lengkap']); ?></td>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                    <td><?= htmlspecialchars(ucfirst($user['role'])); ?></td>
                    <td>
                        <?php if ($user['role'] !== 'admin'): ?>
                        <form action="../proses/user_hapus.php" method="post" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                            <input type="hidden" name="id" value="<?= $user['id']; ?>">
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                        <?php else: ?>
                        <span style="color:#888; font-size:0.95em;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" style="text-align:center; color:#888;">Belum ada user terdaftar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<?php include '../includes/footer.php'; ?> 