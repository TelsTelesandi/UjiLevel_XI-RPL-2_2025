<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'bounce-slow': 'bounce 3s linear infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'scale': 'scale 2s ease-in-out infinite',
                        'slide-up': 'slideUp 0.5s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        scale: {
                            '0%, 100%': { transform: 'scale(1)' },
                            '50%': { transform: 'scale(1.05)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                    },
                },
            },
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .sidebar {
            background-color: #1e1b4b;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 16rem;
            overflow-y: auto;
        }
        .main-content {
            margin-left: 16rem;
            min-height: 100vh;
        }
        .content-area {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .user-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .user-card:hover {
            transform: translateY(-5px);
        }
        .table-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .table-row:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        /* Styling scrollbar untuk sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #1e1b4b;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #4338ca;
            border-radius: 2px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>
</head>
<body>
    <div class="flex">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white p-6">
            <div class="flex items-center mb-8">
                <i class="fas fa-shield-alt text-2xl mr-3"></i>
                <h1 class="text-xl font-semibold">Admin Panel</h1>
            </div>
            
            <nav class="space-y-4">
                <a href="index.php?action=admin_dashboard" class="flex items-center text-gray-300 hover:text-white">
                    <i class="fas fa-home w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="index.php?action=admin_users" class="flex items-center text-white bg-indigo-900 rounded-lg p-2">
                    <i class="fas fa-users w-6"></i>
                    <span>Kelola User</span>
                </a>
                <a href="index.php?action=admin_events" class="flex items-center text-gray-300 hover:text-white">
                    <i class="fas fa-calendar w-6"></i>
                    <span>Kelola Event</span>
                </a>
                <a href="index.php?action=admin_verifications" class="flex items-center text-gray-300 hover:text-white">
                    <i class="fas fa-check-circle w-6"></i>
                    <span>Verifikasi Event</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1">
            <!-- Top Bar -->
            <div class="bg-white bg-opacity-10 p-4 flex justify-between items-center">
                <button class="text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-4">
                    <span class="text-white">Welcome, <?php echo $_SESSION['username'] ?? 'admin@admin.com'; ?></span>
                    <a href="index.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <div class="content-area rounded-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <h2 class="text-2xl text-white font-semibold">Kelola User</h2>
                            <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm" id="userCounter">0 Users</span>
                        </div>
                        <div class="flex space-x-4">
                            <div class="relative">
                                <input type="text" placeholder="Search users..." 
                                       class="bg-white bg-opacity-20 text-white placeholder-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search absolute right-3 top-3 text-gray-300"></i>
                            </div>
                            <button onclick="showAddUserModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                <i class="fas fa-user-plus mr-2"></i>Add User
                            </button>
                        </div>
                    </div>

                    <!-- User Cards -->
                    <div class="grid gap-6" id="userContainer">
                        <!-- User cards will be inserted here by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="userModal" class="fixed inset-0 bg-black/50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-8 max-w-xl bg-[#424874] shadow-lg rounded-xl text-white transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-white" id="modalTitle">Tambah User Baru</h3>
                <button onclick="closeUserModal()" class="text-white/70 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="userForm" onsubmit="return handleUserSubmit(event)" class="space-y-5">
                <input type="hidden" id="userId" name="user_id">
                
                <div>
                    <label class="block text-white mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                           placeholder="Masukkan username"
                           class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white 
                                  placeholder-white/50 focus:outline-none focus:border-blue-500
                                  transition-all duration-200">
                </div>

                <div>
                    <label class="block text-white mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                               placeholder="Masukkan password"
                               class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white 
                                      placeholder-white/50 focus:outline-none focus:border-blue-500
                                      transition-all duration-200">
                        <small class="text-white/70 hidden mt-1 text-xs">*Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                </div>

                <div>
                    <label class="block text-white mb-2">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required
                           placeholder="Masukkan nama lengkap"
                           class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white 
                                  placeholder-white/50 focus:outline-none focus:border-blue-500
                                  transition-all duration-200">
                </div>

                <div>
                    <label class="block text-white mb-2">Jenis Ekstrakurikuler</label>
                    <select id="ekskul" name="ekskul" required
                            class="w-full px-4 py-2.5 rounded-lg bg-[#2D3250] border border-white/20 text-white 
                                   focus:outline-none focus:border-blue-500 transition-all duration-200">
                        <option value="" class="bg-[#2D3250]">Pilih Jenis Ekstrakurikuler</option>
                        <option value="Akustik" class="bg-[#2D3250]">Akustik</option>
                        <option value="Futsal" class="bg-[#2D3250]">Futsal</option>
                        <option value="Tari Tradisional" class="bg-[#2D3250]">Tari Tradisional</option>
                        <option value="Basket" class="bg-[#2D3250]">Basket</option>
                        <option value="Pramuka" class="bg-[#2D3250]">Pramuka</option>
                    </select>
                </div>

                <div>
                    <label class="block text-white mb-2">Role</label>
                    <select id="role" name="role" required
                            class="w-full px-4 py-2.5 rounded-lg bg-[#2D3250] border border-white/20 text-white 
                                   focus:outline-none focus:border-blue-500 transition-all duration-200">
                        <option value="user" class="bg-[#2D3250]">User</option>
                        <option value="admin" class="bg-[#2D3250]">Admin</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 mt-8">
                    <button type="button" onclick="closeUserModal()"
                            class="px-6 py-2.5 bg-gray-600/50 hover:bg-gray-600 text-white rounded-lg
                                   transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg
                                   transition-colors duration-200">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="fixed top-4 right-4 z-50 hidden transform transition-all duration-300">
        <div class="px-6 py-3 rounded-lg shadow-lg min-w-[300px] bg-white/10 backdrop-blur-lg">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-3 text-xl"></i>
                <p class="notification-message text-white"></p>
            </div>
        </div>
    </div>

    <script>
        // Load users when page loads
        document.addEventListener('DOMContentLoaded', loadUsers);

        async function loadUsers() {
            try {
                const response = await fetch('index.php?action=get_users');
                console.log('Raw response:', await response.clone().text());
                
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                
                let jsonData;
                try {
                    jsonData = await response.json();
                } catch (e) {
                    console.error('JSON Parse Error:', e);
                    throw new Error('Invalid JSON response from server');
                }
                
                console.log('Parsed response data:', jsonData);
                
                if (!jsonData.success) {
                    throw new Error(jsonData.message || 'Failed to load users');
                }

                const container = document.getElementById('userContainer');
                container.innerHTML = '';
                
                // Update user counter
                const userCounter = document.getElementById('userCounter');
                userCounter.textContent = `${jsonData.data.userCount} Users`;
                
                if (!Array.isArray(jsonData.data.users)) {
                    throw new Error('Invalid users data received');
                }
                
                jsonData.data.users.forEach(user => {
                    // Validasi data user
                    if (!user || typeof user !== 'object' || !user.user_id) {
                        console.error('Invalid user data:', user);
                        return;
                    }
                    
                    const card = createUserCard(user);
                    if (card) {
                        container.appendChild(card);
                    }
                });
            } catch (error) {
                console.error('Error in loadUsers:', error);
                showNotification('Gagal memuat data user: ' + error.message, 'error');
            }
        }

        function createUserCard(user) {
            console.log('Creating card for user:', user);
            
            // Pastikan user_id adalah integer
            const userId = parseInt(user.user_id);
            if (isNaN(userId)) {
                console.error('Invalid user_id:', user.user_id);
                return null;
            }
            
            const div = document.createElement('div');
            div.className = 'user-card rounded-lg p-6 text-white mb-4';
            div.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-semibold">${user.username}</h3>
                        <p class="text-gray-300">${user.nama_lengkap}</p>
                        <p class="text-gray-300">${user.ekskul}</p>
                        <span class="inline-block px-2 py-1 rounded-full text-sm ${
                            user.role === 'admin' ? 'bg-purple-500' : 'bg-blue-500'
                        } mt-2">${user.role}</span>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editUser(${userId})" 
                                class="text-yellow-400 hover:text-yellow-300 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteUser(${userId})" 
                                class="text-red-400 hover:text-red-300 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            return div;
        }

        function showUserModal() {
            const modal = document.getElementById('userModal');
            const modalContent = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            // Trigger animation after modal is shown
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeUserModal() {
            const modal = document.getElementById('userModal');
            const modalContent = document.getElementById('modalContent');
            // Reverse animation
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('userForm').reset();
            }, 200);
        }

        function showAddUserModal() {
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('userForm');
            const passwordField = document.getElementById('password');
            
            modalTitle.textContent = 'Tambah User Baru';
            form.reset();
            document.getElementById('userId').value = '';
            
            // Set password field sebagai required untuk user baru
            passwordField.required = true;
            const smallElement = passwordField.parentElement.querySelector('small');
            if (smallElement) {
                smallElement.style.display = 'none';
            }
            
            showUserModal();
        }

        function editUser(userId) {
            console.log('Editing user with ID:', userId);
            
            // Show loading state
            showNotification('Memuat data user...', 'info');
            
            fetch(`index.php?action=get_user&id=${userId}`)
                .then(response => {
                    console.log('Edit user response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(user => {
                    console.log('User data received:', user);
                    if (!user) {
                        throw new Error('User tidak ditemukan');
                    }

                    const modal = document.getElementById('userModal');
                    const form = document.getElementById('userForm');
                    const passwordField = document.getElementById('password');
                    
                    document.getElementById('modalTitle').textContent = 'Edit User';
                    document.getElementById('userId').value = user.user_id;
                    document.getElementById('username').value = user.username;
                    document.getElementById('nama_lengkap').value = user.nama_lengkap;
                    document.getElementById('ekskul').value = user.ekskul;
                    document.getElementById('role').value = user.role;
                    
                    // Reset dan disable required untuk password dalam mode edit
                    passwordField.value = '';
                    passwordField.required = false;
                    const smallElement = passwordField.parentElement.querySelector('small');
                    if (smallElement) {
                        smallElement.style.display = 'block';
                    }
                    
                    showUserModal();
                })
                .catch(error => {
                    console.error('Error in editUser:', error);
                    showNotification('Gagal memuat data user: ' + error.message, 'error');
                });
        }

        function deleteUser(userId) {
            if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                return;
            }

            showNotification('Menghapus user...', 'info');
            
            fetch(`index.php?action=delete_user&id=${userId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Delete response status:', response.status);
                return response.json().then(data => {
                    if (!response.ok) {
                        return Promise.reject(data);
                    }
                    return data;
                });
            })
            .then(result => {
                console.log('Delete result:', result);
                if (result.success) {
                    showNotification(result.message || 'User berhasil dihapus', 'success');
                    loadUsers(); // Reload the user list
                } else {
                    throw new Error(result.message || 'Gagal menghapus user');
                }
            })
            .catch(error => {
                console.error('Error in deleteUser:', error);
                showNotification(
                    error.message || 'Gagal menghapus user: Terjadi kesalahan',
                    'error'
                );
            });
        }

        function handleUserSubmit(event) {
            event.preventDefault();
            
            // Debug log
            console.log('Form submission started');
            
            const form = event.target;
            const formData = new FormData(form);
            
            // Debug log form data
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            const userId = formData.get('user_id');
            const url = userId 
                ? `index.php?action=update_user&id=${userId}`
                : 'index.php?action=add_user';

            // Validasi input
            const username = formData.get('username');
            const nama_lengkap = formData.get('nama_lengkap');
            const ekskul = formData.get('ekskul');
            const role = formData.get('role');
            const password = formData.get('password');

            if (!username || !nama_lengkap || !ekskul || !role) {
                showNotification('Semua field harus diisi', 'error');
                return false;
            }

            if (!userId && !password) {
                showNotification('Password harus diisi untuk user baru', 'error');
                return false;
            }

            // Show loading state
            showNotification('Menyimpan data...', 'info');

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json().then(data => {
                    if (!response.ok) {
                        return Promise.reject(data);
                    }
                    return data;
                });
            })
            .then(result => {
                console.log('Submit result:', result);
                if (result.success) {
                    showNotification(result.message, 'success');
                    closeUserModal();
                    loadUsers();
                } else {
                    throw new Error(result.message || 'Gagal menyimpan data user');
                }
            })
            .catch(error => {
                console.error('Error in handleUserSubmit:', error);
                showNotification(
                    error.message || 'Gagal menyimpan data user: Terjadi kesalahan',
                    'error'
                );
            });

            return false;
        }

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const messageElement = notification.querySelector('.notification-message');
            
            notification.classList.remove('hidden', 'bg-green-500', 'bg-red-500', 'bg-blue-500');
            notification.classList.add(
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            );
            messageElement.textContent = message;
            
            notification.classList.remove('hidden');
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        // Close modal when clicking outside
        const modal = document.getElementById('userModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeUserModal();
                }
            });
        }
    </script>
</body>
</html> 