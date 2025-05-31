<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
    </style>
</head>
<body>
    <div class="login-card w-full max-w-md p-8 rounded-xl text-white">
        <div class="text-center mb-8">
            <i class="fas fa-shield-alt text-4xl mb-4"></i>
            <h1 class="text-2xl font-bold">Admin Panel Login</h1>
        </div>

        <form id="loginForm" onsubmit="return handleLogin(event)">
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Username</label>
                <input type="text" name="username" required
                       class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 rounded-lg text-white font-medium transition-colors">
                Login
            </button>
        </form>

        <!-- Notification -->
        <div id="notification" class="fixed top-4 right-4 z-50 hidden">
            <div class="px-4 py-3 rounded-lg shadow-lg min-w-[300px]">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>
                    <p class="notification-message"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const messageElement = notification.querySelector('.notification-message');
            
            notification.classList.remove('hidden', 'bg-green-500', 'bg-red-500');
            notification.classList.add(type === 'success' ? 'bg-green-500' : 'bg-red-500');
            messageElement.textContent = message;
            
            notification.classList.remove('hidden');
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        async function handleLogin(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('index.php?action=doLogin', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Login berhasil, mengalihkan...', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php?action=admin_users';
                    }, 1000);
                } else {
                    throw new Error(result.message || 'Login gagal');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification(error.message || 'Login gagal, silakan coba lagi', 'error');
            }
            
            return false;
        }
    </script>
</body>
</html> 