    <?php
    class LoginController {
        private $conn;

        public function __construct() {
            // Include file config
            include 'database/config.php';
            
            try {
                $this->conn = new PDO(
                    "mysql:host=$db_host;dbname=$db_name", 
                    $db_user, 
                    $db_pass
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage());
            }
        }

        public function showLoginPage() {
            session_start();
            $error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
            unset($_SESSION['login_error']);
            
            include 'views/login.php';
        }

        public function processLogin() {
            session_start();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $_SESSION['login_error'] = 'Metode request tidak valid';
                header('Location: login.php');
                exit;
            }

            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validasi input
            if (empty($username) || empty($password)) {
                $_SESSION['login_error'] = 'Username dan password harus diisi';
                header('Location: login.php');
                exit;
            }

            // Autentikasi user
            $user = $this->authenticateUser($username, $password);

            if ($user) {
                // Login berhasil
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['logged_in'] = true;
                
                header('Location: dashboard.php');
                exit;
            } else {
                // Login gagal
                $_SESSION['login_error'] = 'Username atau password salah';
                header('Location: login.php');
                exit;
            }
        }

        private function authenticateUser($username, $password) {
            try {
                $stmt = $this->conn->prepare("SELECT id, username, password FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($password, $user['password'])) {
                    return $user;
                }
                
                return false;
            } catch(PDOException $e) {
                error_log("Error autentikasi: " . $e->getMessage());
                return false;
            }
        }

        public function logout() {
            session_start();
            session_unset();
            session_destroy();
            header('Location: login.php');
            exit;
        }
    }

    // Routing sederhana
    if (file_exists('database/config.php')) {
        $controller = new LoginController();

        $action = $_GET['action'] ?? $_POST['action'] ?? 'view';

        switch ($action) {
            case 'login':
                $controller->processLogin();
                break;
            case 'logout':
                $controller->logout();
                break;
            default:
                $controller->showLoginPage();
        }
    } else {
        die("File konfigurasi database tidak ditemukan!");
    }
    ?>