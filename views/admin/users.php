<?php
// Cek autentikasi admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?action=login");
    exit();
}

// Ambil data users dari DatabaseQueries
require_once __DIR__ . '/../../app/models/DatabaseQueries.php';
$queries = new DatabaseQueries($db);
$users = $queries->getUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
            min-height: 100vh;
        }
        .content-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-indigo-900 min-h-screen p-4">
            <div class="flex items-center space-x-4 mb-6">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
                <h1 class="text-white text-xl font-bold">Admin Panel</h1>
            </div>
            
            <nav class="space-y-2">
                <a href="index.php?action=admin_dashboard" 
                   class="flex items-center space-x-2 text-gray-300 hover:bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="index.php?action=admin_verifications" 
                   class="flex items-center space-x-2 text-gray-300 hover:bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-check-circle"></i>
                    <span>Verifikasi Event</span>
                </a>
                <a href="index.php?action=manage_users" 
                   class="flex items-center space-x-2 text-white bg-indigo-800 p-2 rounded-lg">
                    <i class="fas fa-users"></i>
                    <span>Kelola Users</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white bg-opacity-10 p-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Kelola Users</h2>
                <div class="flex items-center space-x-4">
                    <div class="text-white">
                        <span class="mr-2">Welcome,</span>
                        <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    <a href="index.php?action=logout" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="p-8">
                <div class="content-card rounded-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <button onclick="showAddUserModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                <i class="fas fa-user-plus mr-2"></i>Add User
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-white">
                            <thead>
                                <tr class="text-left border-b border-gray-700">
                                    <th class="pb-3">Name</th>
                                    <th class="pb-3">Username</th>
                                    <th class="pb-3">Role</th>
                                    <th class="pb-3">Ekskul</th>
                                    <th class="pb-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <!-- Data akan diisi melalui AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Add New User</h3>
                <form id="userForm" onsubmit="return handleUserSubmit(event)">
                    <input type="hidden" id="userId" name="user_id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="username" name="username" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <small class="text-gray-500">Leave empty to keep current password when editing</small>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Ekskul</label>
                        <input type="text" id="ekskul" name="ekskul" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeUserModal()"
                                class="px-4 py-2 text-gray-500 hover:text-gray-700">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Load users saat halaman dimuat
    document.addEventListener('DOMContentLoaded', loadUsers);

    function loadUsers() {
        fetch('index.php?action=get_users')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('usersTableBody');
                tableBody.innerHTML = '';

                data.forEach(user => {
                    const row = document.createElement('tr');
                    row.className = 'border-b border-gray-800';
                    row.innerHTML = `
                        <td class="py-3">
                            <div class="font-medium">${user.nama_lengkap}</div>
                        </td>
                        <td class="py-3">
                            <div class="text-gray-300">${user.username}</div>
                        </td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded-full text-xs 
                                ${user.role === 'admin' ? 'bg-purple-500' : 'bg-blue-500'}">
                                ${user.role}
                            </span>
                        </td>
                        <td class="py-3">
                            <div class="text-gray-300">${user.ekskul || '-'}</div>
                        </td>
                        <td class="py-3">
                            <button onclick="editUser(${JSON.stringify(user).replace(/"/g, '&quot;')})" 
                                    class="text-blue-400 hover:text-blue-300 mr-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteUser(${user.user_id})"
                                    class="text-red-400 hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Error:', error));
    }

    function showAddUserModal() {
        document.getElementById('modalTitle').textContent = 'Add New User';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('password').required = true;
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function editUser(user) {
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('userId').value = user.user_id;
        document.getElementById('username').value = user.username;
        document.getElementById('nama_lengkap').value = user.nama_lengkap;
        document.getElementById('ekskul').value = user.ekskul || '';
        document.getElementById('role').value = user.role;
        document.getElementById('password').required = false;
        document.getElementById('userModal').classList.remove('hidden');
    }

    function handleUserSubmit(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const userId = formData.get('user_id');
        
        const url = userId 
            ? 'index.php?action=update_user'
            : 'index.php?action=add_user';
            
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeUserModal();
                loadUsers();
            } else {
                alert(data.message || 'An error occurred');
            }
        })
        .catch(error => console.error('Error:', error));
        
        return false;
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            fetch('index.php?action=delete_user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadUsers();
                } else {
                    alert(data.message || 'Error deleting user');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
    </script>
</body>
</html> 