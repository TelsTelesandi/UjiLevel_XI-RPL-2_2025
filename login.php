<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Sistem Event</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-rose-100 via-rose-200 to-pink-200 min-h-screen flex items-center justify-center">
  <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl w-96 border border-rose-200">
    <div class="text-center mb-8">
      <h1 class="text-2xl font-bold text-rose-600">Sistem Event</h1>
      <p class="text-rose-400">Manajemen Event Ekstrakurikuler</p>
    </div>
    
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-rose-600 text-sm font-medium mb-2">Username</label>
        <input type="text" name="username" required
          class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white/50">
      </div>
      
      <div>
        <label class="block text-rose-600 text-sm font-medium mb-2">Password</label>
        <input type="password" name="password" required
          class="w-full px-4 py-2 rounded-lg border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white/50">
      </div>

      <button name="login" type="submit"
        class="w-full py-2 px-4 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-lg transition duration-200">
        Login
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-rose-500">
      Belum punya akun? 
      <a href="register.php" class="font-medium text-rose-600 hover:text-rose-700">Register</a>
    </p>
  </div>

  <?php
  if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = $conn->query("SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    if ($query->num_rows > 0) {
      $user = $query->fetch_assoc();
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
      
      header("Location: " . ($user['role'] == 'admin' ? 'dashboard_admin.php' : 'dashboard_user.php'));
    } else {
      echo "<div class='fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>
              Username atau Password salah!
            </div>";
    }
  }
  ?>
</body>
</html>
  