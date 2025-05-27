<!-- manajemen_user.html -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manajemen User</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#3B82F6',
            secondary: '#F59E0B',
            success: '#10B981',
            danger: '#EF4444',
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-50 font-sans">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-blue-800 text-white hidden md:block">
      <div class="p-4 flex items-center space-x-2 border-b border-blue-700">
        <i class="fas fa-calendar-check text-2xl"></i>
        <span class="text-xl font-bold">EventEkskul</span>
      </div>
      <nav class="p-4">
        <ul class="space-y-2">
          <li>
            <a href="dashboard_admin.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
              <i class="fas fa-tachometer-alt w-5"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="approval_report.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
              <i class="fas fa-clipboard-check w-5"></i>
              <span>Approval & Report</span>
            </a>
          </li>
          <li>
            <a href="manajemen_user.php" class="flex items-center space-x-3 p-3 rounded-lg bg-blue-700">
              <i class="fas fa-users-cog w-5"></i>
              <span>Manajemen User</span>
            </a>
          </li>
        </ul>
        <div class="mt-8 pt-4 border-t border-blue-700">
          <a href="../controllers/logout.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Logout</span>
          </a>
        </div>
      </nav>
    </div>
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Mobile Header -->
      <header class="bg-white shadow-sm md:hidden">
        <div class="flex justify-between items-center p-4">
          <button id="sidebarToggle" class="text-gray-600">
            <i class="fas fa-bars"></i>
          </button>
          <h1 class="text-xl font-bold text-gray-800">EventEkskul</h1>
          <div class="w-8"></div>
        </div>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-6xl mx-auto">
          <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-4">
            <h5 class="text-lg font-bold text-gray-800">Manajemen User</h5>
            <button class="px-4 py-2 bg-success text-white rounded-lg hover:bg-green-700 text-sm flex items-center gap-2" onclick="openModalTambah()"><i class="fas fa-plus"></i> Tambah User</button>
          </div>

          <?php if (isset($_GET['success'])): ?>
          <div class="mb-4 p-4 rounded-lg <?= $_GET['success'] == 'tambah' ? 'bg-green-100 text-green-800' : ($_GET['success'] == 'update' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
            <?php
            $message = '';
            switch ($_GET['success']) {
              case 'tambah':
                $message = 'User berhasil ditambahkan!';
                break;
              case 'update':
                $message = 'User berhasil diperbarui!';
                break;
              case 'hapus':
                $message = 'User berhasil dihapus!';
                break;
            }
            echo $message;
            ?>
          </div>
          <?php endif; ?>

          <?php if (isset($_GET['error'])): ?>
          <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800">
            <?php
            $message = '';
            switch ($_GET['error']) {
              case 'username_exists':
                $message = 'Username sudah digunakan!';
                break;
              case 'user_has_events':
                $message = 'User tidak dapat dihapus karena memiliki event!';
                break;
              case 'query':
                $message = 'Terjadi kesalahan pada database!';
                break;
            }
            echo $message;
            ?>
          </div>
          <?php endif; ?>

          <div class="bg-white rounded-xl shadow-md overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-100">
                <tr>
                  <th class="py-3 px-4 text-left">Nama Lengkap</th>
                  <th class="py-3 px-4 text-left">Username</th>
                  <th class="py-3 px-4 text-left">Ekskul</th>
                  <th class="py-3 px-4 text-left">Role</th>
                  <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                include '../config/db.php';

                // Ambil data user
                $users = mysqli_query($conn, "SELECT * FROM users");
                while($row = mysqli_fetch_assoc($users)):
                ?>
                <tr class="border-b">
                  <td class="py-3 px-4"><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['username'] ?? '-') ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['ekskul'] ?? '-') ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['role'] ?? '') ?></td>
                  <td class="py-3 px-4 flex gap-2">
                    <button onclick='openModalEdit(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="px-3 py-1 bg-warning text-white rounded bg-yellow-600 text-xs flex items-center gap-1">
                      <i class="fas fa-edit"></i> Edit
                    </button>
                    <a href="../controllers/user_controller.php?hapus=<?= $row['user_id'] ?>" class="px-3 py-1 bg-danger text-white rounded hover:bg-red-700 text-xs flex items-center gap-1" onclick="return confirm('Yakin hapus user?')">
                      <i class="fas fa-trash"></i> Hapus
                    </a>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
  <!-- Modal Tambah User -->
  <div id="modalTambahUser" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
      <button onclick="closeModalTambah()" class="absolute top-3 right-3 text-gray-400 hover:text-danger">
        <i class="fas fa-times text-xl"></i>
      </button>
      <h2 class="text-xl font-bold mb-4">Tambah User</h2>
      <form action="../controllers/user_controller.php" method="POST">
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
          <input type="text" name="nama_lengkap" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Username</label>
          <input type="text" name="username" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Password</label>
          <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Ekskul</label>
          <input type="text" name="ekskul" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Role</label>
          <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <button type="button" onclick="closeModalTambah()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
          <button type="submit" name="tambah" class="px-4 py-2 bg-success text-white rounded hover:bg-green-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  <!-- Modal Edit User -->
  <div id="modalEditUser" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
      <button onclick="closeModalEdit()" class="absolute top-3 right-3 text-gray-400 hover:text-danger">
        <i class="fas fa-times text-xl"></i>
      </button>
      <h2 class="text-xl font-bold mb-4">Edit User</h2>
      <form action="../controllers/user_controller.php" method="POST">
        <input type="hidden" name="user_id" id="editUserId">
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
          <input type="text" name="nama_lengkap" id="editNamaLengkap" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Username</label>
          <input type="text" name="username" id="editUsername" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Password</label>
          <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Kosongkan jika tidak ingin mengubah password">
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Ekskul</label>
          <input type="text" name="ekskul" id="editEkskul" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Role</label>
          <select name="role" id="editRole" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <button type="button" onclick="closeModalEdit()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
          <button type="submit" name="update" class="px-4 py-2 bg-warning text-white rounded bg-yellow-600">Update</button>
        </div>
      </form>
    </div>
  </div>
  <script>
    // Sidebar toggle (mobile)
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.querySelector('.w-64').classList.toggle('hidden');
    });
    // Modal logic
    function openModalTambah() {
      document.getElementById('modalTambahUser').classList.remove('hidden');
    }
    function closeModalTambah() {
      document.getElementById('modalTambahUser').classList.add('hidden');
    }
    function openModalEdit(user) {
      document.getElementById('editUserId').value = user.user_id;
      document.getElementById('editNamaLengkap').value = user.nama_lengkap;
      document.getElementById('editUsername').value = user.username;
      document.getElementById('editEkskul').value = user.ekskul;
      document.getElementById('editRole').value = user.role;
      document.getElementById('modalEditUser').classList.remove('hidden');
    }
    function closeModalEdit() {
      document.getElementById('modalEditUser').classList.add('hidden');
    }
  </script>
</body>
</html>