<?php
// Tambahkan link Font Awesome dan Tailwind CSS di sini
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Handle user actions
if ($_POST) {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $nama_lengkap = $_POST['nama_lengkap'] ?? '';
        $eskul = $_POST['eskul'] ?? '';
        $role = $_POST['role'] ?? 'user';
        
        if ($username && $password && $nama_lengkap && $eskul) {
            // Check if username exists
            $check_query = "SELECT COUNT(*) as count FROM users WHERE username = ?";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->execute([$username]);
            
            if ($check_stmt->fetch()['count'] == 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_query = "INSERT INTO users (username, password, role, nama_lengkap, eskul) VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $db->prepare($insert_query);
                
                if ($insert_stmt->execute([$username, $hashed_password, $role, $nama_lengkap, $eskul])) {
                    $success = 'User berhasil ditambahkan!';
                } else {
                    $error = 'Gagal menambahkan user!';
                }
            } else {
                $error = 'Username sudah digunakan!';
            }
        } else {
            $error = 'Mohon isi semua field!';
        }
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        
        // Don't allow deleting current admin
        if ($user_id != getUserId()) {
            $delete_query = "DELETE FROM users WHERE user_id = ?";
            $delete_stmt = $db->prepare($delete_query);
            
            if ($delete_stmt->execute([$user_id])) {
                $success = 'User berhasil dihapus!';
            } else {
                $error = 'Gagal menghapus user!';
            }
        } else {
            $error = 'Tidak dapat menghapus akun sendiri!';
        }
    }
}

// Get all users
$users_query = "SELECT * FROM users ORDER BY created_at DESC";
$users = $db->query($users_query)->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'User Management - Event Submission System';
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


<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-users mr-3"></i>User Management
        </h1>
        <p class="text-gray-600 mt-2">Kelola pengguna sistem</p>
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

    <!-- Add User Form -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Add New User</h2>
        </div>
        <div class="p-6">
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" name="role" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                </div>
                
                <div>
                    <label for="eskul" class="block text-sm font-medium text-gray-700">Ekstrakurikuler</label>
                    <input type="text" id="eskul" name="eskul" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" name="add_user" 
                            class="w-full bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Users List</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ekstrakurikuler</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                <?php echo strtoupper(substr($user['nama_lengkap'], 0, 1)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['nama_lengkap']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $user['role'] == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($user['eskul']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if ($user['user_id'] != getUserId()): ?>
                                <form method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" name="delete_user" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="text-blue-600 hover:text-blue-900 ml-4">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php else: ?>
                                <span class="text-gray-400">Current User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


