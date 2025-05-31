<?php
// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session di awal
session_start();

// Debug: Cek isi session
error_log("Session contents: " . print_r($_SESSION, true));

// Include semua file yang dibutuhkan
require_once 'app/config/database.php';
require_once 'app/models/DatabaseQueries.php';
require_once 'app/controllers/EventController.php';

// Initialize database connection
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Koneksi database gagal");
    }

    // Initialize queries dan controllers
    $queries = new DatabaseQueries($db);
    $eventController = new EventController($db);
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Terjadi kesalahan pada koneksi database. Silakan coba lagi nanti.");
}

// Get the action from URL parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Debug: Log current action
error_log("Current action: " . $action);

// Jika tidak ada action dan user sudah login, redirect ke dashboard sesuai role
if (empty($action) && isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: index.php?action=admin_dashboard");
    } else {
        header("Location: index.php?action=dashboard");
    }
    exit();
}

// Jika tidak ada action dan user belum login, redirect ke login
if (empty($action)) {
    header("Location: index.php?action=login");
    exit();
}

// API Endpoints
if ($action === 'verify_event' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $queries->verifyEvent(
        $_POST['event_id'], 
        $_SESSION['user_id'], 
        $_POST['status'], 
        $_POST['keterangan'] ?? ''
    );
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($action === 'get_users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $users = $queries->getUsers();
    header('Content-Type: application/json');
    echo json_encode($users);
    exit;
}

if ($action === 'add_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $queries->addUser($_POST);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($action === 'update_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $queries->updateUser($_POST);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($action === 'delete_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $queries->deleteUser($_POST['user_id']);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// Authentication check
$public_actions = ['login', 'doLogin', 'logout'];
if (!isset($_SESSION['user_id']) && !in_array($action, $public_actions)) {
    header('Location: index.php?action=login');
    exit;
}

// Debug: Log current action and session
error_log("Current action: " . $action);
error_log("Current session: " . print_r($_SESSION, true));
error_log("POST data: " . print_r($_POST, true));

// Route the request to appropriate controller/action
switch($action) {
    // Auth routes
    case 'login':
        error_log("Entering login case");
        if (isset($_SESSION['user_id'])) {
            error_log("User already logged in, redirecting to dashboard");
            header("Location: index.php?action=" . ($_SESSION['role'] === 'admin' ? 'admin_dashboard' : 'dashboard'));
            exit();
        }
        include 'views/login.php';
        break;

    case 'doLogin':
        error_log("Entering doLogin case");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            error_log("Login attempt for username: " . $username);
            
            try {
                $result = $queries->loginUser($username, $password);
                error_log("Login result: " . print_r($result, true));
                
                if ($result['success']) {
                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $result['role'];
                    
                    error_log("Login successful. Role: " . $result['role']);
                    error_log("Session after login: " . print_r($_SESSION, true));
                    
                    header("Location: index.php?action=" . ($result['role'] === 'admin' ? 'admin_dashboard' : 'dashboard'));
                    exit();
                } else {
                    error_log("Login failed: " . ($result['message'] ?? 'Unknown error'));
                    $_SESSION['error'] = 'Username atau password salah';
                    header("Location: index.php?action=login");
                    exit();
                }
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                $_SESSION['error'] = 'Terjadi kesalahan sistem';
                header("Location: index.php?action=login");
                exit();
            }
        }
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header("Location: index.php?action=login");
        exit();

    case 'admin_dashboard':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }
        include 'views/admin/dashboard.php';
        break;

    case 'dashboard':
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
        require_once 'views/user/dashboard.php';
        break;

    case 'submit_event':
        require_once 'views/user/submit_event.php';
        break;

    case 'my_events':
        require_once 'views/user/my_events.php';
        break;

    case 'view_event':
        require_once 'views/user/view_event.php';
        break;

    case 'submit_event_process':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $eventController->submitEvent($_POST, $_FILES);
        }
        break;

    case 'close_event':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventController->closeEvent($_POST['id']);
        }
        break;

    case 'delete_event':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventController->deleteEvent($_POST['id']);
        }
        break;

    case 'admin_verifications':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }
        $pendingEvents = $queries->getPendingEvents();
        include 'views/admin/verifications.php';
        break;

    case 'manage_users':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }
        $users = $queries->getUsers();
        include 'views/admin/users.php';
        break;

    default:
        header("Location: index.php?action=login");
        exit();
}

if (isset($error)) {
    echo "<script>alert('DEBUG: $error');</script>";
}
?>