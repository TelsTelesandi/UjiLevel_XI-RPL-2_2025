<?php
if (!class_exists('UserModel')) {
    class UserModel {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        public function getUserByUsername($username) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->execute([$username]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error in getUserByUsername: " . $e->getMessage());
                return false;
            }
        }

        public function userExists($username) {
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
                $stmt->execute([$username]);
                return $stmt->fetchColumn() > 0;
            } catch (PDOException $e) {
                error_log("Error in userExists: " . $e->getMessage());
                return false;
            }
        }

        public function createUser($username, $password, $nama_lengkap, $ekskul, $role = 'user') {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $this->db->prepare("INSERT INTO users (username, password, nama_lengkap, ekskul, role) VALUES (?, ?, ?, ?, ?)");
                return $stmt->execute([$username, $hashed_password, $nama_lengkap, $ekskul, $role]);
            } catch (PDOException $e) {
                error_log("Error in createUser: " . $e->getMessage());
                return false;
            }
        }

        public function updateUser($user_id, $data) {
            try {
                $fields = [];
                $values = [];
                
                // Normalize ekskul field name
                if (isset($data['Ekskul'])) {
                    $data['ekskul'] = $data['Ekskul'];
                    unset($data['Ekskul']);
                }
                
                foreach ($data as $key => $value) {
                    if ($key === 'password' && !empty($value)) {
                        $value = password_hash($value, PASSWORD_DEFAULT);
                    }
                    $fields[] = "$key = ?";
                    $values[] = $value;
                }
                
                $values[] = $user_id;
                $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = ?";
                
                $stmt = $this->db->prepare($sql);
                $success = $stmt->execute($values);
                
                if (!$success) {
                    error_log("Error in updateUser: Execute failed");
                    return false;
                }
                
                return true;
            } catch (PDOException $e) {
                error_log("Error in updateUser: " . $e->getMessage());
                return false;
            }
        }

        public function deleteUser($user_id) {
            try {
                // Validasi ID
                $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
                if (!$user_id) {
                    error_log("UserModel: Invalid user ID format: " . var_export($user_id, true));
                    return false;
                }

                // Mulai transaksi
                $this->db->beginTransaction();

                try {
                    // Cek apakah user ada
                    $checkStmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = ?");
                    if (!$checkStmt->execute([$user_id])) {
                        error_log("UserModel: Check query failed. Error info: " . json_encode($checkStmt->errorInfo()));
                        throw new Exception("Gagal memeriksa user");
                    }

                    if (!$checkStmt->fetch()) {
                        error_log("UserModel: User not found with ID: " . $user_id);
                        throw new Exception("User tidak ditemukan");
                    }

                    // Hapus user
                    $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
                    if (!$stmt->execute([$user_id])) {
                        error_log("UserModel: Delete query failed. Error info: " . json_encode($stmt->errorInfo()));
                        throw new Exception("Gagal menghapus user");
                    }

                    // Commit transaksi
                    $this->db->commit();
                    error_log("UserModel: Successfully deleted user with ID: " . $user_id);
                    return true;

                } catch (Exception $e) {
                    // Rollback jika ada error
                    $this->db->rollBack();
                    error_log("UserModel: Transaction rolled back: " . $e->getMessage());
                    throw $e;
                }
            } catch (Exception $e) {
                error_log("UserModel: Error in deleteUser: " . $e->getMessage());
                return false;
            }
        }

        public function getAllUsers() {
            try {
                $stmt = $this->db->query("SELECT * FROM users ORDER BY user_id DESC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Debug log
                error_log("UserModel getAllUsers - Raw data: " . print_r($users, true));
                
                // Normalize data
                $normalizedUsers = array_map(function($user) {
                    return [
                        'user_id' => (int)$user['user_id'],
                        'username' => $user['username'],
                        'nama_lengkap' => $user['nama_lengkap'],
                        'ekskul' => $user['ekskul'] ?? $user['Ekskul'] ?? '', // Handle both cases
                        'role' => $user['role']
                    ];
                }, $users);
                
                error_log("UserModel getAllUsers - Normalized data: " . print_r($normalizedUsers, true));
                return $normalizedUsers;
            } catch (PDOException $e) {
                error_log("Error in getAllUsers: " . $e->getMessage());
                return false;
            }
        }

        public function getUserById($id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user) {
                    return false;
                }
                
                // Debug log
                error_log("UserModel getUserById - Raw data: " . print_r($user, true));
                
                // Normalize data
                $normalizedUser = [
                    'user_id' => (int)$user['user_id'],
                    'username' => $user['username'],
                    'nama_lengkap' => $user['nama_lengkap'],
                    'ekskul' => $user['ekskul'] ?? $user['Ekskul'] ?? '', // Handle both cases
                    'role' => $user['role']
                ];
                
                error_log("UserModel getUserById - Normalized data: " . print_r($normalizedUser, true));
                return $normalizedUser;
            } catch (PDOException $e) {
                error_log("Error in getUserById: " . $e->getMessage());
                return false;
            }
        }

        public function usernameExistsExcept($username, $userId) {
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND user_id != ?");
                $stmt->execute([$username, $userId]);
                return $stmt->fetchColumn() > 0;
            } catch (PDOException $e) {
                error_log("Error in usernameExistsExcept: " . $e->getMessage());
                return false;
            }
        }

        public function getUserCount() {
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = 'user'");
                $stmt->execute();
                return $stmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("Error in getUserCount: " . $e->getMessage());
                return 0;
            }
        }

        public function getAdminCount() {
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
                $stmt->execute();
                return $stmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("Error in getAdminCount: " . $e->getMessage());
                return 0;
            }
        }
    }
} 