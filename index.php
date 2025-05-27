<!-- login.html -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Pengajuan Event Ekskul</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      margin: 0;
      padding: 0;
      background: url('https://smktelekomunikasitelesandi.sch.id/public/src/sekolah.jpeg') no-repeat center center fixed;
      background-size: cover;
    }
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      z-index: 1;
    }
    .card {
      box-shadow: 0 4px 24px rgba(0,0,0,0.18);
      background: rgba(255,255,255,0.95);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="card p-4" style="min-width: 350px;">
      <h3 class="mb-3 text-center">Login</h3>
      <form action="controllers/login.php" method="POST">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Masuk</button>
      </form>
    </div>
  </div>
</body>
</html>