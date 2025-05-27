<?php
/*
 * File: db_connect.php
 * Description: Establishes a database connection using MySQLi.
 */

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // <-- Ganti dengan username database Anda
define('DB_PASSWORD', ''); // <-- Ganti dengan password database Anda
define('DB_NAME', 'pengajuan_event_eskulll');

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Optional: Set charset to utf8mb4
mysqli_set_charset($link, "utf8mb4");

?> 