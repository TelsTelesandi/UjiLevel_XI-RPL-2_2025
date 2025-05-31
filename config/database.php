<?php

class Database {
    private $host = "localhost";
    private $database_name = "re_dilan";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Koneksi database gagal: " . $e->getMessage();
        }
        return $this->conn;
    }

    public function count($table, $conditions = []) {
        try {
            $sql = "SELECT COUNT(*) as count FROM " . $table;
            
            if (!empty($conditions)) {
                $sql .= " WHERE ";
                $whereClauses = [];
                foreach ($conditions as $key => $value) {
                    $whereClauses[] = "$key = :$key";
                }
                $sql .= implode(" AND ", $whereClauses);
            }
            
            $stmt = $this->conn->prepare($sql);
            
            if (!empty($conditions)) {
                foreach ($conditions as $key => $value) {
                    $stmt->bindValue(":$key", $value);
                }
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log("Count error: " . $e->getMessage());
            throw $e;
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            throw $e;
        }
    }
}

function getDatabaseConnection() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 're_dilan';

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }
    return $conn;
}

$pdo = new PDO("mysql:host=localhost;dbname=re_dilan", "root", "");
// Ganti username dan password sesuai database kamu
?> 