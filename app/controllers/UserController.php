<?php

class UserController {
    private $conn;
    private $userModel;
    private $db;

    public function __construct($conn) {
        $this->conn = $conn;
        require_once __DIR__ . '/../models/UserModel.php';
        $this->userModel = new UserModel($conn);
        $this->db = $conn;
    }
    
    public function getUsers() {
        try {
            // Pastikan tidak ada output sebelum JSON
            ob_clean();
            
            $users = $this->userModel->getAllUsers();
            if ($users === false) {
                throw new Exception("Gagal mengambil data users");
            }
            
            $userCount = $this->userModel->getUserCount();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => [
                    'users' => $users,
                    'userCount' => $userCount
                ]
            ]);
        } catch (Exception $e) {
            // Pastikan tidak ada output sebelum JSON
            ob_clean();
            
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function getUser($id) {
        try {
            if (!$id) {
                throw new Exception("ID is required");
            }

            $user = $this->userModel->getUserById($id);
            if (!$user) {
                throw new Exception('User not found');
            }

            header('Content-Type: application/json');
            echo json_encode($user);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            header('HTTP/1.1 404 Not Found');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function addUser() {
        try {
            // Debug log untuk melihat data yang diterima
            error_log("ADD USER - POST data received: " . print_r($_POST, true));
            
            // Validasi input
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
            $ekskul = trim($_POST['ekskul'] ?? '');
            $role = trim($_POST['role'] ?? 'user');

            // Validasi field yang diperlukan
            if (empty($username)) {
                throw new Exception('Username harus diisi');
            }
            if (empty($password)) {
                throw new Exception('Password harus diisi');
            }
            if (empty($nama_lengkap)) {
                throw new Exception('Nama lengkap harus diisi');
            }
            if (empty($ekskul)) {
                throw new Exception('Ekstrakurikuler harus diisi');
            }

            // Cek apakah username sudah ada
            if ($this->userModel->userExists($username)) {
                throw new Exception('Username sudah digunakan');
            }

            // Coba tambahkan user
            $result = $this->userModel->createUser($username, $password, $nama_lengkap, $ekskul, $role);
            if (!$result) {
                throw new Exception('Gagal menambahkan user');
            }

            // Debug log untuk sukses
            error_log("ADD USER - Success adding user: $username");

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'User berhasil ditambahkan'
            ]);
        } catch (Exception $e) {
            error_log("ADD USER - Error: " . $e->getMessage());
            
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function updateUser($id) {
        try {
            if (!$id) {
                throw new Exception("ID user tidak ditemukan");
            }

            // Validasi input
            $username = $_POST['username'] ?? '';
            $nama_lengkap = $_POST['nama_lengkap'] ?? '';
            $ekskul = $_POST['ekskul'] ?? '';
            $role = $_POST['role'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($nama_lengkap) || empty($ekskul) || empty($role)) {
                throw new Exception("Semua field harus diisi kecuali password");
            }

            // Siapkan data untuk update
            $data = [
                'username' => $username,
                'nama_lengkap' => $nama_lengkap,
                'Ekskul' => $ekskul,
                'role' => $role
            ];

            // Tambahkan password jika diisi
            if (!empty($password)) {
                $data['password'] = $password;
            }

            // Cek username sudah ada atau belum (kecuali untuk user yang sedang diedit)
            if ($this->userModel->usernameExistsExcept($username, $id)) {
                throw new Exception("Username sudah digunakan");
            }

            // Cek jika mengubah role admin terakhir
            $user = $this->userModel->getUserById($id);
            if ($user['role'] === 'admin' && $role !== 'admin') {
                $adminCount = $this->userModel->getAdminCount();
                if ($adminCount <= 1) {
                    throw new Exception("Tidak dapat mengubah role admin terakhir");
                }
            }

            $success = $this->userModel->updateUser($id, $data);
            
            if (!$success) {
                throw new Exception("Gagal mengupdate user");
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'User berhasil diupdate'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function deleteUser($id) {
        try {
            // Pastikan ID ada dan valid
            if (!$id || !is_numeric($id)) {
                throw new Exception("ID user tidak valid");
            }

            // Cek apakah user ada
            $user = $this->userModel->getUserById($id);
            if (!$user) {
                throw new Exception("User tidak ditemukan");
            }

            // Cek jika menghapus admin terakhir
            if ($user['role'] === 'admin') {
                $adminCount = $this->userModel->getAdminCount();
                if ($adminCount <= 1) {
                    throw new Exception("Tidak dapat menghapus admin terakhir");
                }
            }

            // Mulai transaksi
            $this->db->beginTransaction();

            try {
                // 1. Ambil semua event_id yang dimiliki user
                $stmt = $this->db->prepare("SELECT event_id FROM event_pengajuan WHERE user_id = ?");
                $stmt->execute([$id]);
                $events = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // 2. Hapus verifikasi_event untuk semua event yang dimiliki user
                if (!empty($events)) {
                    $placeholders = str_repeat('?,', count($events) - 1) . '?';
                    $stmt = $this->db->prepare("DELETE FROM verifikasi_event WHERE event_id IN ($placeholders)");
                    $stmt->execute($events);
                }

                // 3. Hapus verifikasi_event dimana user sebagai admin
                $stmt = $this->db->prepare("DELETE FROM verifikasi_event WHERE admin_id = ?");
                $stmt->execute([$id]);

                // 4. Hapus event_pengajuan
                $stmt = $this->db->prepare("DELETE FROM event_pengajuan WHERE user_id = ?");
                $stmt->execute([$id]);

                // 5. Terakhir hapus user
                $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
                if (!$stmt->execute([$id])) {
                    throw new Exception("Gagal menghapus user");
                }

                // Commit transaksi
                $this->db->commit();

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'User berhasil dihapus'
                ]);

            } catch (Exception $e) {
                // Rollback jika terjadi error
                $this->db->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: ./index.php?action=login");
        exit;
    }

    public function getAllUsers() {
        try {
            return $this->userModel->getAllUsers();
        } catch (Exception $e) {
            return [];
        }
    }
} 