<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Ambil user_id dari parameter URL
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header("Location: users.php");
    exit();
}

// Ambil data pengguna berdasarkan user_id
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: users.php");
    exit();
}

// Proses pengeditan pengguna
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $eskul = $_POST['eskul'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($username && $nama_lengkap && $eskul) {
        // Update data pengguna
        $update_query = "UPDATE users SET username = ?, nama_lengkap = ?, eskul = ?, role = ? WHERE user_id = ?";
        $update_stmt = $db->prepare($update_query);
        
        if ($update_stmt->execute([$username, $nama_lengkap, $eskul, $role, $user_id])) {
            $success = 'User berhasil diperbarui!';
        } else {
            $error = 'Gagal memperbarui user!';
        }
    } else {
        $error = 'Mohon isi semua field!';
    }
}

$page_title = 'Edit User - Event Submission System';
include '../includes/header.php';
?>
<nav class="bg-white border-b border-blue-600 shadow-md px-6 py-8 flex items-center justify-between">
    <div class="flex items-center space-x-2">
        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
        <span class="text-lg font-semibold text-gray-800">Event Submission System</span>
    </div>
    <div class="flex items-center space-x-6 text-sm text-gray-700">
        <a href="../dashboard.php" class="flex items-center hover:text-blue-600 transition-colors">
            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
        </a>
        <a href="users.php" class="flex items-center text-blue-600 font-semibold">
    <i class="fas fa-users mr-1"></i> Users
</a>
        <a href="approvals.php" class="flex items-center hover:text-blue-600 transition-colors">
            <i class="fas fa-check-circle mr-1"></i> Approvals
        </a>
        <a href="reports.php" class="flex items-center hover:text-blue-600 transition-colors">
            <i class="fas fa-file-alt mr-1"></i> Reports
        </a>
        <span class="flex items-center text-gray-700">
            <i class="fas fa-user mr-1"></i> Administrator
        </span>
        <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center transition-colors">
            <i class="fas fa-sign-out-alt mr-1"></i> Logout
        </a>
    </div>
</nav>

?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
        <p class="text-gray-600 mt-2">Perbarui informasi pengguna</p>
    </div>

    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $success; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" required
                       value="<?php echo htmlspecialchars($user['username']); ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
            </div>
            
            <div>
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required
                       value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div>
                <label for="eskul" class="block text-sm font-medium text-gray-700">Ekstrakurikuler</label>
                <input type="text" id="eskul" name="eskul" required
                       value="<?php echo htmlspecialchars($user['eskul']); ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md">
                    <i class="fas fa-save mr-2"></i>Update User
                </button>
            </div>
        </form>
    </div>
</div>

