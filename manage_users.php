<?php
include 'config.php';
include 'session.php';
if ($_SESSION['role'] != 'admin') die("Akses ditolak");

// Handle Create/Update User
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'create' || $_POST['action'] == 'update') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        
        if ($_POST['action'] == 'create') {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
        } else {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ?, role = ? WHERE username = ?");
                $stmt->bind_param("sss", $hashed_password, $role, $username);
            } else {
                $stmt = $conn->prepare("UPDATE users SET role = ? WHERE username = ?");
                $stmt->bind_param("ss", $role, $username);
            }
        }
        $stmt->execute();
    }
    
    // Handle Delete User
    elseif ($_POST['action'] == 'delete') {
        $username = $_POST['username'];
        $stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
    }
}

// Get all users
$result = $conn->query("SELECT username, role FROM users ORDER BY username");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .button {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .create {
            background-color: #4CAF50;
            color: white;
        }
        .edit {
            background-color: #2196F3;
            color: white;
        }
        .delete {
            background-color: #f44336;
            color: white;
        }
        .form-container {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .nav-links {
            margin-bottom: 20px;
        }
        .nav-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #2196F3;
        }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="admin.php">Kembali ke Panel Admin</a>
        <a href="logout.php">Logout</a>
    </div>

    <h2>Manajemen User</h2>
    
    <!-- Form Tambah User -->
    <div class="form-container">
        <h3>Tambah User Baru</h3>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            Username: <input type="text" name="username" required>
            Password: <input type="password" name="password" required>
            Role: 
            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" class="button create">Tambah User</button>
        </form>
    </div>

    <!-- Tabel User -->
    <table>
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($row['username']) ?>">
                    Password Baru: <input type="password" name="password">
                    Role: 
                    <select name="role" required>
                        <option value="user" <?= $row['role'] == 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <button type="submit" class="button edit">Update</button>
                </form>
                
                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($row['username']) ?>">
                    <button type="submit" class="button delete">Hapus</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html> 