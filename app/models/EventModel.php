<?php

class EventModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createEvent($data) {
        try {
            $query = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, deskripsi, 
                     total_pembiayaan, file_proposal, status, tanggal_pengajuan) 
                     VALUES (?, ?, ?, ?, ?, ?, 'menunggu', NOW())";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['user_id'],
                $data['judul_event'],
                $data['jenis_kegiatan'],
                $data['deskripsi'],
                $data['total_pembiayaan'],
                $data['file_proposal']
            ]);
        } catch (PDOException $e) {
            error_log("Error in createEvent: " . $e->getMessage());
            throw $e;
        }
    }

    public function getEventById($eventId) {
        try {
            $query = "SELECT * FROM event_pengajuan WHERE event_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$eventId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getEventById: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateEvent($eventId, $data) {
        try {
            $query = "UPDATE event_pengajuan 
                     SET judul_event = ?, 
                         jenis_kegiatan = ?, 
                         deskripsi = ?,
                         total_pembiayaan = ?,
                         file_proposal = ?,
                         tanggal_update = NOW()
                     WHERE event_id = ?";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['judul_event'],
                $data['jenis_kegiatan'],
                $data['deskripsi'],
                $data['total_pembiayaan'],
                $data['file_proposal'],
                $eventId
            ]);
        } catch (PDOException $e) {
            error_log("Error in updateEvent: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteEvent($eventId) {
        try {
            $query = "DELETE FROM event_pengajuan WHERE event_id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$eventId]);
        } catch (PDOException $e) {
            error_log("Error in deleteEvent: " . $e->getMessage());
            throw $e;
        }
    }
} 