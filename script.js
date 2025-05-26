document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  const currentPath = window.location.pathname.split('/').pop(); // hanya nama file
  const loggedUser = JSON.parse(localStorage.getItem('loggedUser'));

  // === LOGIN HANDLER ===
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const username = e.target.username.value.trim();
      const password = e.target.password.value.trim();

const users = JSON.parse(localStorage.getItem('users')) || [];
      const found = users.find(u => u.username === username && u.password === password);

      if (found) {
        localStorage.setItem('loggedUser', JSON.stringify(found));
        window.location.href = found.role === 'admin' ? 'AdminDashboard.html' : 'UserDashboard.html';
      } else {
        document.getElementById('loginError').textContent = 'Username atau password salah!';
      }
    });
  }

  // === ROUTING GUARD ===
  const isDashboardUser = currentPath === 'UserDashboard.html';
  const isDashboardAdmin = currentPath === 'AdminDashboard.html';

  if ((isDashboardUser || isDashboardAdmin) && !loggedUser) {
    alert("Silakan login terlebih dahulu.");
    window.location.href = 'index.html';
  }

  if (loggedUser) {
    if (isDashboardUser && loggedUser.role !== 'user') {
      alert("Akses ditolak. Anda bukan user.");
      window.location.href = 'index.html';
    }

    if (isDashboardAdmin && loggedUser.role !== 'admin') {
      alert("Akses ditolak. Anda bukan admin.");
      window.location.href = 'index.html';
    }
  }
});
