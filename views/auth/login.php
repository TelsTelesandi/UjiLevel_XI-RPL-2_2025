<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Event Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="index.php?action=doLogin">
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Username</label>
                <input type="text" name="username" class="w-full border px-3 py-2 rounded" required>
            </div>
            <div class="mb-6">
                <label class="block mb-1 font-semibold">Password</label>
                <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
        </form>
    </div>
</body>
</html> 