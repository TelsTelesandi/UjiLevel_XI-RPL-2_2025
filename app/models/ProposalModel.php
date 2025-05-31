<?php

class ProposalModel {
    private $db;
    private $uploadDir = 'uploads/proposals/';

    public function __construct($db) {
        $this->db = $db;
        
        // Buat direktori upload jika belum ada
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function uploadProposal($file) {
        try {
            if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("File proposal harus diunggah");
            }

            // Validasi tipe file
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (strtolower($fileExtension) !== 'pdf') {
                throw new Exception("File harus berformat PDF");
            }

            // Generate nama file unik
            $fileName = uniqid() . '_' . time() . '.pdf';
            $uploadPath = $this->uploadDir . $fileName;

            // Pindahkan file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception("Gagal mengunggah file");
            }

            return $uploadPath;
        } catch (Exception $e) {
            error_log("Error in uploadProposal: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProposal($filePath) {
        try {
            if (file_exists($filePath)) {
                unlink($filePath);
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error in deleteProposal: " . $e->getMessage());
            throw $e;
        }
    }
} 