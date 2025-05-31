<?php
class AuthController {
    private $conn;
    private $userModel;
    private $basePath;

    public function __construct($conn) {
        // Initialize session securely
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 86400,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }

        $this->conn = $conn;
        $this->basePath = realpath(__DIR__ . '/..');
        
        require_once $this->basePath . '/models/UserModel.php';
        $this->userModel = new UserModel($conn);
    }

    public function showLogin() {
        // Redirect jika sudah login
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
            return;
        }
        
        require_once $this->basePath . '/views/auth/login.php';
    }

    public function showRegister() {
        // Redirect jika sudah login
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
            return;
        }
        
        require_once $this->basePath . '/views/auth/register.php';
    }

    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=login");
            exit();
        }

        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Username dan password harus diisi!";
            header("Location: index.php?action=login");
            exit();
        }

        try {
            $user = $this->userModel->getUserByUsername($username);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                
                if ($user['role'] === 'admin') {
                    header("Location: index.php?action=admin_dashboard");
                } else {
                    header("Location: index.php?action=dashboard");
                }
                exit();
            } else {
                $_SESSION['error'] = 'Username atau password salah';
                header("Location: index.php?action=login");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Terjadi kesalahan saat login.";
            header("Location: index.php?action=login");
            exit();
        }
    }

    public function doRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=register");
            exit();
        }

        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
        $nama_lengkap = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_STRING);
        $ekskul = filter_input(INPUT_POST, 'ekskul', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validasi input
        if (empty($username) || empty($nama_lengkap) || empty($ekskul) || empty($password) || empty($confirm_password)) {
            $_SESSION['error'] = "Semua field harus diisi!";
            header("Location: index.php?action=register");
            exit();
        }

        // Validasi email
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Format email tidak valid!";
            header("Location: index.php?action=register");
            exit();
        }

        // Validasi password match
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Password dan konfirmasi password tidak cocok!";
            header("Location: index.php?action=register");
            exit();
        }

        try {
            // Cek apakah username sudah ada
            if ($this->userModel->userExists($username)) {
                $_SESSION['error'] = "Email sudah terdaftar!";
                header("Location: index.php?action=register");
                exit();
            }

            // Create new user
            if ($this->userModel->createUser($username, $password, $nama_lengkap, $ekskul)) {
                $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
                header("Location: index.php?action=login");
            } else {
                $_SESSION['error'] = "Gagal membuat user baru.";
                header("Location: index.php?action=register");
            }
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Terjadi kesalahan saat registrasi.";
            header("Location: index.php?action=register");
            exit();
        }
    }

    public function doLogout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }

    private function redirect($page, $params = []) {
        $url = "index.php?action=" . $page;
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= "&" . urlencode($key) . "=" . urlencode($value);
            }
        }
        header("Location: " . $url);
        exit();
    }

    private function redirectBasedOnRole() {
        if ($_SESSION['role'] === 'admin') {
            header("Location: index.php?action=admin_dashboard");
        } else {
            header("Location: index.php?action=dashboard");
        }
        exit();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Username dan password harus diisi!";
            header("Location: index.php?action=login");
            exit();
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Set session data
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: index.php?action=admin_dashboard");
                } else {
                    header("Location: index.php?action=dashboard");
                }
                exit();
            } else {
                $_SESSION['error'] = "Username atau password salah!";
                header("Location: index.php?action=login");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat login.";
            header("Location: index.php?action=login");
            exit();
        }
    }

    public function logout() {
        // Destroy session
        session_unset();
        session_destroy();
        
        // Start new session for flash message
        session_start();
        $_SESSION['success'] = "Anda telah berhasil logout.";
        
        // Redirect to login
        header("Location: index.php?action=login");
        exit();
    }
}
?>