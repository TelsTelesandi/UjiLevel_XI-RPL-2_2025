<?php
class Database {
    private $host = "localhost";
    private $db_name = "re_dilan";
    private $username = "root";
    private $password = "";
    private $conn = null;

    public function getConnection() {
        try {
            if ($this->conn === null) {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                error_log("Database connection established successfully");
            }
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Koneksi database gagal: " . $e->getMessage());
        }
    }
} 