<?php
require_once __DIR__ . '/../models/EventModel.php';

class ProposalController {
    private $db;
    private $eventModel;

    public function __construct($db) {
        $this->db = $db;
        $this->eventModel = new EventModel($db);
    }

    // Tampilkan form upload proposal (user)
    public function uploadForm($event_id) {
        include __DIR__ . '/../views/event/upload_proposal.php';
    }

    // Proses upload proposal (user)
    public function upload($event_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['proposal'])) {
            try {
                $proposal = $_FILES['proposal'];
                if ($proposal['error'] === UPLOAD_ERR_OK) {
                    // Validasi file
                    $allowedTypes = ['application/pdf'];
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($fileInfo, $proposal['tmp_name']);
                    finfo_close($fileInfo);

                    if (!in_array($mimeType, $allowedTypes)) {
                        throw new Exception("File harus berformat PDF");
                    }

                    // Validasi ukuran (max 5MB)
                    if ($proposal['size'] > 5 * 1024 * 1024) {
                        throw new Exception("Ukuran file maksimal 5MB");
                    }

                    $proposalName = time() . '_' . basename($proposal['name']);
                    $proposalPath = 'uploads/' . $proposalName;
                    
                    if (move_uploaded_file($proposal['tmp_name'], __DIR__ . '/../../public/' . $proposalPath)) {
                        // Update proposal di database
                        $stmt = $this->db->prepare("
                            UPDATE event_pengajuan 
                            SET proposal = ?, tanggal_update = NOW() 
                            WHERE event_id = ?
                        ");
                        
                        if ($stmt->execute([$proposalName, $event_id])) {
                            header('Location: index.php?page=user_dashboard&success=1');
                            exit;
                        } else {
                            throw new Exception("Gagal menyimpan data proposal");
                        }
                    } else {
                        throw new Exception("Gagal upload file");
                    }
                } else {
                    throw new Exception("Error saat upload file");
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
                include __DIR__ . '/../views/event/upload_proposal.php';
            }
        }
    }

    // Admin melihat/mengunduh proposal
    public function viewProposal($event_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, u.nama_lengkap 
                FROM event_pengajuan e 
                JOIN users u ON e.user_id = u.user_id 
                WHERE e.event_id = ?
            ");
            $stmt->execute([$event_id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                throw new Exception("Event tidak ditemukan");
            }

            include __DIR__ . '/../views/admin/view_proposal.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=admin_dashboard');
            exit;
        }
    }
} 