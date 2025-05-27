<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Database connection successful!<br>";
    
    // Check tables
    $stmt = $db->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in database:<br>";
    foreach ($tables as $table) {
        echo "- $table<br>";
    }
    
    // Check if event_pengajuan table exists and has records
    if (in_array('event_pengajuan', $tables)) {
        $stmt = $db->query('SELECT COUNT(*) FROM event_pengajuan');
        $count = $stmt->fetchColumn();
        echo "event_pengajuan table exists with $count records<br>";
    } else {
        echo "event_pengajuan table does not exist!<br>";
    }
    
    // Check if verifikasi_event table exists
    if (in_array('verifikasi_event', $tables)) {
        echo "verifikasi_event table exists<br>";
    } else {
        echo "verifikasi_event table does not exist!<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 