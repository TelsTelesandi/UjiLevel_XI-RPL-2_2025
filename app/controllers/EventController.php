<?php

class EventController {
    private $db;
    private $proposalModel;
    private $eventModel;

    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } elseif ($db instanceof Database) {
            $this->db = $db->getConnection();
        } else {
            throw new Exception("Invalid database connection");
        }
        
        // Load models
        require_once __DIR__ . '/../models/EventModel.php';
        require_once __DIR__ . '/../models/ProposalModel.php';
        
        $this->eventModel = new EventModel($this->db);
        $this->proposalModel = new ProposalModel($this->db);
    }

    public function getAllEvents() {
        try {
            $events = $this->db->query(
                "SELECT * FROM event_pengajuan ORDER BY tanggal_pengajuan DESC"
            );
            echo json_encode($events);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getEvent($id) {
        try {
            if (!$id) {
                throw new Exception("ID is required");
            }
            
            $stmt = $this->db->prepare("SELECT * FROM event_pengajuan WHERE event_id = :id");
            $stmt->execute(['id' => $id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) {
                http_response_code(404);
                echo json_encode(['error' => 'Event not found']);
                return;
            }
            
            echo json_encode($event);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateEventStatus() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['event_id']) || !isset($data['status'])) {
                throw new Exception("Event ID and status are required");
            }
            
            $stmt = $this->db->prepare(
                "UPDATE event_pengajuan 
                 SET status = :status 
                 WHERE event_id = :event_id"
            );
            
            $stmt->execute([
                'event_id' => $data['event_id'],
                'status' => $data['status']
            ]);
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getDashboardStats() {
        try {
            // Get total users
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users");
            $stmt->execute();
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Get event stats
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_events,
                    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as pending_events,
                    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as approved_events
                FROM event_pengajuan
            ");
            $stmt->execute();
            $eventStats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stats = [
                'total_users' => $totalUsers,
                'total_events' => $eventStats['total_events'] ?? 0,
                'pending_events' => $eventStats['pending_events'] ?? 0,
                'approved_events' => $eventStats['approved_events'] ?? 0
            ];
            
            echo json_encode($stats);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getEventStatusStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as rejected
                FROM event_pengajuan
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stats = [
                'pending' => $result['pending'] ?? 0,
                'approved' => $result['approved'] ?? 0,
                'rejected' => $result['rejected'] ?? 0
            ];
            
            echo json_encode($stats);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getMonthlyEventStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as total
                FROM event_pengajuan
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting monthly stats: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentActivities() {
        try {
            $activities = [];
            
            // Get recent events
            $recentEvents = $this->db->query(
                "SELECT event_id, judul_event, status, tanggal_pengajuan 
                 FROM event_pengajuan 
                 ORDER BY tanggal_pengajuan DESC 
                 LIMIT 5"
            );
            
            foreach ($recentEvents as $event) {
                $activities[] = [
                    'type' => 'event',
                    'description' => "Event '{$event['judul_event']}' {$event['status']}",
                    'timestamp' => $event['tanggal_pengajuan']
                ];
            }
            
            echo json_encode($activities);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function addEvent() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $requiredFields = ['judul_event', 'ekskul', 'jenis_kegiatan', 'total_pembiayaan'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new Exception("Field $field is required");
                }
            }
            
            // Set default status to 'menunggu'
            $data['status'] = 'menunggu';
            $data['tanggal_pengajuan'] = date('Y-m-d H:i:s');
            $data['user_id'] = $_SESSION['user_id'];
            
            $fields = implode(', ', array_keys($data));
            $values = ':' . implode(', :', array_keys($data));
            
            $stmt = $this->db->prepare(
                "INSERT INTO event_pengajuan ($fields) VALUES ($values)"
            );
            
            $stmt->execute($data);
            
            echo json_encode([
                'success' => true,
                'event_id' => $this->db->lastInsertId()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAllVerifications() {
        $stmt = $this->db->prepare("SELECT * FROM verifikasi_event ORDER BY tanggal_verifikasi DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserDashboardStats($user_id) {
        try {
            // Get user's events statistics
            $stats = [
                'total_pengajuan' => 0,
                'menunggu' => 0,
                'closed' => 0
            ];

            $statsQuery = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
                SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui
                FROM event_pengajuan 
                WHERE user_id = :user_id";
            
            $stmt = $this->db->prepare($statsQuery);
            $stmt->execute(['user_id' => $user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stats['total_pengajuan'] = $result['total'] ?? 0;
            $stats['menunggu'] = $result['menunggu'] ?? 0;
            $stats['closed'] = $result['disetujui'] ?? 0;

            // Get user's recent events
            $eventsQuery = "SELECT * FROM event_pengajuan 
                          WHERE user_id = :user_id 
                          ORDER BY tanggal_pengajuan DESC";
            
            $stmt = $this->db->prepare($eventsQuery);
            $stmt->execute(['user_id' => $user_id]);
            $recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'stats' => $stats,
                'recent_events' => $recent_events
            ];
        } catch (Exception $e) {
            error_log("Error in getUserDashboardStats: " . $e->getMessage());
            return [
                'stats' => [
                    'total_pengajuan' => 0,
                    'menunggu' => 0,
                    'closed' => 0
                ],
                'recent_events' => []
            ];
        }
    }

    public function submitEvent($postData, $files) {
        try {
            // Debug input
            error_log("=== DEBUG SUBMIT EVENT ===");
            error_log("POST data: " . print_r($postData, true));
            error_log("FILES data: " . print_r($files, true));

            // Validasi input
            if (empty($postData['judul_event']) || empty($postData['jenis_kegiatan']) || 
                empty($postData['total_pembiayaan']) || empty($postData['deskripsi'])) {
                throw new Exception("Semua field harus diisi");
            }

            // Validasi file
            if (!isset($files['file_proposal']) || $files['file_proposal']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("File proposal harus diupload");
            }

            $file = $files['file_proposal'];

            // Validasi tipe file
            $allowedTypes = ['application/pdf'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $file['tmp_name']);
            finfo_close($fileInfo);

            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception("File harus berformat PDF");
            }

            // Validasi ukuran file (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception("Ukuran file maksimal 5MB");
            }

            // Buat direktori uploads jika belum ada
            $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/proposals';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate nama file unik
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFilename = uniqid('proposal_') . '.' . $extension;
            $uploadPath = $uploadDir . '/' . $newFilename;

            // Upload file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception("Gagal mengupload file");
            }

            // Format total pembiayaan
            $total_pembiayaan = str_replace([',', '.'], '', $postData['total_pembiayaan']);
            $total_pembiayaan = (float)$total_pembiayaan;

            if ($total_pembiayaan <= 0) {
                throw new Exception("Total pembiayaan harus lebih dari 0");
            }

            // Siapkan data untuk EventModel
            $eventData = [
                'user_id' => $_SESSION['user_id'],
                'judul_event' => $postData['judul_event'],
                'jenis_kegiatan' => $postData['jenis_kegiatan'],
                'deskripsi' => $postData['deskripsi'],
                'total_pembiayaan' => $total_pembiayaan,
                'file_proposal' => 'uploads/proposals/' . $newFilename
            ];

            // Simpan event menggunakan EventModel
            if ($this->eventModel->createEvent($eventData)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Event berhasil diajukan',
                    'data' => [
                        'event_id' => $this->db->lastInsertId(),
                        'judul_event' => $eventData['judul_event']
                    ]
                ]);
                exit();
            } else {
                throw new Exception("Gagal menyimpan data event");
            }

        } catch (Exception $e) {
            error_log("Error in submitEvent: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit();
        }
    }

    public function updateEvent() {
        try {
            // Debug input
            error_log("=== DEBUG UPDATE EVENT ===");
            error_log("POST data: " . print_r($_POST, true));
            error_log("FILES data: " . print_r($_FILES, true));

            // Validasi input
            if (!isset($_POST['event_id']) || !isset($_POST['judul_event']) || 
                !isset($_POST['jenis_kegiatan']) || !isset($_POST['total_pembiayaan'])) {
                throw new Exception("Semua field harus diisi!");
            }

            $event_id = (int)$_POST['event_id'];
            
            // Validasi event exists
            $stmt = $this->db->prepare("SELECT * FROM event_pengajuan WHERE event_id = ?");
            $stmt->execute([$event_id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) {
                throw new Exception("Event tidak ditemukan");
            }

            // Format total pembiayaan
            $total_pembiayaan = str_replace([',', '.'], '', $_POST['total_pembiayaan']);
            $total_pembiayaan = (float)$total_pembiayaan;

            if ($total_pembiayaan <= 0) {
                throw new Exception("Total pembiayaan harus lebih dari 0");
            }

            // Begin transaction
            $this->db->beginTransaction();

            try {
                // Handle file upload if exists
                $file_proposal = $event['file_proposal']; // Default to existing file_proposal
                if (isset($_FILES['file_proposal']) && $_FILES['file_proposal']['size'] > 0) {
                    // Validate file
                    $file = $_FILES['file_proposal'];
                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        throw new Exception("Error uploading file");
                    }

                    // Validate file type
                    $allowedTypes = ['application/pdf'];
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($fileInfo, $file['tmp_name']);
                    finfo_close($fileInfo);

                    if (!in_array($mimeType, $allowedTypes)) {
                        throw new Exception("File harus berformat PDF");
                    }

                    // Validate file size (max 5MB)
                    if ($file['size'] > 5 * 1024 * 1024) {
                        throw new Exception("Ukuran file maksimal 5MB");
                    }

                    // Generate unique filename
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $newFilename = uniqid('proposal_') . '.' . $extension;
                    $uploadPath = dirname(dirname(__DIR__)) . '/public/uploads/proposals/' . $newFilename;

                    // Delete old file if exists
                    if ($event['file_proposal']) {
                        $oldFilePath = dirname(dirname(__DIR__)) . '/public/uploads/proposals/' . $event['file_proposal'];
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }

                    // Move uploaded file
                    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        throw new Exception("Gagal mengupload file");
                    }

                    $file_proposal = 'uploads/proposals/' . $newFilename;
                }

                // Update event data
                $stmt = $this->db->prepare("
                    UPDATE event_pengajuan 
                    SET judul_event = :judul,
                        jenis_kegiatan = :jenis,
                        total_pembiayaan = :biaya,
                        deskripsi = :deskripsi,
                        file_proposal = :file_proposal,
                        tanggal_update = NOW()
                    WHERE event_id = :id
                ");

                $params = [
                    ':judul' => $_POST['judul_event'],
                    ':jenis' => $_POST['jenis_kegiatan'],
                    ':biaya' => $total_pembiayaan,
                    ':deskripsi' => $_POST['deskripsi'],
                    ':file_proposal' => $file_proposal,
                    ':id' => $event_id
                ];

                if (!$stmt->execute($params)) {
                    throw new Exception("Gagal mengupdate data");
                }

                $this->db->commit();

                // Return success response
                echo json_encode([
                    'success' => true,
                    'message' => 'Event berhasil diupdate!',
                    'data' => [
                        'event_id' => $event_id,
                        'judul_event' => $_POST['judul_event'],
                        'jenis_kegiatan' => $_POST['jenis_kegiatan'],
                        'total_pembiayaan' => $total_pembiayaan,
                        'file_proposal' => $file_proposal
                    ]
                ]);
                exit;

            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Error in updateEvent: " . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function deleteEvent($eventId) {
        try {
            // Validasi kepemilikan event
            $query = "SELECT * FROM event_pengajuan WHERE event_id = ? AND user_id = ? AND status = 'menunggu'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$eventId, $_SESSION['user_id']]);
            $event = $stmt->fetch();

            if (!$event) {
                throw new Exception("Event tidak ditemukan atau tidak dapat dihapus");
            }

            // Hapus file proposal jika ada
            if (file_exists($event['file_proposal'])) {
                unlink($event['file_proposal']);
            }

            // Hapus data event dari database
            $query = "DELETE FROM event_pengajuan WHERE event_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$eventId]);

            echo json_encode(['success' => true]);
            exit();

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit();
        }
    }

    public function getTotalEvents() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM event_pengajuan");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error getting total events: " . $e->getMessage());
            return 0;
        }
    }

    public function getPendingEvents() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as pending FROM event_pengajuan WHERE status = 'menunggu'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['pending'];
        } catch (PDOException $e) {
            error_log("Error getting pending events: " . $e->getMessage());
            return 0;
        }
    }

    public function getRecentEvents($limit = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT ep.*, u.username as organizer 
                FROM event_pengajuan ep 
                LEFT JOIN users u ON ep.user_id = u.id 
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

    public function verifyEvent() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

        try {
            $data = $_POST;
            
            if (!isset($data['event_id']) || !isset($data['status'])) {
                throw new Exception('Data tidak lengkap');
            }

            $this->db->beginTransaction();

            // Update status event di tabel event_pengajuan
            $stmt = $this->db->prepare("UPDATE event_pengajuan 
                                      SET status = :status,
                                          tanggal_update = NOW() 
                                      WHERE event_id = :event_id");
            
            if (!$stmt->execute([
                'status' => $data['status'],
                'event_id' => $data['event_id']
            ])) {
                throw new Exception('Gagal mengupdate status event');
            }

            // Buat record verifikasi di tabel verifikasi_event
            $stmt = $this->db->prepare("INSERT INTO verifikasi_event 
                                      (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) 
                                      VALUES 
                                      (:event_id, :admin_id, NOW(), :catatan_admin, 'closed')");
            
            if (!$stmt->execute([
                'event_id' => $data['event_id'],
                'admin_id' => $_SESSION['user_id'],
                'catatan_admin' => $data['catatan_admin'] ?? ''
            ])) {
                throw new Exception('Gagal menyimpan data verifikasi');
            }

            $this->db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Event berhasil diverifikasi'
            ]);
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function closeEvent($eventId) {
        try {
            // Validasi kepemilikan event dan status
            $query = "SELECT * FROM event_pengajuan WHERE event_id = ? AND user_id = ? AND status = 'disetujui'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$eventId, $_SESSION['user_id']]);
            $event = $stmt->fetch();

            if (!$event) {
                throw new Exception("Event tidak ditemukan atau tidak dapat ditutup");
            }

            // Update status event menjadi selesai
            $query = "UPDATE event_pengajuan SET status = 'selesai', tanggal_selesai = CURRENT_TIMESTAMP WHERE event_id = ?";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt->execute([$eventId])) {
                throw new Exception("Gagal mengupdate status event");
            }

            echo json_encode([
                'success' => true,
                'message' => 'Event berhasil diselesaikan'
            ]);
            exit();

        } catch (Exception $e) {
            error_log("Error in closeEvent: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menyelesaikan event: ' . $e->getMessage()
            ]);
            exit();
        }
    }
} 