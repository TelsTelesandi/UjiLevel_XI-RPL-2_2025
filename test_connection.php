<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Database Connection</h2>";

// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$db   = "re_fachri";

try {
    // Test connection
    echo "<p>Attempting to connect to MySQL server...</p>";
    $conn = mysqli_connect($host, $user, $pass);
    
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    echo "<p style='color: green;'>✓ Successfully connected to MySQL server</p>";
    
    // Check if database exists
    echo "<p>Checking if database '$db' exists...</p>";
    $check_db = mysqli_query($conn, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'");
    
    if (mysqli_num_rows($check_db) == 0) {
        echo "<p style='color: red;'>✗ Database '$db' does not exist!</p>";
    } else {
        echo "<p style='color: green;'>✓ Database '$db' exists</p>";
        
        // Select database
        if (!mysqli_select_db($conn, $db)) {
            throw new Exception("Failed to select database: " . mysqli_error($conn));
        }
        
        // Check users table
        echo "<p>Checking users table...</p>";
        $result = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
        if (mysqli_num_rows($result) == 0) {
            echo "<p style='color: red;'>✗ Users table does not exist!</p>";
        } else {
            echo "<p style='color: green;'>✓ Users table exists</p>";
            
            // Check users table structure
            $result = mysqli_query($conn, "DESCRIBE users");
            echo "<h3>Users Table Structure:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . $row['Default'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Check if admin user exists
            $result = mysqli_query($conn, "SELECT * FROM users WHERE username = 'admin'");
            if (mysqli_num_rows($result) == 0) {
                echo "<p style='color: red;'>✗ Admin user does not exist!</p>";
            } else {
                echo "<p style='color: green;'>✓ Admin user exists</p>";
            }
        }
        
        // Check event_pengajuan table
        echo "<p>Checking event_pengajuan table...</p>";
        $result = mysqli_query($conn, "SHOW TABLES LIKE 'event_pengajuan'");
        if (mysqli_num_rows($result) == 0) {
            echo "<p style='color: red;'>✗ Event_pengajuan table does not exist!</p>";
        } else {
            echo "<p style='color: green;'>✓ Event_pengajuan table exists</p>";
            
            // Check event_pengajuan table structure
            $result = mysqli_query($conn, "DESCRIBE event_pengajuan");
            echo "<h3>Event_pengajuan Table Structure:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . $row['Default'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Count events
            $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan");
            $row = mysqli_fetch_assoc($result);
            echo "<p>Total events in database: " . $row['total'] . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?> 