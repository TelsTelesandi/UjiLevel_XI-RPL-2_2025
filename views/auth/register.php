<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Event Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                    },
                },
            },
        }
    </script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center p-6">
    <div class="w-full max-w-md animate-fade-in">
        <!-- Register Card -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8 animate-slide-up">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
                <p class="text-gray-600">Register untuk mengakses sistem</p>
            </div>

            <!-- Register Form -->
            <form action="index.php?action=doRegister" method="POST" class="space-y-4">
                <!-- Username/Email Input -->
                <div class="space-y-2">
                    <label for="username" class="text-sm font-medium text-gray-700 block">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="username" name="username" required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                               placeholder="Enter your email">
                    </div>
                </div>

                <!-- Nama Lengkap Input -->
                <div class="space-y-2">
                    <label for="nama_lengkap" class="text-sm font-medium text-gray-700 block">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                               placeholder="Enter your full name">
                    </div>
                </div>

                <!-- Ekskul Input -->
                <div class="space-y-2">
                    <label for="ekskul" class="text-sm font-medium text-gray-700 block">Ekskul</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-users"></i>
                        </span>
                        <input type="text" id="ekskul" name="ekskul" required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                               placeholder="Enter your extracurricular">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-gray-700 block">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                               placeholder="Enter your password">
                    </div>
                </div>

                <!-- Confirm Password Input -->
                <div class="space-y-2">
                    <label for="confirm_password" class="text-sm font-medium text-gray-700 block">Konfirmasi Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                               placeholder="Confirm your password">
                    </div>
                </div>

                <!-- Error Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                        <p class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span><?= htmlspecialchars($_SESSION['error']) ?></span>
                        </p>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Success Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                        <p class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
                        </p>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2 px-4 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Register</span>
                </button>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="index.php?action=login" class="text-blue-600 hover:text-blue-700 font-medium">Login disini</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-white">
            <p>&copy; <?= date('Y') ?> Event Management System. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Add loading state to form on submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const originalContent = button.innerHTML;
            
            button.disabled = true;
            button.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-circle-notch fa-spin"></i>
                    <span>Processing...</span>
                </div>
            `;

            // If form takes too long to submit, restore button state after 5 seconds
            setTimeout(() => {
                if (button.disabled) {
                    button.disabled = false;
                    button.innerHTML = originalContent;
                }
            }, 5000);
        });
    </script>
</body>
</html> 