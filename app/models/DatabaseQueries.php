<?php
class DatabaseQueries {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Query untuk mendapatkan semua event yang belum diverifikasi
    public function getPendingEvents() {
        $query = "SELECT e.*, u.nama_lengkap, u.ekskul 
                 FROM event_pengajuan e 
                 JOIN users u ON e.user_id = u.user_id 
                 ORDER BY e.tanggal_pengajuan DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Query untuk mendapatkan event by ID
    public function getEventById($eventId) {
        $query = "SELECT e.*, u.nama_lengkap, u.ekskul 
                 FROM event_pengajuan e 
                 JOIN users u ON e.user_id = u.user_id 
                 WHERE e.event_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$eventId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Query untuk update status event
    public function updateEventStatus($eventId, $status, $keterangan = null) {
        $query = "UPDATE event_pengajuan 
                 SET status = ?, keterangan = ? 
                 WHERE event_id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $keterangan, $eventId]);
    }

    // Query untuk mendapatkan statistik dashboard
    public function getDashboardStats() {
        $stats = [
            'total_events' => 0,
            'pending_events' => 0,
            'approved_events' => 0,
            'rejected_events' => 0
        ];

        // Get total events
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as rejected
                 FROM event_pengajuan";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $stats['total_events'] = $result['total'];
            $stats['pending_events'] = $result['pending'];
            $stats['approved_events'] = $result['approved'];
            $stats['rejected_events'] = $result['rejected'];
        }

        return $stats;
    }

    // Query untuk mendapatkan statistik user
    public function getUserStats($userId) {
        try {
            $query = "SELECT 
                COUNT(*) as total_pengajuan,
                SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
                SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
                FROM event_pengajuan 
                WHERE user_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_pengajuan' => $result['total_pengajuan'] ?? 0,
                'menunggu' => $result['menunggu'] ?? 0,
                'disetujui' => $result['disetujui'] ?? 0,
                'ditolak' => $result['ditolak'] ?? 0
            ];
        } catch (Exception $e) {
            error_log("Error in getUserStats: " . $e->getMessage());
            return [
                'total_pengajuan' => 0,
                'menunggu' => 0,
                'disetujui' => 0,
                'ditolak' => 0
            ];
        }
    }

    // Query untuk mendapatkan semua event user
    public function getUserEvents($userId) {
        $query = "SELECT * FROM event_pengajuan WHERE user_id = ? ORDER BY tanggal_pengajuan DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verifyEvent($eventId, $adminId, $status, $keterangan = '') {
        try {
            $this->db->beginTransaction();

            // Update status di tabel event_pengajuan
            $query = "UPDATE event_pengajuan SET status = ? WHERE event_id = ?";
            $stmt = $this->db->prepare($query);
            $eventStatus = ($status === 'disetujui') ? 'disetujui' : 'ditolak';
            $stmt->execute([$eventStatus, $eventId]);

            // Insert ke tabel verifikasi_event
            $query = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, status) 
                     VALUES (?, ?, NOW(), ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$eventId, $adminId, $keterangan, $status]);

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function addUser($data) {
        try {
            $query = "INSERT INTO users (username, password, role, nama_lengkap, ekskul) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['username'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role'],
                $data['nama_lengkap'],
                $data['ekskul']
            ]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateUser($data) {
        try {
            if (empty($data['password'])) {
                $query = "UPDATE users SET username = ?, role = ?, nama_lengkap = ?, ekskul = ? 
                         WHERE user_id = ?";
                $params = [
                    $data['username'],
                    $data['role'],
                    $data['nama_lengkap'],
                    $data['ekskul'],
                    $data['user_id']
                ];
            } else {
                $query = "UPDATE users SET username = ?, password = ?, role = ?, nama_lengkap = ?, ekskul = ? 
                         WHERE user_id = ?";
                $params = [
                    $data['username'],
                    password_hash($data['password'], PASSWORD_DEFAULT),
                    $data['role'],
                    $data['nama_lengkap'],
                    $data['ekskul'],
                    $data['user_id']
                ];
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteUser($userId) {
        try {
            $query = "DELETE FROM users WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Query untuk mendapatkan semua users
    public function getUsers() {
        $query = "SELECT * FROM users ORDER BY user_id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function loginUser($username, $password) {
        try {
            error_log("Attempting to login user: " . $username);
            
            $query = "SELECT user_id, username, password, role FROM users WHERE username = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("User data from database: " . print_r($user, true));

            if ($user) {
                $passwordValid = password_verify($password, $user['password']);
                error_log("Password verification result: " . ($passwordValid ? "valid" : "invalid"));
                
                if ($passwordValid) {
                    error_log("Login successful for user: " . $username);
                    return [
                        'success' => true,
                        'user_id' => $user['user_id'],
                        'role' => $user['role']
                    ];
                }
            }

            error_log("Login failed for user: " . $username);
            return ['success' => false, 'message' => 'Username atau password salah'];
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getRecentEvents($limit = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT ep.*, u.username as organizer 
                FROM event_pengajuan ep 
                LEFT JOIN users u ON ep.user_id = u.user_id 
                ORDER BY ep.tanggal_pengajuan DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting recent events: " . $e->getMessage());
            return [];
        }
    }

    public function getMonthlyEventStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE_FORMAT(tanggal_pengajuan, '%Y-%m') as month,
                    COUNT(*) as total
                FROM event_pengajuan
                WHERE tanggal_pengajuan >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(tanggal_pengajuan, '%Y-%m')
                ORDER BY month ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting monthly stats: " . $e->getMessage());
            return [];
        }
    }
}
?> 